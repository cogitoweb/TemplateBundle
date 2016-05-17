<?php

namespace Cogitoweb\TemplateBundle\Entity;

use JMS\Serializer\Annotation\Exclude;

/**
 * Description of BaseTranslatedEntity
 *
 * @author 2z
 */
class BaseTranslatedEntity extends BaseEntity
{
	/**
	 * 
	 * @var string
	 * 
	 * @Exclude
	 */
	protected $myDefaultLocale = 'it';

	/**
	 * To String
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if (!$this->getId()) {
			return '-';
		}

		if ($this->getName()) {
			return $this->getName();
		} else {
			if ($this->translate($this->getMyDefaultLocale())->getName()) {
				return $this->translate($this->getMyDefaultLocale())->getName();
			} else {
				return (string) $this->getId();
			}
		}
	}  

	/**
	 * Get myDefaultLocale
	 * 
	 * @return string
	 */
	public function getMyDefaultLocale()
	{
	  return $this->myDefaultLocale;
	}

	/**
	 * Set currentLocaleFluid
	 * 2z -> fluid interface
	 * 
	 * @param mixed $locale The current locale
	 */
	public function setCurrentLocaleFuild($locale)
	{
		$this->currentLocale = $locale;

		return $this;
	}
}