<?php
use Jigoshop\Helper\Render;
/**
 * @var $menu
 * @var $tabs
 */
?>
<div class="jigoshop">
    <div class="form-horizontal">
        <ul class="jigoshop_product_attachments nav nav-tabs" role="tablist">
            <?php foreach($menu as $id => $name) : ?>
            <li class="<?php echo $id; ?><?php echo $id == 'gallery' ? ' active' : ''; ?>">
                <a href="#<?php echo $id; ?>" data-toggle="tab"><?php echo $name; ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content">
            <?php foreach($tabs as $id => $data) : ?>
            <div class="tab-pane<?php echo $id == 'gallery' ? ' active' : ''; ?>" id="<?php echo $id; ?>">
                <?php Render::output('admin/product/attachments/'.$id, $data); ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>