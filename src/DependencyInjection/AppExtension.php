<?php
namespace App\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // ... you'll load the files here later
    }
}


/*public function load(array $configs, ContainerBuilder $container)
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
 }*/