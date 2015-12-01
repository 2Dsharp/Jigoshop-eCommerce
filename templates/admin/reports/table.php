<?php
/**
 *
 */
?>
<div class="row">
	<div class="col-sm-6">

	</div>
	<div class="col-sm-6" style="text-align:right">
		<div class="row">
			<div class="col-xs-5 item-count"><?php printf('%s: %d', __('Items', 'jigoshop'), $total_items); ?></div>
			<div class="col-xs-7"">
				<?php if(isset($total_pages) && $total_pages > 1) : ?>
					<nav>
						<ul class="pagination">
							<?php if($active_page > 1): ?>
								<li>
									<a href="<?php echo add_query_arg('paged', $active_page - 1); ?>" aria-label="Previous">
										<span aria-hidden="true">&laquo;</span>
									</a>
								</li>
							<?php endif; ?>
							<?php $limit = $active_page + 2 > $total_pages ? $total_pages : $active_page + 2; ?>
							<?php $start = $limit - 4 > 0 ? $limit - 4 : 1; ?>
							<?php for($i = $start; $i <= $limit ; $i++) : ?>
						        <li class="<?php echo $i == $active_page ? 'active' : ''; ?>"><a href="<?php echo add_query_arg('paged', $i); ?>"><?php echo $i; ?></a></li>
							<?php endfor; ?>
							<?php if($active_page < $total_pages): ?>
							    <li>
									<a href="<?php echo add_query_arg('paged', $active_page + 1); ?>" aria-label="Next">
										<span aria-hidden="true">&raquo;</span>
									</a>
							    </li>
							<?php endif; ?>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="clear"></div>
</div>
<div class="list">
	<div class="row">
		<?php foreach ($columns as $columnKey => $columnData) : ?>
			<div class="col-sm-<?php echo $columnData['size'] ?> fix-padding visible-lg visible-md"><?php echo $columnData['name']; ?></div>
		<?php endforeach; ?>
		<div class="clear"></div>
	</div>
	<?php foreach ($items as $item) : ?>
		<div class="row">
			<?php foreach ($columns as $columnKey => $columnData) : ?>
				<div class="col-md-<?php echo $columnData['size'] ?> fix-padding">
					<div class="col-sm-6 col-xs-6 fix-padding visible-sm visible-xs">
						<?php echo $columnData['name']; ?>
					</div>
					<div class="col-md-12 col-sm-6 col-xs-6 fix-padding">
						<?php if ($columnKey == 'user_actions') : ?>
							<?php foreach ($item[$columnKey] as $action): ?>
								<a href="<?php echo $action['url']; ?>" class="btn btn-sm btn-default <?php echo $action['action']; ?>"><?php echo $action['name']; ?></a>
							<?php endforeach; ?>
						<?php else : ?>
							<?php echo $item[$columnKey] ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
			<div class="clear"></div>
		</div>
	<?php endforeach; ?>
	<div class="row">
		<?php foreach ($columns as $columnKey => $columnData) : ?>
			<div class="col-sm-<?php echo $columnData['size'] ?> fix-padding visible-lg visible-md" style="text-align:center"><?php echo $columnData['name']; ?></div>
		<?php endforeach; ?>
		<div class="clear"></div>
	</div>
</div>
<div class="row">
	<div class="col-xs-11" style="text-align:center">
		<?php if(isset($total_pages) && $total_pages > 1) : ?>
			<nav>
				<ul class="pagination">
					<?php if($active_page > 1): ?>
						<li>
							<a href="<?php echo add_query_arg('paged', $active_page - 1); ?>" aria-label="Previous">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
					<?php endif; ?>
					<?php $limit = $active_page + 2 > $total_pages ? $total_pages : $active_page + 2; ?>
					<?php $start = $limit - 4 > 0 ? $limit - 4 : 1; ?>
					<?php for($i = $start; $i <= $limit ; $i++) : ?>
						<li class="<?php echo $i == $active_page ? 'active' : ''; ?>"><a href="<?php echo add_query_arg('paged', $i); ?>"><?php echo $i; ?></a></li>
					<?php endfor; ?>
					<?php if($active_page < $total_pages): ?>
						<li>
							<a href="<?php echo add_query_arg('paged', $active_page + 1); ?>" aria-label="Next">
								<span aria-hidden="true">&raquo;</span>
							</a>
					    </li>
					<?php endif; ?>
				</ul>
			</nav>
		<?php endif; ?>
	</div>
	<div class="clear"></div>
</div>

