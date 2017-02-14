<?php

namespace Jigoshop\Admin\Settings;

use Jigoshop\Core\Options;
use Jigoshop\Core\Messages;
use Jigoshop\Helper\Country;
use Jigoshop\Helper\Render;
use Jigoshop\Helper\Scripts;
use Jigoshop\Service\TaxServiceInterface;
use WPAL\Wordpress;

/**
 * Taxes tab definition.
 *
 * @package Jigoshop\Admin\Settings
 */
class TaxesTab implements TabInterface
{
	const SLUG = 'tax';

	/** @var array */
	private $options;
	/** @var TaxServiceInterface */
	private $taxService;

	public function __construct(Wordpress $wp, Options $options, TaxServiceInterface $taxService, Messages $messages)
	{
		$this->options = $options->get(self::SLUG);
		$this->taxService = $taxService;
		$options = $this->options;

		$wp->addAction('admin_enqueue_scripts', function () use ($options){
			if (!isset($_GET['tab']) || $_GET['tab'] != TaxesTab::SLUG) {
				return;
			}

			$classes = array();
			foreach ($options['classes'] as $class) {
				$classes[$class['class']] = $class['label'];
			}

			$states = array();
			foreach (Country::getAllStates() as $country => $stateList) {
				$states[$country] = array(
					array('id' => '', 'text' => _x('All states', 'admin_taxing', 'jigoshop')),
				);
				foreach ($stateList as $code => $state) {
					$states[$country][] = array('id' => $code, 'text' => $state);
				}
			}

			$countries = array_merge(
				array('' => __('All countries', 'jigoshop')),
				Country::getAll()
			);

			Scripts::add('jigoshop.admin.settings.taxes', \JigoshopInit::getUrl().'/assets/js/admin/settings/taxes.js', array(
				'jquery',
			), array('page' => 'jigoshop_page_jigoshop_settings'));
			Scripts::localize('jigoshop.admin.settings.taxes', 'jigoshop_admin_taxes', array(
				'new_class' => Render::get('admin/settings/tax/class', array('class' => array('label' => '', 'class' => ''))),
				'new_rule' => Render::get('admin/settings/tax/rule', array(
					'rule' => array('id' => '', 'label' => '', 'class' => '', 'is_compound' => false, 'rate' => '', 'country' => '', 'states' => array(), 'postcodes' => array()),
					'classes' => $classes,
					'countries' => $countries,
				)),
				'states' => $states,
			));
		});
	}

	/**
	 * @return string Title of the tab.
	 */
	public function getTitle()
	{
		return __('Taxes', 'jigoshop');
	}

	/**
	 * @return string Tab slug.
	 */
	public function getSlug()
	{
		return self::SLUG;
	}

	/**
	 * @return array List of items to display.
	 */
	public function getSections()
	{
		$classes = array();
		foreach ($this->options['classes'] as $class) {
			$classes[$class['class']] = $class['label'];
		}

		return array(
			[
				'title' => __('Main', 'jigoshop'),
				'id' => 'main',
				'fields' => [
//                    [
//                        'id' => 'default_country',
//                        'name' => '[default_country]',
//                        'title' => __('Default country', 'jigoshop'),
//                        'type' => 'select',
//                        'value' => $this->options['country'],
//                        'options' => Country::getAll(),
//                    ],
//                    [
//                        'id' => 'default_state',
//                        'name' => '[default_state]',
//                        'title' => __('Default state', 'jigoshop'),
//                        'type' => 'text',
//                        'value' => $this->options['state'],
//                    ],
//                    [
//                        'id' => 'default_postcode',
//                        'name' => '[default_postcode]',
//                        'title' => __('Default postcode', 'jigoshop'),
//                        'type' => 'text',
//                        'value' => $this->options['postcode'],
//                    ],
					[
						'name' => '[shipping]',
						'title' => __('Taxes based on shipping country?', 'jigoshop'),
						'type' => 'checkbox',
						'description' => __('By default, taxes based on billing country.', 'jigoshop'),
						'checked' => $this->options['shipping'],
						'classes' => ['switch-medium'],
					],
				],
			],
            [
                'title' => __('Prices', 'jigoshop'),
                'id' => 'prices',
                'fields' => [
                    [
                        'name' => '[prices_entered]',
                        'title' => __('Entered prices tax status', 'jigoshop'),
                        'type' => 'select',
                        'value' => $this->options['prices_entered'],
                        'options' => [
                            'without_tax' => __('I will enter prices without tax', 'jigoshop'),
                            'with_tax' => __('I will enter prices with tax included', 'jigoshop'),
                        ]
                    ],
                    [
                        'name' => '[item_prices]',
                        'title' => __('Show prices in cart and checkout', 'jigoshop'),
                        'type' => 'select',
                        'value' => $this->options['item_prices'],
                        'options' => [
                            'including_tax' => __('Including tax', 'jigoshop'),
                            'excluding_tax' => __('Excluding tax', 'jigoshop'),
                        ]
                    ],
                    [
                        'name' => '[product_prices]',
                        'title' => __('Show product prices', 'jigoshop'),
                        'type' => 'select',
                        'value' => $this->options['product_prices'],
                        'options' => [
                            'including_tax' => __('Including tax', 'jigoshop'),
                            'excluding_tax' => __('Excluding tax', 'jigoshop'),
                        ]
                    ],
                    [
                        'name' => '[show_suffix]',
                        'title' => __('Show tax suffix', 'jigoshop'),
                        'type' => 'select',
                        'value' => $this->options['show_suffix'],
                        'options' => [
                            'in_cart_totals' => __('In cart totals', 'jigoshop'),
                            'everywhere' => __('Everywhere', 'jigoshop'),
                        ]
                    ],
                    [
                        'name' => '[suffix_for_included]',
                        'title' => __('Suffix for prices with tax included', 'jigoshop'),
                        'type' => 'text',
                        'value' => $this->options['suffix_for_included'],
                    ],
                    [
                        'name' => '[suffix_for_excluded]',
                        'title' => __('Suffix for prices without tax', 'jigoshop'),
                        'type' => 'text',
                        'value' => $this->options['suffix_for_excluded'],
                    ],
                ]
            ],
			array(
				'title' => __('Classes', 'jigoshop'),
				'id' => 'classes',
				'fields' => array(
					array(
						'title' => '',
						'name' => '[classes]',
						'type' => 'user_defined',
						'display' => array($this, 'displayClasses'),
					),
				),
			),
			array(
				'title' => __('Rules', 'jigoshop'),
				'id' => 'rules',
				'fields' => array(
					array(
						'title' => '',
						'name' => '[rules]',
						'type' => 'user_defined',
						'display' => array($this, 'displayRules'),
					),
				),
			),
			array(
				'title' => __('New products', 'jigoshop'),
				'description' => __('This section defines default tax settings for new products.', 'jigoshop'),
				'id' => 'defaults',
				'fields' => array(
					array(
						'title' => __('Is taxable?', 'jigoshop'),
						'name' => '[defaults][taxable]',
						'type' => 'checkbox',
						'checked' => $this->options['defaults']['taxable'],
						'classes' => array('switch-medium'),
					),
					array(
						'title' => __('Tax classes', 'jigoshop'),
						'name' => '[defaults][classes]',
						'type' => 'select',
						'multiple' => true,
						'options' => $classes,
						'value' => $this->options['defaults']['classes'],
					),
				),
			),
		);
	}

	public function displayClasses()
	{
		Render::output('admin/settings/tax/classes', array(
			'classes' => $this->options['classes'],
		));
	}

	public function displayRules()
	{
		$classes = array();
		foreach ($this->options['classes'] as $class) {
			$classes[$class['class']] = $class['label'];
		}
		$countries = array_merge(
			array('' => __('All countries', 'jigoshop')),
			Country::getAll()
		);
		Render::output('admin/settings/tax/rules', array(
			'rules' => $this->taxService->getRules(),
			'classes' => $classes,
			'countries' => $countries,
		));
	}

	/**
	 * Validate and sanitize input values.
	 *
	 * @param array $settings Input fields.
	 *
	 * @return array Sanitized and validated output.
	 * @throws ValidationException When some items are not valid.
	 */
	public function validate($settings)
	{
		$settings['included'] = $settings['included'] == 'on';
		$settings['shipping'] = $settings['shipping'] == 'on';
		$classes = $settings['classes'];
		$settings['classes'] = array();
		foreach ($classes['class'] as $key => $class) {
			$settings['classes'][] = array(
				'class' => $class,
				'label' => $classes['label'][$key],
			);
		}

		$settings['defaults']['taxable'] = $settings['defaults']['taxable'] == 'on';
		$settings['defaults']['classes'] = array_filter($settings['defaults']['classes'], function ($class) use ($classes){
			return in_array($class, $classes['class']);
		});

		if (!isset($settings['rules'])) {
			$settings['rules'] = array('id' => array());
		}

		$this->taxService->removeAllExcept($settings['rules']['id']);

		$currentKey = 0;
		foreach ($settings['rules']['id'] as $key => $id) {
			if (empty($id) && $settings['rules']['compound'][$key + 1] == 'on') {
				$currentKey++;
			}

			$this->taxService->save(array(
				'id' => $id,
				'rate' => $settings['rules']['rate'][$key],
				'is_compound' => $settings['rules']['compound'][$key + $currentKey] == 'on',
				'label' => $settings['rules']['label'][$key],
				'class' => $settings['rules']['class'][$key],
				'country' => $settings['rules']['country'][$key],
				'states' => $settings['rules']['states'][$key],
				'postcodes' => $settings['rules']['postcodes'][$key],
			));
		}
		unset($settings['rules']);

		//if (!in_array($settings['price_tax'], array('with_tax', 'without_tax'))) {
		//	$this->messages->addWarning(sprintf(__('Invalid prices option: "%s". Value set to %s.', 'jigoshop'), $settings['price_tax'], __('Without tax', 'jigoshop')));
		//	$settings['price_tax'] = 'without_tax';
		//}

		return $settings;
	}
}
