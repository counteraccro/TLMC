<?php
namespace App\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 */
class AppExtension extends Extension
{
	/**
	 * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::load()
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		
		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../config'));
		$loader->load('services.yml');
		//$loader->load('parameters.yml');
		
		echo __DIR__.'/../Resources/DataFixtures';
        die();
		
		$fixture_loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/DataFixtures'));
		$fixture_loader->load('Patients.yml');
		
		$data_loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/datas'));
		//$data_loader->load('first_name.yml')
	}
}