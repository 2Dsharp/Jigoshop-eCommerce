<?php
use Jigoshop\Helper\Render;

/**
 * @var $tools array List of tools to display.
 * @var $messages \Jigoshop\Core\Messages Messages container.
 */
?>
<div class="wrap jigoshop migration">
	<h1><?php _e('Jigoshop &raquo; Migration tool', 'jigoshop'); ?></h1>
	<?php settings_errors(); ?>
	<?php Render::output('shop/messages', array('messages' => $messages)); ?>
	<p class="alert alert-info"><?php _e('This panel allows you to update your old Jigoshop plugin data to new format.', 'jigoshop'); ?></p>
	<p class="alert alert-info"><?php _e('Migration is a lengthy process and depends on how much items you have in your store. Please keep patient until the process is finished.', 'jigoshop'); ?></p>
	<p class="alert alert-danger no-remove"><?php printf(__('Please create a backup of database in case of any error! <a href="%s">Here you can find instruction how to do this</a>', 'jigoshop'), 'http://codex.wordpress.org/Backing_Up_Your_Database'); ?></p>
	<ul class="list-group clearfix max-width-250">
		<?php foreach ($tools as $tool): /** @var $tool \Jigoshop\Admin\Migration\Tool */ ?>
			<li class="list-group-item tool-<?php echo $tool->getId(); ?>"><?php echo $tool->display(); ?></li>
		<?php endforeach; ?>
	</ul>
	<input type="hidden" name="page" value="<?php echo Jigoshop\Admin\Migration::NAME; ?>" />
</div>
<div class="wrap jigoshop migration_progress hidden">
	<h1 id="title"><?php _e('Jigoshop &raquo; Migration Tool &raquo; ', 'jigoshop'); ?></h1>
	<br>

	<div id="migration_alert" class="alert alert-info col-lg-12 col-sm-12 no-remove">
		<div class="row">
			<div class="col-lg-1 col-sm-2 migration_icon"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></div>
			<div class="col-lg-11 col-sm-10 padding_left_40">
				<span class="migration_header"><?php _e('Migration status', 'jigoshop'); ?><span class="migration-id"></span></span> <br/><br/>

				<div class="padding_left_10 font_md">
					<div class="row">
						<div class="col-lg-2 col-sm-3 font_bold"><?php _e('Processed', 'jigoshop'); ?></div>
						<div class="col-lg-2 col-sm-3 migration_processed"></div>
						<p class="visible-xs"></p>
					</div>
					<div class="row">
						<div class="col-lg-2 col-sm-3 font_bold"><?php _e('Remaining', 'jigoshop'); ?></div>
						<div class="col-lg-2 col-sm-3 migration_remain"></div>
						<p class="visible-xs"></p>
					</div>
					<div class="row">
						<div class="col-lg-2 col-sm-3 font_bold"><?php _e('Total', 'jigoshop'); ?></div>
						<div class="col-lg-2 col-sm-3 migration_total"></div>
						<p class="visible-xs"></p>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>

	<div class="clear"></div>
	<div class="progress parent_progress_bar simple_border">
		<div id="migration_progress_bar" class="progress-bar active progress_bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
	</div>
	<form id="back_to_mt" action="admin.php?page=<?php echo Jigoshop\Admin\Migration::NAME; ?>" method="post">
		<button type="submit" class="btn btn-primary invisible back-to-home"><?php _e('Back to Migration Tool'); ?></button>
	</form>
</div>
