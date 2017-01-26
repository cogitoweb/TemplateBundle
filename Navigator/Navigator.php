<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Navigator;

use Sonata\AdminBundle\Admin\AdminInterface;

/**
 * Description of Navigator
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class Navigator
{
	/**
	 * Admin
	 * 
	 * @var AdminInterface
	 */
	protected $admin;

	/**
	 * Set admin
	 * 
	 * @param AdminInterface $admin
	 */
	public function setAdmin(AdminInterface $admin)
	{
		$this->admin = $admin;

		return $this;
	}

	/**
	 * Get admin
	 * 
	 * @return AdminInterface
	 */
	public function getAdmin()
	{
		return $this->admin;
	}

	/**
	 * Is available
	 * 
	 * @return boolean
	 */
	public function isAvailable()
	{
		return (boolean) $this->getCurrent();
	}

	/**
	 * Get previous object
	 * 
	 * @return mixed
	 */
	public function getPrevious()
	{
		$results = $this->getAdmin()->getDatagrid()->getResults();

		// Get 0-index position
		$position = $this->getCurrent() - 1;

		// Previous position
		$position --;

		return array_key_exists($position, $results) ? $results[$position] : null;
	}

	/**
	 * Get current object
	 * 
	 * @return integer
	 */
	public function getCurrent()
	{
		$position = array_search(
			$this->getAdmin()->getSubject(),
			$this->getAdmin()->getDatagrid()->getResults()
		);

		if (false === $position) {
			return null;
		}

		// Return 1-index position
		return $position + 1;
	}

	/**
	 * Count objects
	 * 
	 * @return integer
	 */
	public function count()
	{
		return count($this->getAdmin()->getDatagrid()->getResults());
	}

	/**
	 * Get next object
	 * 
	 * @return mixed
	 */
	public function getNext()
	{
		$results  = $this->getAdmin()->getDatagrid()->getResults();

		// Get 0-index position
		$position = $this->getCurrent() - 1;

		// Next position
		$position ++;

		return array_key_exists($position, $results) ? $results[$position] : null;
	}
}