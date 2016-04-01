<?php

namespace Jigoshop;

use Jigoshop\Admin\Dashboard;
use Jigoshop\Admin\PageInterface;
use Jigoshop\Admin\Permalinks;
use Jigoshop\Admin\Settings;
use Jigoshop\Core\Types;
use Jigoshop\Helper\Scripts;
use Jigoshop\Helper\Styles;
use Monolog\Registry;
use WPAL\Wordpress;

/**
 * Class for handling administration panel.
 *
 * @package Jigoshop
 * @author  Amadeusz Starzykiewicz
 */
class Admin
{
	const MENU = 'jigoshop';

	/** @var \WPAL\Wordpress */
	private $wp;
	/** @var array */
	private $pages = array(
		'jigoshop' => array(),
		'products' => array(),
		'orders' => array(),
	);
	private $dashboard;

	public function __construct(Wordpress $wp, Dashboard $dashboard, Permalinks $permalinks)
	{
		$this->wp = $wp;
		$this->dashboard = $dashboard;

		$wp->addAction('admin_menu', array($this, 'beforeMenu'), 9);
		$wp->addAction('admin_menu', array($this, 'afterMenu'), 50);

		//TODO do wyrzucenia, przeniesienia do osobnych widokow
		Scripts::add('jigoshop.vendors.bs_tab_trans_tooltip_collapse', JIGOSHOP_URL.'/assets/js/vendors/bs_tab_trans_tooltip_collapse.js', array('jquery'));
		Styles::add('jigoshop.vendors.select2', JIGOSHOP_URL.'/assets/css/vendors/select2.css');
		Scripts::add('jigoshop.vendors.select2', JIGOSHOP_URL.'/assets/js/vendors/select2.js', array('jquery'));


		Styles::add('jigoshop.admin', JIGOSHOP_URL . '/assets/css/admin.css');

		Scripts::add('jigoshop.admin', JIGOSHOP_URL.'/assets/js/admin.js', array(
			'jquery',
			'jigoshop.helpers',
			'jigoshop.vendors.bs_tab_trans_tooltip_collapse'
		));
		Scripts::add('jigoshop.vendors.bs_tab_trans_tooltip_collapse', JIGOSHOP_URL . '/assets/js/vendors/bs_tab_trans_tooltip_collapse.js', array(
			'jquery',
		), array('in_footer' => true));
	}

	/**
	 * Adds new page to Jigoshop admin panel.
	 * Available parents:
	 *   * jigoshop - main Jigoshop menu,
	 *   * products - Jigoshop products menu
	 *   * orders - Jigoshop orders menu
	 *
	 * @param $page PageInterface Page to add.
	 *
	 * @throws Exception When trying to add page not in Jigoshop menus.
	 */
	public function addPage(PageInterface $page)
	{
		$parent = $page->getParent();
		if (!isset($this->pages[$parent])) {
			if (WP_DEBUG) {
				throw new Exception(sprintf('Trying to add page to invalid parent (%s). Available ones are: %s', $parent, join(', ', array_keys($this->pages))));
			}

			Registry::getInstance(JIGOSHOP_LOGGER)->addDebug(sprintf('Trying to add page to invalid parent (%s).', $parent), array('parents' => $this->pages));

			return;
		}

		$this->pages[$parent][] = $page;
	}

	/**
	 * Adds Jigoshop menus.
	 */
	public function beforeMenu()
	{
		$menu = $this->wp->getMenu();

		if ($this->wp->currentUserCan('manage_jigoshop')) {
			$menu[54] = array('', 'read', 'separator-jigoshop', '', 'wp-menu-separator jigoshop');
		}

		$this->wp->addMenuPage(__('Jigoshop'), __('Jigoshop'), 'manage_jigoshop', 'jigoshop', array($this->dashboard, 'display'), null, 55);
		foreach ($this->pages['jigoshop'] as $page) {
			/** @var $page PageInterface */
			$this->wp->addSubmenuPage(self::MENU, $page->getTitle(), $page->getTitle(), $page->getCapability(), $page->getMenuSlug(), array($page, 'display'));
		}

		foreach ($this->pages['products'] as $page) {
			/** @var $page PageInterface */
			$this->wp->addSubmenuPage('edit.php?post_type='.Types::PRODUCT, $page->getTitle(), $page->getTitle(), $page->getCapability(), $page->getMenuSlug(), array(
				$page,
				'display'
			));
		}

		foreach ($this->pages['orders'] as $page) {
			/** @var $page PageInterface */
			$this->wp->addSubmenuPage('edit.php?post_type='.Types::ORDER, $page->getTitle(), $page->getTitle(), $page->getCapability(), $page->getMenuSlug(), array(
				$page,
				'display'
			));
		}

		$this->wp->doAction('jigoshop\admin\before_menu');
	}

	/**
	 * Adds Jigoshop settings and system information menus (at the end of Jigoshop sub-menu).
	 */
	public function afterMenu()
	{
//		$this->wp->addSubmenuPage(self::MENU, $this->settings->getTitle(), $this->settings->getTitle(), $this->settings->getCapability(),
//			$this->settings->getMenuSlug(), array($this->settings, 'display'));
//		$this->wp->addSubmenuPage(self::MENU, $this->systemInfo->getTitle(), $this->systemInfo->getTitle(), $this->systemInfo->getCapability(),
//			$this->systemInfo->getMenuSlug(), array($this->systemInfo, 'display'));

		$this->wp->doAction('jigoshop\admin\after_menu');
	}
}
