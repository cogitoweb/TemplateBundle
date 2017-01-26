<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Navigator;

use Sonata\AdminBundle\Datagrid\DatagridInterface;

/**
 * Description of Navigator
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class Navigator
{
	/**
	 * Datagrid
	 * 
	 * @var DatagridInterface
	 */
	protected $datagrid;

	protected $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Set datagrid
	 * @param DatagridInterface $datagrid
	 */
	public function setDatagrid(DatagridInterface $datagrid)
	{
		$this->datagrid = $datagrid;

		return $this;
	}

	/**
	 * Get datagrid
	 * 
	 * @return DatagridInterface
	 */
	public function getDatagrid()
	{
		return $this->datagrid;
	}

	public function getPrevious() {
		return $this->entityManager->find(\Application\LavAdr\CRUDBundle\Entity\Contratto::class, 25);
	}

	public function getCurrent() {
		return $this->entityManager->find(\Application\LavAdr\CRUDBundle\Entity\Contratto::class, 26);
	}

	public function getNext() {
		return $this->entityManager->find(\Application\LavAdr\CRUDBundle\Entity\Contratto::class, 27);
	}

	/**
	 * Count
	 * 
	 * @return integer
	 */
	public function count() {
		return count($this->getDatagrid()->getResults());
	}
}