<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Cogitoweb\TemplateBundle\Datagrid\ProxyQuery;
use Cogitoweb\MultiLevelAdminBundle\Admin\AbstractAdmin as CogitowebAbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * Description of AbstractAdmin
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class AbstractAdmin extends CogitowebAbstractAdmin
{
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
}