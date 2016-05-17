<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Sonata\AdminBundle\Admin\CogitowebAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * Description of CogitowebTranslatableAdmin
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class CogitowebTranslatableAdmin extends CogitowebAdmin
{
	/**
	 * Create query
	 * 
	 * @param  string  $context
	 * @param  boolean $getCleanQuery
	 * 
	 * @return ProxyQueryInterface
	 */
	public function createQuery($context = 'list', $getCleanQuery = false)
	{
		$query = parent::createQuery($context);

		$query->andWhere('s_translations.locale = :locale');
		$query->setParameter('locale', $this->getLocale());

		if ($getCleanQuery) {
			return $query;
		}

		$query->addSelect('s_translations');

		return $query;
	}
}
