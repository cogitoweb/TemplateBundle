<?php

namespace Cogitoweb\TemplateBundle\Admin;

use Cogitoweb\TemplateBundle\Admin\BaseAdmin;

abstract class BaseTranslatableAdmin extends BaseAdmin
{
    
    /**
     * 
     * @param string $context
     * @param boolean $get_clean_query
     * @return type
     */
    public function createQuery($context = 'list', $get_clean_query = false)
    {
        $query = parent::createQuery($context);
        
        $query->andWhere('s_translations.locale = :locale');
        $query->setParameter('locale', $this->getLocale());
        
        if($get_clean_query)
        {
            return $query;
        }

        $query->addSelect('s_translations');

        return $query;
    }

}
