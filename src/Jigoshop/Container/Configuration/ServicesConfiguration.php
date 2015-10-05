<?php

namespace Jigoshop\Container\Configuration;

use Jigoshop\Container\Services;
use Jigoshop\Container\Tags;
use Jigoshop\Container\Triggers;
use Jigoshop\Container\Factories;
use Jigoshop\Container\ClassLoader;

/**
 * Class ServicesConfiguration
 *
 * @package Jigoshop\Container\Configuration
 * @author  Krzysztof Kasowski
 */
class ServicesConfiguration implements ConfigurationInterface
{
	/**
	 * @param Services $services
	 *
	 * @return mixed
	 */
	public function initServices(Services $services)
	{
		$services->setDatails('jigoshop.service.order', 'Jigoshop\Service\OrderService', array());
		$services->setDatails('jigoshop.service.product', 'Jigoshop\Service\ProductService', array());
		$services->setDatails('jigoshop.service.product.variable', 'Jigoshop\Service\Product\VariableService', array());
		$services->setDatails('jigoshop.service.tax', 'Jigoshop\Service\TaxService', array());
		$services->setDatails('jigoshop.service.customer', 'Jigoshop\Service\CustomerService', array());
		$services->setDatails('jigoshop.service.shipping', 'Jigoshop\Service\ShippingService', array());
		$services->setDatails('jigoshop.service.payment', 'Jigoshop\Service\PaymentService', array());
		$services->setDatails('jigoshop.service.cart', 'Jigoshop\Service\CartService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer',
			'jigoshop.service.product',
			'jigoshop.service.shipping',
			'jigoshop.service.payment',
			'jigoshop.factory.order'
		));
		$services->setDatails('jigoshop.service.email', 'Jigoshop\Service\EmailService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.email'
		));
		$services->setDatails('jigoshop.service.coupon', 'Jigoshop\Service\CouponService', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.factory.coupon'
		));

		$services->setLazyStaus('jigoshop.service.order', true);
		$services->setLazyStaus('jigoshop.service.product', true);
		$services->setLazyStaus('jigoshop.service.product.variable', true);
		$services->setLazyStaus('jigoshop.service.tax', true);
		$services->setLazyStaus('jigoshop.service.customer', true);
		$services->setLazyStaus('jigoshop.service.shipping', true);
		$services->setLazyStaus('jigoshop.service.payment', true);
		$services->setLazyStaus('jigoshop.service.cart', true);
		$services->setLazyStaus('jigoshop.service.coupon', true);
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
		$triggers->add('jigoshop.service.cart', 'jigoshop.service.cart', 'init', array());
	}

	/**
	 * @param Factories $factories
	 *
	 * @return mixed
	 */
	public function initFactories(Factories $factories)
	{
		$factories->set('jigoshop.service.order', 'jigoshop.factory.order_service', 'getService');
		$factories->set('jigoshop.service.product', 'jigoshop.factory.product_service', 'getService');
		$factories->set('jigoshop.service.product.variable', 'jigoshop.factory.variable_product_service', 'getService');
		$factories->set('jigoshop.service.tax', 'jigoshop.factory.tax_service', 'getService');
		$factories->set('jigoshop.service.customer', 'jigoshop.factory.customer_service', 'getService');
		$factories->set('jigoshop.service.shipping', 'jigoshop.factory.shipping_service', 'getService');
		$factories->set('jigoshop.service.payment', 'jigoshop.factory.payment_service', 'getService');
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