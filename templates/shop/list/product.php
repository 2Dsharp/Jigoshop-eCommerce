<?php
use Jigoshop\Core\Options;
use Jigoshop\Helper\Product;

/**
 * @var $product \Jigoshop\Entity\Product Product to display.
 */
?>
<li class="product">
	<?php do_action('jigoshop\shop\list\product\before', $product); ?>
	<a class="image" href="<?php echo $product->getLink(); ?>">
		<?php do_action('jigoshop\shop\list\product\before_title', $product); ?>
		<?php if (Product::isOnSale($product)): ?>
			<span class="on-sale"><?php echo apply_filters('jigoshop\shop\list\product\sale_text', __('Sale!', 'jigoshop'), $product) ?></span>
		<?php endif; ?>
		<?php echo Product::getFeaturedImage($product, Options::IMAGE_SMALL); ?>
	</a>
	<a href="<?php echo $product->getLink(); ?>">
		<?php do_action('jigoshop\shop\list\product\before_title', $product); ?>
		<strong><?php echo $product->getName(); ?></strong>
		<?php do_action('jigoshop\shop\list\product\after_title', $product); ?>
	</a>
	<?php do_action('jigoshop\shop\list\product\before_button', $product); ?>
	<span class="price"><?php echo Product::getPriceHtml($product); ?></span>
	<?php Product::printAddToCartForm($product, 'list'); ?>
	<?php do_action('jigoshop\shop\list\product\after', $product); ?>
</li>
