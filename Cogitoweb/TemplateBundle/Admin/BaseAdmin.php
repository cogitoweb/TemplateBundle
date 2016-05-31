<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Cogitoweb\TemplateBundle\Datagrid\CogitowebProxyQuery;
use Sonata\AdminBundle\Admin\CogitowebAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * Description of BaseAdmin
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 * 
 * @deprecated since version 2.3. This class is kept for backward compatibility only. Use {@link Sonata\AdminBundle\Admin\CogitowebAdmin} instead.
 */
class BaseAdmin extends CogitowebAdmin
{
	/**
	 * Overridden method to use CogitowebProxyQuery.
	 * See {@see CogitowebProxyQuery} for further informations.
	 * 
	 * @param  string $context
	 * 
	 * @return ProxyQueryInterface
	 */
	public function createQuery($context = 'list')
	{
		$originalProxyQuery  = parent::createQuery($context);
		$queryBuilder        = $originalProxyQuery->getQueryBuilder();
		$cogitowebProxyQuery = new CogitowebProxyQuery($queryBuilder);

		return $cogitowebProxyQuery;
	}
}