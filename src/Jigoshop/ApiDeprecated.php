<?php

namespace Jigoshop;

use Jigoshop\Container;
use WPAL\Wordpress;

/**
 * Class ApiDeprecated
 * @package Jigoshop
 * @deprecated
 */
class ApiDeprecated
{
	const API_ENDPOINT = 'jigoshop_api';

	/** @var Wordpress */
	private $wp;
	/** @var \Jigoshop\Container */
	private $di;

	public function __construct(Wordpress $wp, Container $di)
	{
		$this->wp = $wp;
		$this->di = $di;
	}

	public function run()
	{
		$this->wp->addFilter('query_vars', array($this, 'addQueryVars'), 0);
		$this->wp->addAction('init', array($this, 'addEndpoint'), 1);
		$this->wp->addAction('parse_request', array($this, 'parseRequest'), 0);
	}

	/**
	 * Adds Jigoshop API query var to available vars.
	 *
	 * @param $vars array Current list of variables.
	 *
	 * @return array Updated list of variables.
	 */
	public function addQueryVars($vars)
	{
		$vars[] = self::API_ENDPOINT;

		return $vars;
	}

	/**
	 * Adds rewrite endpoint for processing Jigoshop APIs
	 */
	public function addEndpoint()
	{
		$this->wp->addRewriteEndpoint(self::API_ENDPOINT, EP_ALL);
	}

    /**
     * @param \WP_Query $query
     */
	public function parseRequest($query)
	{
        $endpoint = isset($query->query_vars[self::API_ENDPOINT]) ? $query->query_vars[self::API_ENDPOINT] : null;
		if (!empty($endpoint)) {
			if ($this->di->services->detailsExists('jigoshop.api.'.$endpoint)) {
				ob_start();
				$api = $this->di->get('jigoshop.api.'.$endpoint);

				if (!($api instanceof Api\Processable)) {
					if (WP_DEBUG) {
						throw new Exception(__('Provided API is not processable.', 'jigoshop'));
					}

					return;
				}

				$api->processResponse();
			} else {
				$this->wp->doAction('jigoshop_api_'.$endpoint);
			}

			exit;
		}
	}
}
