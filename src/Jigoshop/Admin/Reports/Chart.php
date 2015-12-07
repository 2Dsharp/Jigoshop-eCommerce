<?php

namespace Jigoshop\Admin\Reports;

use Jigoshop\Admin\Reports;
use Jigoshop\Core\Options;
use Jigoshop\Helper\Scripts;
use WPAL\Wordpress;

/**
 * Class Chart
 *
 * @package Jigoshop\Admin\Reports
 * @author  Krzysztof Kasowski
 *
 */
abstract class Chart
{
	/** @var Wordpress  */
	protected $wp;
	/** @var Options  */
	protected $options;
	/** @var array  */
	protected $charColors = array();
	/** @var array  */
	protected $range = array();
	/** @var array  */
	protected $orderStatus = array();
	/** @var  string */
	protected $currentRange;
	/** @var  string */
	protected $chartGroupBy;
	/** @var  string */
	protected $groupByQuery;
	/** @var  int */
	protected $chartInterval;
	/** @var  int */
	protected $barwidth;

	public function __construct(Wordpress $wp, Options $options, $currentRange)
	{
		//DEBUG
		\WpDebugBar\Debugger::init($wp);
		$this->wp = $wp;
		$this->options = $options;
		$this->currentRange = $currentRange;
		$this->orderStatus = isset($_GET['order_status']) && !empty($_GET['order_status']) ? $_GET['order_status'] : array('jigoshop-completed', 'jigoshop-processing');
		$wp->addAction('admin_enqueue_scripts', function () use ($wp){
			// Weed out all admin pages except the Jigoshop Settings page hits
			if (!in_array($wp->getPageNow(), array('admin.php', 'options.php'))) {
				return;
			}

			$screen = $wp->getCurrentScreen();
			if ($screen->base != 'jigoshop_page_'.Reports::NAME) {
				return;
			}
			Scripts::add('jigoshop.vendors.flot', JIGOSHOP_URL.'/assets/js/vendors/flot.min.js', array('jquery'));
			Scripts::add('jigoshop.reports.chart', JIGOSHOP_URL.'/assets/js/admin/reports/chart.js', array(
					'jquery',
					'jigoshop.vendors.flot'
			));
		});
	}

	/**
	 * Output the report
	 */
	abstract public function display();

	/**
	 * Get the main chart
	 *
	 * @return array
	 */
	abstract public function getMainChart();

	/**
	 * Get the legend for the main chart sidebar
	 *
	 * @return array
	 */
    abstract public function getChartLegend();

	/**
	 * [get_chart_widgets description]
	 *
	 * @return array
	 */
	abstract public function getChartWidgets();

	/**
	 * Get an export link if needed
	 */
	abstract public function getExportButton();

	/**
	 * Get the current range and calculate the start and end dates
	 *
	 */
	protected function calculateCurrentRange()
	{
		switch ($this->currentRange) {
			case 'custom' :
				$this->range['start'] = strtotime(sanitize_text_field($_GET['start_date']));
				$this->range['end'] = strtotime('midnight', strtotime(sanitize_text_field($_GET['end_date'])));

				if (!$this->range['end']) {
					$this->range['end'] = current_time('timestamp');
				}

				$interval = 0;
				$min_date = $this->range['start'];

				while (($min_date = strtotime("+1 MONTH", $min_date)) <= $this->range['end']) {
					$interval++;
				}

				// 3 months max for day view
				if ($interval > 3) {
					$this->chartGroupBy = 'month';
				} else {
					$interval = 0;
					$min_date = $this->range['start'];
					while (($min_date = strtotime("+1 day", $min_date)) <= $this->range['end']) {
						$interval++;
					}
					if($interval > 0){
						$this->chartGroupBy = 'day';
					} else {
						$this->chartGroupBy = 'hour';
					}
				}
				break;
			case 'all' :
				$this->range['start'] = strtotime(date('Y-m-d', strtotime($this->getFirstOrderDate())));
				$this->range['end'] = strtotime('midnight', current_time('timestamp'));
				$this->chartGroupBy = 'month';
				break;
			case 'year' :
				$this->range['start'] = strtotime(date('Y-01-01', current_time('timestamp')));
				$this->range['end'] = strtotime('midnight', current_time('timestamp'));
				$this->chartGroupBy = 'month';
				break;
			case 'last_month' :
				$first_day_current_month = strtotime(date('Y-m-01', current_time('timestamp')));
				$this->range['start'] = strtotime(date('Y-m-01', strtotime('-1 DAY', $first_day_current_month)));
				$this->range['end'] = strtotime(date('Y-m-t', strtotime('-1 DAY', $first_day_current_month)));
				$this->chartGroupBy = 'day';
				break;
			case 'month' :
				$this->range['start'] = strtotime(date('Y-m-01', current_time('timestamp')));
				$this->range['end'] = strtotime('midnight', current_time('timestamp'));
				$this->chartGroupBy = 'day';
				break;
			case '30day' :
				$this->range['start'] = strtotime('-29 days', current_time('timestamp'));
				$this->range['end'] = strtotime('midnight', current_time('timestamp'));
				$this->chartGroupBy = 'day';
				break;
			case '7day' :
				$this->range['start'] = strtotime('-6 days', current_time('timestamp'));
				$this->range['end'] = strtotime('midnight', current_time('timestamp'));
				$this->chartGroupBy = 'day';
				break;
			case 'today' :
				$this->range['start'] = strtotime('midnight', current_time('timestamp'));
				$this->range['end'] = strtotime('+1 hour', current_time('timestamp'));
				$this->chartGroupBy = 'hour';
				break;
		}

		// Group by
		switch ($this->chartGroupBy) {
			case 'hour' :
				$this->groupByQuery = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date), HOUR(posts.post_date)';
				$this->chartInterval = ceil(max(0, ($this->range['end'] - $this->range['start']) / (60 * 60)));
				$this->barwidth = 60 * 60 * 1000;
				break;
			case 'day' :
				$this->groupByQuery = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';
				$this->chartInterval = ceil(max(0, ($this->range['end'] - $this->range['start']) / (60 * 60 * 24)));
				$this->barwidth = 60 * 60 * 24 * 1000;
				break;
			case 'month' :
				$this->groupByQuery = 'YEAR(posts.post_date), MONTH(posts.post_date)';
				$this->chartInterval = 0;
				$min_date = $this->range['start'];

				while (($min_date = strtotime("+1 MONTH", $min_date)) <= $this->range['end']) {
					$this->chartInterval++;
				}

				$this->barwidth = 60 * 60 * 24 * 7 * 4 * 1000;
				break;
		}
	}

	/**
	 * Put data with post_date's into an array of times
	 *
	 * @param  array $data array of your data
	 * @param  string $dateKey key for the 'date' field. e.g. 'post_date'
	 * @param  string $dataKey key for the data you are charting
	 * @param  int $interval
	 * @param  string $startDate
	 * @param  string $groupBy
	 * @return array
	 */
	protected function prepareChartData($data, $dateKey, $dataKey, $interval, $startDate, $groupBy)
	{
		$preparedData = array();

		// Ensure all days (or months) have values first in this range
		for ($i = 0; $i <= $interval; $i++) {
			switch ($groupBy) {
				case 'hour' :
					$time = strtotime(date('YmdHi', strtotime($startDate)))+$i*3600000;
					break;
				case 'day' :
					$time = strtotime(date('Ymd', strtotime("+{$i} DAY", $startDate))).'000';
					break;
				case 'month' :
				default :
					$time = strtotime(date('Ym', strtotime("+{$i} MONTH", $startDate)).'01').'000';
					break;
			}

			if (!isset($preparedData[$time])) {
				$preparedData[$time] = array(esc_js($time), 0);
			}
		}
		\WpDebugBar\Debugger::getInstance()->getDebugBar()['messages']->addMessage($data, 'chart_dara');
		foreach ($data as $d) {
			switch ($groupBy) {
				case 'hour' :
					$time = (date('H', strtotime($d->$dateKey))*3600).'000';
					break;
				case 'day' :
					$time = strtotime(date('Ymd', strtotime($d->$dateKey))).'000';
					break;
				case 'month' :
				default :
					$time = strtotime(date('Ym', strtotime($d->$dateKey)).'01').'000';
					break;
			}

			if (!isset($preparedData[$time])) {
				continue;
			}

			if ($dataKey) {
				$preparedData[$time][1] += $d->$dataKey;
			} else {
				$preparedData[$time][1]++;
			}
		}

		return $preparedData;
	}

	protected function getFirstOrderDate()
	{
		$args = array(
			'posts_per_page'   => 1,
			'offset'           => 0,
			'orderby'          => 'post_date',
			'order'            => 'ASC',
			'post_type'        => 'shop_order',
		);
		$postsArray = get_posts( $args );

		return $postsArray[0]->post_date;
	}

	protected function filterItem($item, $value)
	{
		if (isset($value['where'])) {
			switch($value['where']['type']) {
				case 'item_id':
					$item = array_filter($item, function($product) use ($value){
						$result = false;
						foreach ($value['where']['keys'] as $key) {
							$result |= in_array($product[$key], $value['where']['value']);
						}

						return $result;
					});
					break;
				case 'comparison':
					$item = array_filter($item, function($product) use ($value){
						switch ($value['where']['operator']) {
							case '<>':
							case '!=':
								return $product[$value['where']['key']] != $value['where']['value'];
							case '=':
								return $product[$value['where']['key']] == $value['where']['value'];
							case '<':
								return $product[$value['where']['key']] < $value['where']['value'];
							case '>':
								return $product[$value['where']['key']] > $value['where']['value'];
							case '<=':
								return $product[$value['where']['key']] <= $value['where']['value'];
							case '>=':
								return $product[$value['where']['key']] >= $value['where']['value'];
							case 'in':
								return in_array($product[$value['where']['key']], $value['where']['value']);
							case 'intersection':
								$intersection = array_intersect($product[$value['where']['key']], $value['where']['value']);
								return !empty($intersection);
						}

						return false;
					});
					break;
				case 'object_comparison':
					switch ($value['where']['operator']) {
						case '<>':
						case '!=':
							if ($item[$value['where']['key']] != $value['where']['value']) {
								return $item;
							}
						case '=':
							if ($item[$value['where']['key']] == $value['where']['value']) {
								return $item;
							}
						case '<':
							if ($item[$value['where']['key']] < $value['where']['value']) {
								return $item;
							}
						case '>':
							if ($item[$value['where']['key']] > $value['where']['value']) {
								return $item;
							}
						case '<=':
							if ($item[$value['where']['key']] <= $value['where']['value']) {
								return $item;
							}
						case '>=':
							if ($item[$value['where']['key']] >= $value['where']['value']) {
								return $item;
							}
						case 'in':
							if (in_array($item[$value['where']['key']], $value['where']['value'])) {
								return $item;
							}
						case 'intersection':
							$source = $item[$value['where']['key']];
							if(is_array($source)) {
								if (isset($value['where']['map'])) {
									$source = array_map($value['where']['map'], $source);
								}

								$intersection = array_intersect($source, $value['where']['value']);
								if (!empty($intersection)) {
									return $item;
								};
							}
					}

					return false;
			}
		}

		return $item;
	}

	/**
	 * Get report totals such as order totals and discount amounts.
	 * Data example:
	 * 'select' => array(
	 *     'table' => array(
	 *			array(
	 * 				'field' => '*'
	 * 			)
	 * 		),
	 * ),
	 * 'from' => array(
	 * 		'table' => 'table_name'
	 * )
	 * Return example:
	 * "SELECT table.* FROM table_name AS table"
	 *
	 * @param  array $args
	 * @return array|string depending on query_type
	 */
	public function prepareQuery($args = array())
	{
		$defaultArgs = array(
			'select' => array(),
			'from' => array(),
			'join' => array(),
			'where' => array(),
			'group_by' => '',
			'order_by' => '',
			'filter_range' => false,
		);
		$args = $this->wp->applyFilters('jigoshop/admin/reports/chart/report_query_args', $args);
		$args = wp_parse_args($args, $defaultArgs);

		if (empty($args['select'])) {
			return '';
		}

		$query = implode(' ', array(
			$this->prepareQuerySelect($args['select']),
			$this->prepareQueryFrom($args['from']),
			$this->prepareQueryJoin($args['join']),
			$this->prepareQueryWhere($args['where'], $args['filter_range']),
			$this->prepareQueryGroupBy($args['group_by']),
			$this->prepareQueryOrderBy($args['order_by'])
		));
		return $query;
	}

	public function getOrderReportData($query)
	{
		return $this->wp->getWPDB()->get_results($query);
	}

	/**
	 * Converts array to std Object
	 *
	 * @param array $array
	 *
	 * @return object
	 */
	protected function arrayToObject(array $array)
	{
		return (object) $array;
	}

	/**
	 * Returns SQL query part (SELECT)
	 *
	 * @param array $args
	 * @return string
	 */
	private function prepareQuerySelect(array $args)
	{
		$select = 'SELECT';
		foreach($args as $table => $columns){
			for($i = 0 ; $i < sizeof($columns) ; $i++){
				$value = sprintf('%s.%s', $table, $columns[$i]['field']);
				if(isset($columns[$i]['distinct']) && $columns[$i]['distinct']){
					$value = sprintf('DISTINCT %s', $value);
				}
				if(!empty($columns[$i]['function'])){
					$value = sprintf('%s(%s)',$columns[$i]['function'], $value);
				}
				if(!empty($columns[$i]['name'])){
					$value = sprintf('%s AS %s', $value, $columns[$i]['name']);
				}
				$select = sprintf('%s %s,',$select, $value);
			}
		}

		return rtrim($select, ',');
	}

	/**
	 * Returns SQL query part (FROM)
	 *
	 * @param array $args
	 * @return string
	 */
	private function prepareQueryFrom(array $args)
	{
		$from = 'FROM';
		foreach($args as $tableAlias => $tableName){
			$from = sprintf('%s %s AS %s', $from, $tableName, $tableAlias);
		}

		return $from;
	}

	/**
	 * Returns SQL query part (JOIN)
	 *
	 * @param array $args
	 * @return string
	 */
	private function prepareQueryJoin(array $args)
	{
		$join = '';
		foreach($args as $alias => $data){
			$on = '1=1';
			for($i = 0; $i < sizeof($data['on']); $i++){
				$on = sprintf('%s AND %s.%s %s %s', $on, $alias, $data['on'][$i]['key'], $data['on'][$i]['compare'], $data['on'][$i]['value']);
			}
			$join = sprintf('%s LEFT JOIN %s AS %s ON %s', $join, $data['table'], $alias, $on);
		}

		return $join;
	}

	/**
	 * Returns SQL query part (WHERE)
	 *
	 * @param array $args
	 * @param bool $filterRange
	 * @return string
	 */
	private function prepareQueryWhere(array $args, $filterRange)
	{
		$where = 'WHERE 1=1';
		for($i = 0 ; $i < sizeof($args) ; $i++){
			$where = sprintf('%s AND %s %s %s', $where, $args[$i]['key'], $args[$i]['compare'], $args[$i]['value']);
		}
		if($filterRange){
			$where = sprintf('%s AND posts.post_date >= "%s" AND posts.post_date < "%s"', $where, date('Y-m-d', $this->range['start']), date('Y-m-d', strtotime('+1 DAY', $this->range['end'])));
		}

		return $where;
	}

	/**
	 * Returns SQL query part (GROUP BY)
	 *
	 * @param string $groupBy
	 * @return string
	 */
	private function prepareQueryGroupBy($groupBy)
	{
		if(!empty($groupBy)){
			return sprintf('GROUP BY %s', $groupBy);
		}

		return '';
	}

	/**
	 * Returns SQL query part (ORDER BY)
	 *
	 * @param $orderBy
	 * @return string
	 */
	private function prepareQueryOrderBy($orderBy)
	{
		if(!empty($orderBy)){
			return sprintf('ORDER BY %s',$orderBy);
		}

		return '';
	}
}