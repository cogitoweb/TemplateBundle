<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Cogitoweb\MultiLevelAdminBundle\Admin\AbstractAdmin as CogitowebAbstractAdmin;
use Cogitoweb\TemplateBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Description of AbstractAdmin
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class AbstractAdmin extends CogitowebAbstractAdmin
{
	/**
	 * Fields to show in added object informations
	 * 
	 * @var string[]
	 */
	protected $objectInformationFields = ['id', 'createdBy', 'updatedBy', 'createdAt', 'updatedAt'];

	/**
	 * Overridden method to use Cogitoweb's ProxyQuery.
	 * See {@see Cogitoweb\TemplateBundle\Datagrid\ProxyQuery} for further informations.
	 * 
	 * @param  string $context
	 * 
	 * @return ProxyQueryInterface
	 */
	public function createQuery($context = 'list')
	{
		$originalProxyQuery  = parent::createQuery($context);
		$queryBuilder        = $originalProxyQuery->getQueryBuilder();
		$cogitowebProxyQuery = new ProxyQuery($queryBuilder);

		return $cogitowebProxyQuery;
	}

	/**
	 * Is underlying object new?
	 * 
	 * @return boolean
	 */
	public function isNew()
	{
		return $this->getRoot()->getSubject() && $this->getRoot()->getSubject()->getId();
	}

	/**
	 * Get service
	 * 
	 * @param  string $service
	 * 
	 * @return mixed
	 */
	public function getService($service)
	{
		return $this->getConfigurationPool()->getContainer()->get($service);
	}

	

	/**
	 * Append object informations, if available, to current ShowMapper:
	 * - id;
	 * - createdBy;
	 * - updatedBy;
	 * - createdAt;
	 * - updatedAt;
	 * 
	 * @param ShowMapper $showMapper
	 * @param string     $tabName
	 * @param string[]   $tabOptions
	 * @param string     $withName
	 * @param string[]   $withOptions
	 * @param string[]   $objectInformationFields
	 */
	public function addObjectInformations(ShowMapper $showMapper, $tabName = null, array $tabOptions = [], $withName = 'label.object_informations', array $withOptions = [], array $objectInformationFields = ['id', 'createdBy', 'updatedBy', 'createdAt', 'updatedAt'])
	{
		$entityManager = $this->getEntityManager();
		$className     = $this->getClass();
		$classMetadata = $entityManager->getClassMetadata($className);
		$fieldNames    = $classMetadata->getFieldNames();

		// Check if model has at least one element to show
		if (empty(array_intersect($objectInformationFields, $fieldNames))) {
			return;
		}

		// Open tab
		if (null !== $tabName) {
			// Check if it's necessary to close previous tab
			if ($showMapper->hasOpenTab()) {
				$showMapper->end();
			}

			$showMapper->tab($tabName, $tabOptions);
		}

		// Open group
		if (null !== $withName) {
			$showMapper->with($withName, $withOptions);
		}

		// Add fields
		foreach ($objectInformationFields as $objectInformationField) {
			if (in_array($objectInformationField, $fieldNames)) {
				$showMapper->add($objectInformationField);
			}
		}

		// Close group
		if (null !== $withName) {
			$showMapper->end();
		}

		// Close tab
		if (null !== $tabName) {
			$showMapper->end();
		}
	}

	/**
	 * Get EntityManager
	 * 
	 * @return EntityManagerInterface
	 */
	protected function getEntityManager()
	{
		return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
	}
}