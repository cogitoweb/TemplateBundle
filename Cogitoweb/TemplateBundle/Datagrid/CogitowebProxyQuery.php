<?php

namespace Cogitoweb\TemplateBundle\Datagrid;

use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Extended ProxyQuery to remove "orderBy" DQL part from pager counter
 * without forking SonataDoctrineORMAdminBundle.
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class CogitowebProxyQuery extends ProxyQuery
{
	/**
	 * {@inheritdoc}
	 */
	public function execute(array $params = array(), $hydrationMode = null)
	{
		// always clone the original queryBuilder
		$queryBuilder = clone $this->queryBuilder;

		// todo : check how doctrine behave, potential SQL injection here ...
		if ($this->getSortBy()) {
			$sortBy = $this->getSortBy();
			if (strpos($sortBy, '.') === false) { // add the current alias
				$sortBy = $queryBuilder->getRootAlias() . '.' . $sortBy;
			}
			$queryBuilder->addOrderBy($sortBy, $this->getSortOrder());
		} else {
			$queryBuilder->resetDQLPart( 'orderBy' );
		}

		return $this->getFixedQueryBuilder($queryBuilder)->getQuery()->execute($params, $hydrationMode);
	}

	/**
	 * {@inheritdoc}
	 */
	private function getFixedQueryBuilder(QueryBuilder $queryBuilder)
	{
		$queryBuilderId = clone $queryBuilder;
        // step 1 : retrieve the targeted class
        $from = $queryBuilderId->getDQLPart('from');
        $class = $from[0]->getFrom();
        // step 2 : retrieve the column id
        $idName = current($queryBuilderId->getEntityManager()->getMetadataFactory()->getMetadataFor($class)->getIdentifierFieldNames());
        // step 3 : retrieve the different subjects id
        $rootAliases = $queryBuilderId->getRootAliases();
        $rootAlias = $rootAliases[0];
        $select = sprintf('%s.%s', $rootAlias, $idName);
        $queryBuilderId->resetDQLPart('select');
        $queryBuilderId->add('select', 'DISTINCT '.$select);
        $queryBuilderId = $this->preserveSqlOrdering($queryBuilderId);
        $results = $queryBuilderId->getQuery()->execute(array(), Query::HYDRATE_ARRAY);
        $idx = array();
        $connection = $queryBuilder->getEntityManager()->getConnection();
        foreach ($results as $id) {
            $idx[] = $connection->quote($id[$idName]);
        }
        // step 4 : alter the query to match the targeted ids
        if (count($idx) > 0) {
            $queryBuilder->andWhere(sprintf('%s IN (%s)', $select, implode(',', $idx)));
            $queryBuilder->setMaxResults(null);
            $queryBuilder->setFirstResult(null);
        }
        return $queryBuilder;
	}

	/**
	 * Generates new QueryBuilder for Postgresql or Oracle if necessary.
	 *
	 * @param $queryBuilder QueryBuilder
	 *
	 * @return QueryBuilder
	 */
	public function preserveSqlOrdering(QueryBuilder $queryBuilder)
	{
		$rootAliases = $queryBuilder->getRootAliases();
		$rootAlias = $rootAliases[0];
		// for SELECT DISTINCT, ORDER BY expressions must appear in select list
		// Consider SELECT DISTINCT x FROM tab ORDER BY y;
		// For any particular x-value in the table there might be many different y
		// values. Which one will you use to sort that x-value in the output?
		// todo : check how doctrine behave, potential SQL injection here ...
		if ($this->getSortBy()) {
			$sortBy = $this->getSortBy();
			if (strpos($sortBy, '.') === false) {
				// add the current alias
				$sortBy = $rootAlias.'.'.$sortBy;
			}
			$sortBy .= ' AS __order_by';
			$queryBuilder->addSelect($sortBy);
		}
		// For any orderBy clause defined directly in the dqlParts
		$dqlParts = $queryBuilder->getDqlParts();
		if ($dqlParts['orderBy'] && count($dqlParts['orderBy'])) {
			$sqlOrderColumns = array();
			foreach ($dqlParts['orderBy'] as $part) {
				foreach ($part->getParts() as $orderBy) {
					$sqlOrderColumns[] = preg_replace("/\s+(ASC|DESC)$/i", '', $orderBy);
				}
			}
			$queryBuilder->addSelect(implode(', ', $sqlOrderColumns));
		}
		return $queryBuilder;
	}
}