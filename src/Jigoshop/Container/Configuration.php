<?php

namespace Jigoshop\Container;

use Jigoshop\Container;
use Jigoshop\Container\Configuration\ConfigurationInterface;

class Configuration
{
	private $configurations;

	public function init(Container $container)
	{
		foreach($this->configurations as $configuration) {
			$instance = new $configuration();
			if($instance instanceof ConfigurationInterface)	{
				$instance->initClassLoader($container->classLoader);
				$instance->initServices($container->services);
				$instance->initTags($container->tags);
				$instance->initTriggers($container->triggers);
				$instance->initFactories($container->factories);
			}
		}
	}

	public function getConfigurations()
	{
		$configurations = array(
			'\Jigoshop\Container\Configuration\MainConfiguration',
			'\Jigoshop\Container\Configuration\PagesConfiguration',
			'\Jigoshop\Container\Configuration\AdminConfiguration',
			'\Jigoshop\Container\Configuration\PaymentConfiguration',
			'\Jigoshop\Container\Configuration\ServicesConfiguration',
			'\Jigoshop\Container\Configuration\ShippingConfiguration',
			'\Jigoshop\Container\Configuration\FactoriesConfiguration',
			'\Jigoshop\Container\Configuration\Admin\MigrationConfiguration',
			'\Jigoshop\Container\Configuration\Admin\PagesConfiguration',
			'\Jigoshop\Container\Configuration\Admin\ReportsConfiguration',
			'\Jigoshop\Container\Configuration\Admin\SettingsConfiguration',
			'\Jigoshop\Container\Configuration\Admin\SystemInfoConfiguration'
		);

		$this->configurations = $configurations;
	}
}
