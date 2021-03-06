<?php

namespace Cogitoweb\TemplateBundle\Core;

/**
 * Stores Messages on the filesystem.
 *
 * @package Swift
 * @author  Fabien Potencier
 * @author  Xavier De Cock <xdecock@gmail.com>
 */
class SmartfileSpool extends \Swift_ConfigurableSpool
{
	/**
	 * The spool directory
	 */
	private $_path;
	private $_sent_path;
	private $_err_path;

	/**
	 * File WriteRetry Limit
	 *
	 * @var int
	 */
	private $_retryLimit = 10;

	/**
	 * Create a new FileSpool.
	 *
	 * @param string $path
	 * @param string $sent dir
	 * @param string $err dir
	 *
	 * @throws Swift_IoException
	 */
	public function __construct($path, $sent, $err)
	{
		$this->_path      = $path;
		$this->_sent_path = $sent;
		$this->_err_path  = $err;

		if (!file_exists($this->_path)) {
			if (!mkdir($this->_path, 0777, true)) {
				throw new \Swift_IoException('Unable to create Path ['.$this->_path.']');
			}
		}

		if (!file_exists($this->_sent_path)) {
			if (!mkdir($this->_sent_path, 0777, true)) {
				throw new \Swift_IoException('Unable to create send Path ['.$this->_sent_path.']');
			}
		}

		if (!file_exists($this->_err_path)) {
			if (!mkdir($this->_err_path, 0777, true)) {
				throw new \Swift_IoException('Unable to create err Path ['.$this->_err_path.']');
			}
		}
	}

	/**
	 * Tests if this Spool mechanism has started.
	 *
	 * @return boolean
	 */
	public function isStarted()
	{
		return true;
	}

	/**
	 * Starts this Spool mechanism.
	 */
	public function start()
	{
	}

	/**
	 * Stops this Spool mechanism.
	 */
	public function stop()
	{
	}

	/**
	 * Allow to manage the enqueuing retry limit.
	 *
	 * Default, is ten and allows over 64^20 different fileNames
	 *
	 * @param integer $limit
	 */
	public function setRetryLimit($limit)
	{
		$this->_retryLimit = $limit;
	}

	/**
	 * Queues a message.
	 *
	 * @param Swift_Mime_Message $message The message to store
	 *
	 * @return boolean
	 *
	 * @throws Swift_IoException
	 */
	public function queueMessage(\Swift_Mime_Message $message)
	{
		$ser = serialize($message);
		$fileName = $this->_path . '/' . $this->getRandomString(10);
		for ($i = 0; $i < $this->_retryLimit; ++$i) {
			/* We try an exclusive creation of the file. This is an atomic operation, it avoid locking mechanism */
			$fp = @fopen($fileName . '.message', 'x');
			if (false !== $fp) {
				if (false === fwrite($fp, $ser)) {
					return false;
				}

				return fclose($fp);
			} else {
				/* The file already exists, we try a longer fileName */
				$fileName .= $this->getRandomString(1);
			}
		}

		throw new \Swift_IoException('Unable to create a file for enqueuing Message');
	}

	/**
	 * Execute a recovery if for any reason a process is sending for too long.
	 *
	 * @param integer $timeout in second Defaults is for very slow smtp responses
	 */
	public function recover($timeout = 900)
	{
		foreach (new DirectoryIterator($this->_path) as $file) {
			$file = $file->getRealPath();

			if (substr($file, - 16) == '.message.sending') {
				$lockedtime = filectime($file);
				if ((time() - $lockedtime) > $timeout) {
					rename($file, substr($file, 0, - 8));
				}
			}
		}
	}

	/**
	 * Sends messages using the given transport instance.
	 *
	 * @param Swift_Transport $transport        A transport instance
	 * @param string[]        $failedRecipients An array of failures by-reference
	 *
	 * @return integer The number of sent e-mail's
	 */
	public function flushQueue(\Swift_Transport $transport, &$failedRecipients = null)
	{
//		$logger = new \Swift_Plugins_Loggers_ArrayLogger(); // Use EchoLogger lo log everything
		$logger = new \Swift_Plugins_Loggers_EchoLogger();
		$transport->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

		$directoryIterator = new \DirectoryIterator($this->_path);

		/* Start the transport only if there are queued files to send */
		if (!$transport->isStarted()) {
			foreach ($directoryIterator as $file) {
				if (substr($file->getRealPath(), -8) == '.message') {
					$transport->start();
					break;
				}
			}
		}

		$failedRecipients = (array) $failedRecipients;
		$count = 0;
		$time = time();
		foreach ($directoryIterator as $file) {
			$file = $file->getRealPath();

			if (substr($file, -8) != '.message') {
				continue;
			}

			/* We try a rename, it's an atomic operation, and avoid locking the file */
			if (rename($file, $file.'.sending')) {
				$message = unserialize(file_get_contents($file.'.sending'));

				try
				{
					$count += $transport->send($message, $failedRecipients);
				}
				catch(\Exception $ex) {

					if(filectime($file . '.sending') > 300) // dopo 5 min sposto in errore
					{
						$new_file = str_replace($this->_path, $this->_err_path, $file).'.'.time().'.err';

						rename($file.'.sending', $new_file);
						file_put_contents($new_file.'.ex', $ex);
						continue;
					}
				}

				//unlink($file.'.sending');
				$new_file = str_replace($this->_path, $this->_sent_path, $file).'.'.time().'.sent';
				rename($file.'.sending', $new_file);
			} else {
				/* This message has just been catched by another process */
				continue;
			}

			if ($this->getMessageLimit() && $count >= $this->getMessageLimit()) {
				break;
			}

			if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit()) {
				break;
			}
		}

		return $count;
	}

	/**
	 * Returns a random string needed to generate a fileName for the queue.
	 *
	 * @param integer $count
	 *
	 * @return string
	 */
	protected function getRandomString($count)
	{
		// This string MUST stay FS safe, avoid special chars
		$base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.";
		$ret = '';
		$strlen = strlen($base);
		for ($i = 0; $i < $count; ++$i) {
			$ret .= $base[((int) rand(0, $strlen - 1))];
		}

		return $ret;
	}
}