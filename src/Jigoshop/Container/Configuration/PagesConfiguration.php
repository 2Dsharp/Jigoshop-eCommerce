<?php

namespace Jigoshop\Container\Configuration;

use Jigoshop\Container\Services;
use Jigoshop\Container\Tags;
use Jigoshop\Container\Triggers;
use Jigoshop\Container\Factories;
use Jigoshop\Container\ClassLoader;

/**
 * Clas PagesConfiguration
 *
 * @package Jigoshop\Container\Configuration
 * @author  Krzysztof Kasowski
 */
class PagesConfiguration implements ConfigurationInterface
{
	/**
	 * @param Services $services
	 *
	 * @return mixed
	 */
	public function initServices(Services $services)
	{
		$services->setDatails('jigoshop.query.interceptor', 'Jigoshop\Query\Interceptor', array(
			'wpal',
			'jigoshop.options'
		));
		$services->setDatails('jigoshop.frontend.page_resolver', 'Jigoshop\Frontend\PageResolver', array(
			'wpal'
		));
		$services->setDatails('jigoshop.page.product_list', 'Jigoshop\Frontend\Page\ProductList', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.product',
			'jigoshop.service.cart',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.product_category_list', 'Jigoshop\Frontend\Page\ProductCategoryList', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.product',
			'jigoshop.service.cart',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.product_tag_list', 'Jigoshop\Frontend\Page\ProductTagList', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.product',
			'jigoshop.service.cart',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.product', 'Jigoshop\Frontend\Page\Product', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.product',
			'jigoshop.service.cart',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.cart', 'Jigoshop\Frontend\Page\Cart', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages',
			'jigoshop.service.cart',
			'jigoshop.service.product',
			'jigoshop.service.customer',
			'jigoshop.service.order',
			'jigoshop.service.shipping',
			'jigoshop.service.coupon'
		));
		$services->setDatails('jigoshop.page.checkout', 'Jigoshop\Frontend\Page\Checkout', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages',
			'jigoshop.service.cart',
			'jigoshop.service.customer',
			'jigoshop.service.shipping',
			'jigoshop.service.payment',
			'jigoshop.service.order'
		));
		$services->setDatails('jigoshop.page.checkout.thank_you', 'Jigoshop\Frontend\Page\Checkout\ThankYou', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages',
			'jigoshop.service.order'
		));
		$services->setDatails('jigoshop.page.checkout.pay', 'Jigoshop\Frontend\Page\Checkout\Pay', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.messages',
			'jigoshop.service.order',
			'jigoshop.service.payment'
		));
		$services->setDatails('jigoshop.page.account', 'Jigoshop\Frontend\Page\Account', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer',
			'jigoshop.service.order',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.account.edit_address', 'Jigoshop\Frontend\Page\Account\EditAddress', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.account.change_password', 'Jigoshop\Frontend\Page\Account\ChangePassword', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer',
			'jigoshop.messages'
		));
		$services->setDatails('jigoshop.page.account.orders', 'Jigoshop\Frontend\Page\Account\Orders', array(
			'wpal',
			'jigoshop.options',
			'jigoshop.service.customer',
			'jigoshop.service.order',
			'jigoshop.messages'
		));
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