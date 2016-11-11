<?php

namespace Cogitoweb\TemplateBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CogitowebTemplateExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

	/**
	 * {@inheritdoc}
	 * 
	 * @todo Cogitoweb: temporaly disabled; is it needed?
	 */
	public function _prepend(ContainerBuilder $container)
	{
		// Cogitoweb: override SonataDoctrineORMAdmin bundle default template
		$name = 'sonata_doctrine_orm_admin';

		$config['templates']['form'] = ['CogitowebTemplateBundle:Form:cogitoweb_form_admin_fields.html.twig'];

		$container->prependExtensionConfig($name, $config);
	}
}