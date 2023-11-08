<div <?php echo $this->get_render_attribute_string('posts-container'); ?> data-params="<?php echo htmlentities(json_encode($js_params)); ?>" data-widget="<?php echo $this->get_name(); ?>"></div>

<?php
if( isset($settings['masonry']) && $settings['masonry'] == 'yes' ) {
$margin = intval($settings['masonry_column_gap']['size']);
$col = intval($settings['masonry_columns']);
$tablet_col = intval($settings['masonry_columns_tablet']);
$phone_col = intval($settings['masonry_columns_mobile']);

$width = 'calc((100% - ' . (($col - 1) * $margin) . 'px) / ' . $col . ')';
$tablet_width = 'calc((100% - ' . (($tablet_col - 1) * $margin) . 'px) / ' . $tablet_col . ')';
$phone_width = 'calc((100% - ' . (($phone_col - 1) * $margin) . 'px) / ' . $phone_col . ')';

$item_selector = '.elementor-element-' . $this->get_id() . ' .twbb-posts-masonry-container .twbb-posts-item';
?>
<style>

    <?php echo $item_selector; ?>
    {
        width:
    <?php echo $width; ?>
    }

    @media (max-width: 1024px) {
    <?php echo $item_selector; ?> {
        width: <?php echo $tablet_width; ?>
    }
    }

    @media (max-width: 767px) {
    <?php echo $item_selector; ?> {
        width: <?php echo $phone_width; ?>
    }
    }
</style>
    <?php
}