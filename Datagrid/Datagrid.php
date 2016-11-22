<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Datagrid;

use Cogitoweb\TemplateBundle\Admin\AbstractAdmin;
use Doctrine\Common\Inflector\Inflector;
use Sonata\AdminBundle\Datagrid\Datagrid as SonataDatagrid;

/**
 * Description of Datagrid
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class Datagrid extends SonataDatagrid
{
	/**
	 * {@see AbstractAdmin::addQueryResults()} 
	 * 
	 * @var \Closure
	 */
	protected $addQueryResults;

	/**
	 * Set add query results
	 * 
	 * @param \Closure|null $closure
	 * 
	 * @return Datagrid
	 */
	public function setAddQueryResults(\Closure $addQueryResults = null)
	{
		$this->addQueryResults = $addQueryResults;

		return $this;
	}

	/**
	 * Get add query results
	 * 
	 * @return \Closure
	 */
	public function getAddQueryResults()
	{
		return $this->addQueryResults;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResults()
	{
		$results = parent::getResults();

		if ($closure = $this->getAddQueryResults()) {
			// Exec closure and retrieve data to append
			$addResults = $closure($results);

			/*
			 * For each result to add, check if it matches with an item retrieved from the Datagrid
			 * and set its properties in case.
			 */
			foreach ($addResults as $addResult) {
				foreach ($results as $result) {
					if (
						   array_key_exists('id', $addResult)
						&& method_exists   ($result, 'getId')
						&& $addResult['id'] === $result->getId()
					) {
						foreach ($addResult as $key => $value) {
							$method = Inflector::camelize("set_$key");

							if (method_exists($result, $method)) {
								$result->$method($value);
							}
						}
					}
				}
			}
		}

		return $results;
	}
}
