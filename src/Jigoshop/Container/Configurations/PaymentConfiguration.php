<?php

namespace Jigoshop\Container\Configurations;

use Jigoshop\Container\Services;
use Jigoshop\Container\Tags;
use Jigoshop\Container\Triggers;
use Jigoshop\Container\Factories;

/**
 * Class PaymentConfiguration
 *
 * @package Jigoshop\Container\Configuration
 * @author  Krzysztof Kasowski
 */
class PaymentConfiguration implements ConfigurationInterface
{
	/**
	 * @param Services $services
	 *
	 * @return mixed
	 */
	public function addServices(Services $services)
	{
		$services->setDetails('jigoshop.payment.cheque', 'Jigoshop\Payment\Cheque', array(
			'wpal',
			'jigoshop.options',
		));
		$services->setDetails('jigoshop.payment.on_delivery', 'Jigoshop\Payment\OnDelivery', array(
			'wpal',
			'jigoshop.options',
		));
		$services->setDetails('jigoshop.payment.paypal', 'Jigoshop\Payment\PayPal', array(
			'wpal',
			'di',
			'jigoshop.options',
			'jigoshop.messages',
		));
        $services->setDetails('jigoshop.api.paypal', 'Jigoshop\Payment\PayPal', array(
            'wpal',
            'di',
            'jigoshop.options',
            'jigoshop.messages',
        ));
		$services->setDetails('jigoshop.payment.bank_transfer', 'Jigoshop\Payment\BankTransfer', array(
			'wpal',
			'jigoshop.options',
		));
	}

	/**
	 * @param Tags $tags
	 *
	 * @return mixed
	 */
	public function addTags(Tags $tags)
	{
		$tags->add('jigoshop.payment.method', 'jigoshop.payment.cheque');
		$tags->add('jigoshop.payment.method', 'jigoshop.payment.on_delivery');
		$tags->add('jigoshop.payment.method', 'jigoshop.payment.paypal');
		$tags->add('jigoshop.payment.method', 'jigoshop.payment.bank_transfer');
	}

	/**
	 * @param Triggers $triggers
	 *
	 * @return mixed
	 */
	public function addTriggers(Triggers $triggers)
	{

	}

	/**
	 * @param Factories $factories
	 *
	 * @return mixed
	 */
	public function addFactories(Factories $factories)
	{

	}
}