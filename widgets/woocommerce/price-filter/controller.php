<?php

namespace Tenweb_Buider\Widgets\Woocommerce;

use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Widget_Base;
use Elementor\Element_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;
use Tenweb_Builder\Widgets\Woocommerce\Products\Products;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Pricing_Filter extends Products {
  public function get_name() {
    return 'twbb_woocommerce-pricing-filter';
  }

  public function get_title() {
    return __('Pricing filter', 'nerd-builder');
  }

  public function get_icon() {
    return 'twbb-wc-pricing twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_GROUP ];
  }

  protected function register_controls() {
  }


  protected function render() {
    ob_start();
    ?>
    <form method="post" action="" class="twbb_woo_price_filter" name="twbb_woo_price_filter<?php echo rand()?>">
      <h3 class="twbb_woo_price_filter_heading"><?php _e('Price Filter', 'nerd-builder'); ?></h3>
      <div class="multiRange">
        <div class="multiRange__rangeWrap">
          <div data-idx="0" class="multiRange__range" style="left:0%">
            <div class="multiRange__handle"></div>
          </div>
          <div data-idx="1" class="multiRange__range" style="left:0%">
            <div class="multiRange__handle"></div>
          </div>
          <div data-idx="2" class="multiRange__range" style="left:100%">
            <div class="multiRange__handle"></div>
          </div>
        </div>
        <div class="multiRange__ticks">
          <div data-value="0"></div>
          <div data-value="20"></div>
          <div data-value="40"></div>
          <div data-value="50"></div>
          <div data-value="60"></div>
          <div data-value="80"></div>
          <div data-value="100"></div>
        </div>
      </div>
      <input type="hidden" name="min_price" class="price1" value="" data-minPrice="">
      <input type="hidden" name="max_price" class="price2" value="" data-maxPrice="">
      <div class="twbb_woo_price_filter-info">
        <button type="submit" class="twbb_woo_price_filter-submit">
          <a><?php _e('FILTER', 'nerd-builder'); ?></a>
        </button>
        <div class="twbb_woo_price_filter-info-price_range">
          <span><?php _e('Price', 'nerd-builder'); ?> : </span>
          <span> <span class="current_min_price">0</span><?php echo get_woocommerce_currency_symbol() ?> - </span>
          <span> <span class="current_max_price">100</span><?php echo get_woocommerce_currency_symbol() ?></span>
        </div>
      </div>
    </form>

    <?php
    $content = ob_get_clean();
    echo $content;
    add_action('wp_footer', function() {
      ?>
      <script>
        jQuery('.price1').attr('data-minPrice', <?php echo Products::$products_min_max_prices['allMinPrice'] ?>);
        jQuery('.price1').attr('value', <?php echo Products::$products_min_max_prices['currentMinPrice'] ?>);
        jQuery('.price2').attr('data-maxPrice', <?php echo Products::$products_min_max_prices['allMaxPrice'] ?>);
        jQuery('.price2').attr('value', <?php echo Products::$products_min_max_prices['currentMaxPrice'] ?>);
      </script>
      <?php

    });
  }

  public function render_plain_content() {
  }

}

\Elementor\Plugin::instance()->widgets_manager->register(new Pricing_Filter());
