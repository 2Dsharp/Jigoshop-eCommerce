<?php

namespace Jigoshop\Admin;

use Jigoshop\Admin\Settings\AdvancedTab;
use Jigoshop\Admin\Settings\GeneralTab;
use Jigoshop\Admin\Settings\ProductsTab;
use Jigoshop\Entity\Product\Attributes\StockStatus;
use Jigoshop\Helper\Country;
use Jigoshop\Helper\Render;
use Jigoshop\Helper\Scripts;
use Jigoshop\Helper\Styles;
use Jigoshop\Integration;

/**
 * Class Setup
 * @package Jigoshop\Admin;
 * @author Krzysztof Kasowski
 */
class Setup implements DashboardInterface
{
    const SLUG = 'jigoshop_setup';

    public function __construct()
    {
        Styles::add('jigoshop.admin.setup', \JigoshopInit::getUrl().'/assets/css/admin/setup.css', ['jigoshop.admin']);
        Styles::add('jigoshop.admin.settings', \JigoshopInit::getUrl().'/assets/css/admin/settings.css', ['jigoshop.admin']);
        Styles::add('jigoshop.vendors.select2', \JigoshopInit::getUrl().'/assets/css/vendors/select2.css', ['jigoshop.admin']);
        Styles::add('jigoshop.vendors.datepicker', \JigoshopInit::getUrl().'/assets/css/vendors/datepicker.css', ['jigoshop.admin']);
        Styles::add('jigoshop.vendors.bs_switch', \JigoshopInit::getUrl().'/assets/css/vendors/bs_switch.css', ['jigoshop.admin']);

        Scripts::add('jigoshop.admin.setup', \JigoshopInit::getUrl().'/assets/js/admin/setup.js', ['jigoshop.admin']);
        Scripts::add('jigoshop.admin.settings', \JigoshopInit::getUrl() . '/assets/js/admin/settings.js', ['jigoshop.admin'], ['in_footer' => true]);
        Scripts::add('jigoshop.vendors.select2', \JigoshopInit::getUrl() . '/assets/js/vendors/select2.js', [
            'jigoshop.admin.settings',
        ], ['in_footer' => true]);
        Scripts::add('jigoshop.vendors.bs_tab_trans_tooltip_collapse', \JigoshopInit::getUrl() . '/assets/js/vendors/bs_tab_trans_tooltip_collapse.js', [
            'jigoshop.admin.settings',
        ], ['in_footer' => true]);
        Scripts::add('jigoshop.vendors.bs_switch', \JigoshopInit::getUrl() . '/assets/js/vendors/bs_switch.js', [
            'jigoshop.admin.settings',
        ], ['in_footer' => true]);


        $states = [];
        foreach (Country::getAllStates() as $country => $stateList) {
            foreach ($stateList as $code => $state) {
                $states[$country][] = ['id' => $code, 'text' => $state];
            }
        }
        Scripts::localize('jigoshop.admin.setup', 'jigoshop_setup', [
            'states' => $states,
        ]);

        $this->display();
    }

    /** @return string Title of page. */
    public function getTitle()
    {
        return __('Setup', 'jigoshop');
    }

    /** @return string Required capability to view the page. */
    public function getCapability()
    {
        return 'manage_jigoshop';
    }

    /** @return string Menu slug. */
    public function getMenuSlug()
    {
        return self::SLUG;
    }

    public function getSteps()
    {
        return [
            'page-setup' => __('Page setup', 'jigoshop'),
            'store-settings' => __('Store Settings', 'jigoshop'),
            'shipping' => __('Shipping', 'jigoshop'),
            //'payments' => __('Payments', 'jigoshop'),
            //'theme' => __('Theme', 'jigoshop'),
            'ready' => __('Ready!', 'jigoshop'),
        ];
    }

    public function getCurrentStep()
    {
        $steps = $this->getSteps();

        return isset($_GET['step'], $steps[$_GET['step']]) ?  $_GET['step'] : '';
    }

    public function getNextStep()
    {
        $steps = $this->getSteps();
        $currentStep = $this->getCurrentStep();
        $keys = array_keys($steps);
        $currentId = array_search($currentStep, $keys);

        if($currentId === false) {
            return $keys[0];
        } elseif (isset($keys[$currentId + 1])) {
            return $keys[$currentId + 1];
        } else {
            return null;
        }
    }

    public function getOptions()
    {
        $pages = [];
        foreach(get_pages() as $page) {
            $pages[$page->ID] = $page->post_title;
        }
        $settings = Integration::getOptions()->getAll();
        $weightUnit = [
            'kg' => __('Kilograms', 'jigoshop'),
            'lbs' => __('Pounds', 'jigoshop'),
        ];
        $dimensionUnit = [
            'cm' => __('Centimeters', 'jigoshop'),
            'in' => __('Inches', 'jigoshop'),
        ];
        $stockStatuses = [
            StockStatus::IN_STOCK => __('In stock', 'jigoshop'),
            StockStatus::OUT_STOCK => __('Out of stock', 'jigoshop'),
        ];

        $options = [
            'page-setup' => [
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][shop]',
                    'label' => __('Shop page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['shop'],
                    'options' => $pages,
                ],
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][cart]',
                    'label' => __('Cart page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['cart'],
                    'options' => $pages,
                ],
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][checkout]',
                    'label' => __('Checkout page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['checkout'],
                    'options' => $pages,
                ],
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][checkout_thank_you]',
                    'label' => __('Thanks page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['checkout_thank_you'],
                    'options' => $pages,
                ],
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][account]',
                    'label' => __('My account page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['account'],
                    'options' => $pages,
                ],
                [
                    'name' => 'jigoshop['.AdvancedTab::SLUG.'][pages][terms]',
                    'label' => __('Terms page', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[AdvancedTab::SLUG]['pages']['terms'],
                    'options' => array_merge([0 => __('None', 'jigoshop')], $pages)
                ],
            ],
            'store-settings' => [
                [
                    'id' => 'country',
                    'name' => 'jigoshop['.GeneralTab::SLUG.'][country]',
                    'label' => __('Shop location (country)', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[GeneralTab::SLUG]['country'],
                    'options' => Country::getAll(),
                ],
                [
                    'id' => 'state',
                    'name' => 'jigoshop['.GeneralTab::SLUG.'][state]',
                    'label' => __('Shop location (state)', 'jigoshop'),
                    'type' => 'text',
                    'value' => $settings[GeneralTab::SLUG]['state'],
                ],
                [
                    'name' => 'jigoshop['.GeneralTab::SLUG.'][email]',
                    'label' => __('Administrator e-mail', 'jigoshop'),
                    'type' => 'text',
                    'tip' => __('The email address used to send all Jigoshop related emails, such as order confirmations and notices.', 'jigoshop'),
                    'value' => $settings[GeneralTab::SLUG]['email'],
                ],
            ],
            'shipping' => [
                [
                    'name' => 'jigoshop['.ProductsTab::SLUG.'][weight_unit]',
                    'label' => __('Weight units', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[ProductsTab::SLUG]['weight_unit'],
                    'options' => $weightUnit,
                ],
                [
                    'name' => 'jigoshop['.ProductsTab::SLUG.'][dimensions_unit]',
                    'label' => __('Dimensions unit', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[ProductsTab::SLUG]['dimensions_unit'],
                    'options' => $dimensionUnit,
                ],
                [
                    'name' => 'jigoshop['.ProductsTab::SLUG.'][stock_status]',
                    'label' => __('Stock status', 'jigoshop'),
                    'description' => __('This option allows you to change default stock status for new products.', 'jigoshop'),
                    'type' => 'select',
                    'value' => $settings[ProductsTab::SLUG]['stock_status'],
                    'options' => $stockStatuses,
                ],
            ],
        ];

        return $options[$this->getCurrentStep()];
    }

    /** Displays the page. */
    public function display()
    {
        Render::output('admin/setup', [
            'steps' => $this->getSteps(),
            'currentStep' => $this->getCurrentStep(),
            'nextStep' => $this->getNextStep(),
            'options' => $this->getOptions(),
        ]);

        exit;
    }
}