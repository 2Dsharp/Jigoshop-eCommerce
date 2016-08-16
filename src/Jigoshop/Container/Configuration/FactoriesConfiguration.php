<?php

namespace Jigoshop\Container\Configuration;

use Jigoshop\Container\Services;
use Jigoshop\Container\Tags;
use Jigoshop\Container\Triggers;
use Jigoshop\Container\Factories;
use Jigoshop\Container\ClassLoader;

/**
 * Class FactoriesConfiguration
 *
 * @package Jigoshop\Container\Configuration
 * @author  Krzysztof Kasowski
 */
class FactoriesConfiguration implements ConfigurationInterface
{
	/**
	 * @param Services $services
	 *
	 * @return mixed
	 */
	public function initServices(Services $services)
	{
		$services->setDetails('jigoshop.factory.order_service', 'Jigoshop\Factory\OrderService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.order'
		));
		$services->setDetails('jigoshop.factory.product_service', 'Jigoshop\Factory\ProductService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.product'
		));
		$services->setDetails('jigoshop.factory.variable_product_service', 'Jigoshop\Factory\Product\VariableService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.product.variable',
			'jigoshop.service.product'
		));
		$services->setDetails('jigoshop.factory.tax_service', 'Jigoshop\Factory\TaxService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer'
		));
		$services->setDetails('jigoshop.factory.customer_service', 'Jigoshop\Factory\CustomerService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.customer'
		));
		$services->setDetails('jigoshop.factory.product', 'Jigoshop\Factory\Product', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDetails('jigoshop.factory.product.variable', 'Jigoshop\Factory\Product\Variable', array(
			'wpal',
			'jigoshop.service.product'
		));
		$services->setDetails('jigoshop.factory.order', 'Jigoshop\Factory\Order', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages'
		));
		$services->setDetails('jigoshop.factory.customer', 'Jigoshop\Factory\Customer', array(
			'wpal'
		));
		$services->setDetails('jigoshop.factory.shipping_service', 'Jigoshop\Factory\ShippingService', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDetails('jigoshop.factory.payment_service', 'Jigoshop\Factory\PaymentService', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDetails('jigoshop.factory.email', 'Jigoshop\Factory\Email', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDetails('jigoshop.factory.coupon', 'Jigoshop\Factory\Coupon', array(
			'wpal'
		));

		$services->setLazyStaus('jigoshop.factory.product', true);
		$services->setLazyStaus('jigoshop.factory.product.variable', true);
		$services->setLazyStaus('jigoshop.factory.order', true);
		$services->setLazyStaus('jigoshop.factory.customer', true);
		$services->setLazyStaus('jigoshop.factory.order', true);
		$services->setLazyStaus('jigoshop.factory.coupon', true);
	}

	/**
	 * @param Tags $tags
	 *
	 * @return mixed
	 */
	public function initTags(Tags $tags)
	{

	}

	/**
	 * @param Triggers $triggers
	 *
	 * @return mixed
	 */
	public function initTriggers(Triggers $triggers)
	{
		$triggers->add('jigoshop.factory.order', 'jigoshop.factory.order', 'init', array(
			'jigoshop.service.customer',
			'jigoshop.service.product',
			'jigoshop.service.shipping',
			'jigoshop.service.payment',
			'jigoshop.service.coupon'
		));
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