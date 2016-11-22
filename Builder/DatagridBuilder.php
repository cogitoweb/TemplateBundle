<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Builder;

use Cogitoweb\TemplateBundle\Datagrid\Datagrid;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder as SonataDatagridBuilder;

/**
 * Description of DatagridBuilder
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class DatagridBuilder extends SonataDatagridBuilder
{
	/**
	 * {@inheritdoc}
	 */
	public function getBaseDatagrid(AdminInterface $admin, array $values = array())
	{
		$datagrid = parent::getBaseDatagrid($admin, $values);

		$defaultOptions = array();
        if ($this->csrfTokenEnabled) {
            $defaultOptions['csrf_protection'] = false;
        }

        // NEXT_MAJOR: Remove this line when drop Symfony <2.8 support
        $formType = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form';
        $formBuilder = $this->formFactory->createNamedBuilder('filter', $formType, array(), $defaultOptions);

		// Use Cogitoweb datagrid
		return new Datagrid($admin->createQuery(), $admin->getList(), $datagrid->getPager(), $formBuilder, $values);
	}
}
