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
		$from  = $queryBuilderId->getDQLPart('from');
		$class = $from[0]->getFrom();
		$metadata = $queryBuilderId->getEntityManager()->getMetadataFactory()->getMetadataFor($class);

		// step 2 : retrieve identifier columns
		$idNames = $metadata->getIdentifierFieldNames();

		// step 3 : retrieve the different subjects ids
		$selects = array();
		$idxSelect = '';
		foreach ($idNames as $idName) {
			$select = sprintf('%s.%s', $queryBuilderId->getRootAlias(), $idName);
			// Put the ID select on this array to use it on results QB
			$selects[$idName] = $select;
			// Use IDENTITY if id is a relation too. See: http://doctrine-orm.readthedocs.org/en/latest/reference/dql-doctrine-query-language.html
			// Should work only with doctrine/orm: ~2.2
			$idSelect = $select;
			if ($metadata->hasAssociation($idName)) {
				$idSelect = sprintf('IDENTITY(%s) as %s', $idSelect, $idName);
			}
			$idxSelect .= ($idxSelect !== '' ? ', ' : '') . $idSelect;
		}
		// Cogitoweb: also reset 'orderBy' part to avoid DBMS errors
	//        $queryBuilderId->resetDQLPart('select');
		$queryBuilderId->resetDQLParts(['select', 'orderBy']);
		$queryBuilderId->add('select', 'DISTINCT '.$idxSelect);

		// for SELECT DISTINCT, ORDER BY expressions must appear in idxSelect list
		/* Consider
			SELECT DISTINCT x FROM tab ORDER BY y;
		For any particular x-value in the table there might be many different y
		values.  Which one will you use to sort that x-value in the output?
		*/
		// todo : check how doctrine behave, potential SQL injection here ...
		if ($this->getSortBy()) {
			$sortBy = $this->getSortBy();
			if (strpos($sortBy, '.') === false) { // add the current alias
				$sortBy = $queryBuilderId->getRootAlias() . '.' . $sortBy;
			}
			$sortBy .= ' AS __order_by';
			$queryBuilderId->addSelect($sortBy);
		}

		$results    = $queryBuilderId->getQuery()->execute(array(), Query::HYDRATE_ARRAY);
		$idxMatrix  = array();
		foreach ($results as $id) {
			foreach ($idNames as $idName) {
				$idxMatrix[$idName][] = $id[$idName];
			}
		}

		// step 4 : alter the query to match the targeted ids
		foreach ($idxMatrix as $idName => $idx) {
			if (count($idx) > 0) {
				$idxParamName = sprintf('%s_idx', $idName);
				$queryBuilder->andWhere(sprintf('%s IN (:%s)', $selects[$idName], $idxParamName));
				$queryBuilder->setParameter($idxParamName, $idx);
				$queryBuilder->setMaxResults(null);
				$queryBuilder->setFirstResult(null);
			}
		}

		return $queryBuilder;
	}
}
