<?php
namespace Jigoshop\Container\Configuration\Admin;

use Jigoshop\Container\Configuration\ConfigurationInterface;
use Jigoshop\Container\Services;
use Jigoshop\Container\Tags;
use Jigoshop\Container\Triggers;
use Jigoshop\Container\Factories;
use Jigoshop\Container\ClassLoader;

/**
 * Class MigrationConfiguration
 *
 * @package Jigoshop\Container\Configuration
 * @author  Krzysztof Kasowski
 */
class MigrationConfiguration implements ConfigurationInterface
{
	/**
	 * @param Services $services
	 *
	 * @return mixed
	 */
	public function initServices(Services $services)
	{
		$services->setDatails('jigoshop.admin.migration.options', 'Jigoshop\Admin\Migration\Options', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.tax'
		));
		$services->setDatails('jigoshop.admin.migration.coupons', 'Jigoshop\Admin\Migration\Coupons', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDatails('jigoshop.admin.migration.emails', 'Jigoshop\Admin\Migration\Emails', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDatails('jigoshop.admin.migration.products', 'Jigoshop\Admin\Migration\Products', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.product',
			'jigoshop.service.tax'
		));
		$services->setDatails('jigoshop.admin.migration.orders', 'Jigoshop\Admin\Migration\Orders', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages',
			'jigoshop.service.order',
			'jigoshop.service.shipping',
			'jigoshop.service.payment',
			'jigoshop.service.product'
		));
	}

	/**
	 * @param Tags $tags
	 *
	 * @return mixed
	 */
	public function initTags(Tags $tags)
	{
		$tags->add('jigoshop.admin.migration', 'jigoshop.admin.migration.options');
		$tags->add('jigoshop.admin.migration', 'jigoshop.admin.migration.coupons');
		$tags->add('jigoshop.admin.migration', 'jigoshop.admin.migration.emails');
		$tags->add('jigoshop.admin.migration', 'jigoshop.admin.migration.products');
		$tags->add('jigoshop.admin.migration', 'jigoshop.admin.migration.orders');
	}

	/**
	 * @param Triggers $triggers
	 *
	 * @return mixed
	 */
	public function initTriggers(Triggers $triggers)
	{

	}

	/**
	 * @param Factories $factories
	 *
	 * @return mixed
	 */
	public function initFactories(Factories $factories)
	{

	}

	/**
	 * @param ClassLoader $classLoader
	 *
	 * @return mixed
	 */
	public function initClassLoader(ClassLoader $classLoader)
	{

	}
}