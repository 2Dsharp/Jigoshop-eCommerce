<?php

namespace Jigoshop\Admin\Reports\Chart;

use Jigoshop\Admin\Reports;
use Jigoshop\Admin\Reports\Chart;
use Jigoshop\Core\Options;
use Jigoshop\Helper\Currency;
use Jigoshop\Helper\Product;
use Jigoshop\Helper\Render;
use Jigoshop\Helper\Scripts;
use WPAL\Wordpress;

class DiscountSummary extends Chart
{
	public $chartColours = array();
	public $couponCodes = array();
	private $reportData;

	/**
	 * @param Wordpress $wp
	 * @param Options   $options
	 * @param string    $currentRange
	 */
	public function __construct(Wordpress $wp, Options $options, $currentRange)
	{
		parent::__construct($wp, $options, $currentRange);
		if (isset($_GET['coupon_codes']) && is_array($_GET['coupon_codes'])) {
			$this->couponCodes = array_filter(array_map('sanitize_text_field', $_GET['coupon_codes']));
		} elseif (isset($_GET['coupon_codes'])) {
			$this->couponCodes = array_filter(array(sanitize_text_field($_GET['coupon_codes'])));
		}
		// Prepare data for report
		$this->calculateCurrentRange();
		$this->getReportData();
		$this->getChartColors();

		$wp->addAction('admin_enqueue_scripts', function () use ($wp){
			// Weed out all admin pages except the Jigoshop Settings page hits
			if (!in_array($wp->getPageNow(), array('admin.php', 'options.php'))) {
				return;
			}

			$screen = $wp->getCurrentScreen();
			if ($screen->base != 'jigoshop_page_'.Reports::NAME) {
				return;
			}
			Scripts::add('jigoshop.flot', JIGOSHOP_URL.'/assets/js/flot/jquery.flot.min.js', array('jquery'));
			Scripts::add('jigoshop.flot.time', JIGOSHOP_URL.'/assets/js/flot/jquery.flot.time.min.js', array(
				'jquery',
				'jigoshop.flot'
			));
			Scripts::add('jigoshop.flot.pie', JIGOSHOP_URL.'/assets/js/flot/jquery.flot.pie.min.js', array(
				'jquery',
				'jigoshop.flot'
			));
			Scripts::add('jigoshop.reports.chart', JIGOSHOP_URL.'/assets/js/admin/reports/chart.js', array(
				'jquery',
				'jigoshop.flot'
			));
			Scripts::localize('jigoshop.reports.chart', 'chart_data', $this->getMainChart());
		});
	}

	public function getChartLegend()
	{
		$legend = array();

		$query = array(
			'data' => array(
				'order_data' => array(
					'type' => 'meta',
					'name' => 'discount_amount',
					'process' => true,
				),
				'post_date' => array(
					'type' => 'post_data',
					'function' => '',
					'name' => 'post_date'
				),
			),
			'query_type' => 'get_results',
			'filter_range' => true,
			'order_types' => array('shop_order'),
		);

		if ($this->couponCodes) {
			$query['data']['order_data']['where'] = array(
				'type' => 'object_comparison',
				'key' => 'order_discount_coupons',
				'value' => $this->couponCodes,
				'operator' => 'intersection',
				'map' => function($item){
					return $item['code'];
				},
			);
		}

		$items = $this->getOrderReportData($query);
		$totalDiscount = array_sum(array_map(function($item){
			return $item->discount_amount;
		}, $items));
		$totalCoupons = absint(array_sum(array_map(function($item){
			return $item->coupons_used;
		}, $items)));

		$legend[] = array(
			'title' => sprintf(__('%s discounts in total', 'jigoshop'), '<strong>'.Product::formatPrice($totalDiscount).'</strong>'),
			'color' => $this->chartColours['discount_amount'],
			'highlight_series' => 1
		);

		$legend[] = array(
			'title' => sprintf(__('%s coupons used in total', 'jigoshop'), '<strong>'.$totalCoupons.'</strong>'),
			'color' => $this->chartColours['coupon_count'],
			'highlight_series' => 0
		);

		return $legend;
	}

	public function display()
	{
		/** @noinspection PhpUnusedLocalVariableInspection */
		$ranges = array(
			'all' => __('All Time', 'jigoshop'),
			'year' => __('Year', 'jigoshop'),
			'last_month' => __('Last Month', 'jigoshop'),
			'month' => __('This Month', 'jigoshop'),
			'30day' => __('Last 30 Days', 'jigoshop'),
			'7day' => __('Last 7 Days', 'jigoshop'),
			'today' => __('Today', 'jigoshop'),
		);

		$this->calculateCurrentRange();

		Render::output('admin/reports/chart', array(
			/** TODO This is ugly... */
			'current_type' => 'discount_summary',
			'ranges' => $ranges,
			'current_range' => $this->currentRange,
			'legends' => $this->getChartLegend(),
			'widgets' => $this->getChartWidgets(),
			'group_by' => $this->chartGroupBy
		));
	}

	public function getChartWidgets()
	{
		$widgets = array();

		$widgets[] = array(
			'title' => '',
			'callback' => array($this, 'coupons_widget')
		);

		return $widgets;
	}

	public function coupons_widget()
	{
		?>
		<h4 class="section_title"><span><?php _e('Filter by coupon', 'jigoshop'); ?></span></h4>
		<div class="section">
			<form method="GET">
				<div>
					<?php
					$data = $this->get_report_data();
					$used_coupons = array();
					foreach ($data as $coupons) {
						foreach ($coupons->coupons as $coupon) {
							if(!empty($coupon)){
								if (!isset($used_coupons[$coupon['code']])) {
									$used_coupons[$coupon['code']] = $coupon;
									$used_coupons[$coupon['code']]['usage'] = 0;
								}

								$used_coupons[$coupon['code']]['usage'] += $coupons->usage[$coupon['code']];
							}
						}
					}

					if ($used_coupons) :
						?>
						<select id="coupon_codes" name="coupon_codes" class="wc-enhanced-select" data-placeholder="<?php _e('Choose coupons&hellip;', 'jigoshop'); ?>" style="width:100%;">
							<option value=""><?php _e('All coupons', 'jigoshop'); ?></option>
							<?php
							foreach ($used_coupons as $coupon) {
								echo '<option value="'.esc_attr($coupon['code']).'" '.selected(in_array($coupon['code'], $this->couponCodes), true, false).'>'.$coupon['code'].'</option>';
							}
							?>
						</select>
						<input type="submit" class="submit button" value="<?php _e('Show', 'jigoshop'); ?>" />
						<input type="hidden" name="range" value="<?php if (!empty($_GET['range'])) echo esc_attr($_GET['range']) ?>" />
						<input type="hidden" name="start_date" value="<?php if (!empty($_GET['start_date'])) echo esc_attr($_GET['start_date']) ?>" />
						<input type="hidden" name="end_date" value="<?php if (!empty($_GET['end_date'])) echo esc_attr($_GET['end_date']) ?>" />
						<input type="hidden" name="page" value="<?php if (!empty($_GET['page'])) echo esc_attr($_GET['page']) ?>" />
						<input type="hidden" name="tab" value="<?php if (!empty($_GET['tab'])) echo esc_attr($_GET['tab']) ?>" />
						<input type="hidden" name="report" value="<?php if (!empty($_GET['report'])) echo esc_attr($_GET['report']) ?>" />
					<?php else : ?>
						<span><?php _e('No used coupons found', 'jigoshop'); ?></span>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<h4 class="section_title"><span><?php _e('Most Popular', 'jigoshop'); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_popular = $used_coupons;
				usort($most_popular, function($a, $b){
					return $b['usage'] - $a['usage'];
				});
				$most_popular = array_slice($most_popular, 0, 12);

				if ($most_popular) {
					foreach ($most_popular as $coupon) {
						echo '<tr class="'.(in_array($coupon['code'], $this->couponCodes) ? 'active' : '').'">
							<td class="count" width="1%">'.$coupon['usage'].'</td>
							<td class="name"><a href="'.esc_url(add_query_arg('coupon_codes', $coupon['code'])).'">'.$coupon['code'].'</a></td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="2">'.__('No coupons found in range', 'jigoshop').'</td></tr>';
				}
				?>
			</table>
		</div>
		<h4 class="section_title"><span><?php _e('Most Discount', 'jigoshop'); ?></span></h4>
		<div class="section">
			<table cellspacing="0">
				<?php
				$most_discount = $used_coupons;
				usort($most_discount, function($a, $b){
					return $b['amount'] * $b['usage'] - $a['amount'] * $a['usage'];
				});
				$most_discount = array_slice($most_discount, 0, 12);

				if ($most_discount) {

					foreach ($most_discount as $coupon) {
						echo '<tr class="'.(in_array($coupon['code'], $this->couponCodes) ? 'active' : '').'">
							<td class="count" width="1%">'.jigoshop_price($coupon['amount'] * $coupon['usage']).'</td>
							<td class="name"><a href="'.esc_url(add_query_arg('coupon_codes', $coupon['code'])).'">'.$coupon['code'].'</a></td>
						</tr>';
					}
				} else {
					echo '<tr><td colspan="3">'.__('No coupons found in range', 'jigoshop').'</td></tr>';
				}
				?>
			</table>
		</div>
		<script type="text/javascript">
			jQuery(function($){
				$('.section_title').click(function(){
					var next_section = $(this).next('.section');
					if($(next_section).is(':visible'))
						return false;
					$('.section:visible').slideUp();
					$('.section_title').removeClass('open');
					$(this).addClass('open').next('.section').slideDown();
					return false;
				});
				$('.section').slideUp(100, function(){
					<?php if ( empty( $this->couponCodes ) ) : ?>
					$('.section_title:eq(1)').click();
					<?php else : ?>
					$('.section_title:eq(0)').click();
					<?php endif; ?>
				});
			});
		</script>
		<?php
	}

	/**
	 * Get report data
	 *
	 * @return array
	 */
	public function getReportData()
	{
		if (empty($this->reportData)) {
			$this->queryReportData();
		}

		return $this->reportData;
	}

	/**
	 * Get all data needed for this report and store in the class
	 */
	private function queryReportData()
	{
		$this->reportData = new \stdClass();
		$this->reportData->orderCoupons = $this->getOrderReportData(array(
			'data' => array(
				'order_data' => array(
					'type' => 'meta',
					'name' => 'order_coupons',
					'process' => true,
				),
				'post_date' => array(
					'type' => 'post_data',
					'function' => '',
					'name' => 'post_date'
				),
			),
			'query_type' => 'get_results',
			'filter_range' => true,
			'order_types' => array('shop_order'),
		));


		$couponCodes = $this->couponCodes;
		if(!empty($couponCcodes[0])){
			$this->reportData->orderCoupons = array_filter($this->reportData->orderCoupons, function($item) use ($couponCodes)	{
				return isset($item->usage[$couponCodes[0]]);
			});
		};

		$this->reportData->orderCouponCounts = array_map(function($item) use ($couponCodes){
			$time = new \stdClass();
			$time->post_date = $item->post_date;
			if(!empty($couponCodes))
			{
				$time->order_coupon_count = $item->usage[$couponCodes[0]];
			} else {
				$time->order_coupon_count = count($item->coupons);
			}

			return $time;
		}, $this->reportData->orderCoupons);

		$this->reportData->orderDiscountAmounts = array_map(function($item) use ($couponCodes){
			$time = new \stdClass();
			$time->post_date = $item->post_date;
			if(!empty($item->coupons)){
				$time->discount_amount = array_sum(array_map(function($innerItem) use ($item, $couponCodes){
					if(empty($innerItem)){
						return 0;
					}
					if(!empty($coupon_codes[0])) {
						return $coupon_codes[0] == $innerItem['code'] ? $item->usage[$innerItem['code']] * $innerItem['amount'] : 0;
					} else {
						return $item->usage[$innerItem['code']] * $innerItem['amount'];
					}
				}, $item->coupons));
			} else {
				$time->discount_amount = 0;
			}


			return $time;
		}, $this->reportData->orderCoupons);
	}

	public function getExportButton()
	{
		return array(
			'download' => 'report-'.esc_attr($this->currentRange).'-'.date_i18n('Y-m-d', current_time('timestamp')).'.csv',
			'xaxes' => __('Date', 'jigoshop'),
			'groupby' => $this->chartGroupBy,
		);
	}

	public function getMainChart()
	{
		global $wp_locale;

		$startTime = $this->range['start'];
		$endTime = $this->range['end'];
		$filterTimes = function($item) use ($startTime, $endTime){
			$time = strtotime($item->post_date);
			return $time >= $startTime && $time < $endTime;
		};

		// Prepare data for report
		$orderCouponCounts = $this->prepareChartData(array_filter($this->reportData->orderCouponCounts, $filterTimes), 'post_date', 'order_coupon_count', $this->chartInterval, $this->range['start'], $this->chartGroupBy);
		$orderDiscountAmounts = $this->prepareChartData(array_filter($this->reportData->orderDiscountAmounts, $filterTimes), 'post_date', 'discount_amount', $this->chartInterval, $this->range['start'], $this->chartGroupBy);

		$data = array();
		$data['series'] = array();
		$data['series'][] = $this->arrayToObject(array(
			'label' => esc_js(__('Number of coupons used', 'jigoshop')),
			'data' => array_values($orderCouponCounts),
			'color' => $this->chartColours['coupon_count' ],
			'bars' => $this->arrayToObject(array(
				'fillColor' => $this->chartColours['coupon_count'],
				'fill' => true,
				'show' => true,
				'lineWidth' => 0,
				'align' => 'center',
				'barWidth' => $this->barwidth * 0.5
			)),
			'shadowSize' => 0,
			'hoverable' => false
		));
		$data['series'][] = $this->arrayToObject(array(
			'label' => esc_js(__('Discount amount', 'jigoshop')),
			'data' => array_values($orderDiscountAmounts),
			'yaxis' => 2,
			'color' => $this->chartColours['discount_amount'],
			'points' => $this->arrayToObject(array(
				'show' => true,
				'radius' => 5,
				'lineWidth' => 4,
				'fillColor' => '#fff',
				'fill' => true,
			)),
			'lines' => $this->arrayToObject(array(
				'show' => true,
				'lineWidth' => 4,
				'fill' => false,
			)),
			'shadowSize' => 0,
			'append_tooltip' => Currency::symbol(),
		));

		$data['options'] = $this->arrayToObject(array(
			'legend' => $this->arrayToObject(array(
				'show' => false,
			)),
			'grid' => $this->arrayToObject(array(
				'color' => '#aaa',
				'borderColor' => 'transparent',
				'borderWidth' => 0,
				'hoverable' => true
			)),
			'xaxes' => array(
				$this->arrayToObject(array(
					'color' => '#aaa',
					'position' => 'bottom',
					'tickColor' => 'transparent',
					'mode' => 'time',
					'timeformat' => $this->chartGroupby == 'hour' ? '%H' : $this->chartGroupBy == 'day' ? '%d %b' : '%b',
					'monthNames' => array_values($wp_locale->month_abbrev),
					'tickLength' => 1,
					'minTickSize' => array(1, $this->chartGroupBy),
					'font' => $this->arrayToObject(array('color' => '#aaa')),
				))
			),
			'yaxes' => array(
				$this->arrayToObject(array(
					'min' => 0,
					'minTickSize' => 1,
					'tickDecimals' => 0,
					'color' => '#ecf0f1',
					'font' => $this->arrayToObject(array('color' => '#aaa'))
				)),
				$this->arrayToObject(array(
					'position' => 'right',
					'min' => 0,
					'tickDecimals' => 2,
					'alignTicksWithAxis' => 1,
					'autoscaleMargin' => 0,
					'color' => 'transparent',
					'font' => $this->arrayToObject(array('color' => '#aaa'))
				))
			),
		));
		if ($this->chartGroupBy == 'hour') {
			$data['options']->xaxes[0]->min = 0;
			$data['options']->xaxes[0]->max = 24 * 60 * 60 * 1000;
		}

		return $data;
	}

	private function getChartColors()
	{
		$this->chartColours = $this->wp->applyFilters('jigoshop/admin/reports/discount_summary/chart_colors', array(

				'discount_amount' => '#3498db',
				'coupon_count' => '#d4d9dc',
		));
	}
}