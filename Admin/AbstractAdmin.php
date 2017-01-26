<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Cogitoweb\MultiLevelAdminBundle\Admin\AbstractAdmin as CogitowebAbstractAdmin;
use Cogitoweb\TemplateBundle\Datagrid\ProxyQuery;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Description of AbstractAdmin
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class AbstractAdmin extends CogitowebAbstractAdmin
{
	/**
	 * Constants used in {@see getCollectionIds()}
	 * 
	 * @const string
	 */
	const PHP_ARRAY_FORMAT      = 'php';
	const POSTGRES_ARRAY_FORMAT = 'postgres';

	/**
	 * Fields to show in added object informations
	 * 
	 * @var string[]
	 */
	protected $objectInformationFields = ['id', 'createdBy', 'updatedBy', 'createdAt', 'updatedAt'];

	/**
	 * {@inheritdoc}
	 */
	public function buildDatagrid()
	{
		parent::buildDatagrid();

		// Handle add query results with method {@see addQueryResults()}
		if (method_exists($this->datagrid, 'setAddQueryResults')) {
			$this->datagrid->setAddQueryResults($this->addQueryResults());
		}
	}

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
	 * {@inheritdoc}
	 */
	public function getBatchActions()
	{
		// Disable batch actions
		return [];
	}

	/**
	 * Get collection id
	 * 
	 * @static
	 * 
	 * @param array|Iterable $collection
	 * @param string         $format
	 * 
	 * @return integer[]
	 */
	public static function getCollectionIdsStatic($collection, $format = self::PHP_ARRAY_FORMAT)
	{
		$ids = [];

		foreach ($collection as $item) {
			switch (true) {
				case is_object($item):
					$ids[] = $item->getId();
					break;
				case is_array($item):
					if (array_key_exists('id', $item)) {
						$ids[] = $item['id'];
					}
					break;
				default:
				
			}
		}

		switch ($format) {
			case self::POSTGRES_ARRAY_FORMAT:
				return sprintf('{%s}', implode(',', $ids));
			case self::PHP_ARRAY_FORMAT:
				return $ids;
			default:
				
		}
	}

	/**
	 * Get collection ids
	 * 
	 * @param array|Iterable $collection
	 * @param string         $format
	 * 
	 * @return integer[]
	 */  
	public function getCollectionIds($collection, $format = self::PHP_ARRAY_FORMAT)
	{
		return self::getCollectionIdsStatic($collection, $format);
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
	 * Is underlying object new?
	 * 
	 * @return boolean
	 */
	public function isNew()
	{
		return $this->getRoot()->getSubject() && !$this->getRoot()->getSubject()->getId();
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
	protected function addObjectInformations(ShowMapper $showMapper, $tabName = null, array $tabOptions = [], $withName = 'Informazioni aggiuntive', array $withOptions = [], array $objectInformationFields = ['id', 'createdBy', 'updatedBy', 'createdAt', 'updatedAt'])
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
	 * Inject custom values to retrieved objects
	 * 
	 * @return closure Method must return a closure with the objects to be used in the Datagrid
	 */
	public function addQueryResults() {}

	/**
	 * {@inheritdoc}
	 */
	protected function configureRoutes(RouteCollection $collection)
	{
		parent::configureRoutes($collection);

		// Disable batch and export routes
		$collection
			->remove('batch')
			->remove('export')
		;
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

	/**
	 * Get Navigator service
	 * 
	 * @return Navigator
	 */
	public function getNavigator()
	{
		$navigator = $this->getConfigurationPool()->getContainer()->get('cogitoweb.navigator');

		$navigator->setDatagrid($this->getDatagrid());

		return $navigator;
	}
}