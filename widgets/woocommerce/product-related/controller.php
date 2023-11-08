<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Tenweb_Builder\Widgets\Woocommerce\Products\Widgets\Products_Base;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

class Product_Related extends Products_Base {

  public function get_name() {
    return 'twbb-product-related';
  }

  public function get_title() {
    return __('Product Related', 'nerd-builder');
  }

  public function get_icon() {
    return 'twbb-product_related twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'related', 'similar' ];
  }

  protected function register_controls() {
    if ( !Woocommerce::get_preview_product() ) {
      $this->start_controls_section('general', [
        'label' => $this->get_title(),
      ]);
      $this->add_control('msg', [
        'type' => \Elementor\Controls_Manager::RAW_HTML,
        'raw' => Woocommerce::add_new_product_link(),
      ]);
      $this->end_controls_section();
    }
    else {
      $this->start_controls_section('section_related_products_content', [
        'label' => __('Related Products', 'nerd-builder'),
      ]);
      $this->add_control('posts_per_page', [
        'label' => __('Products Per Page', 'nerd-builder'),
        'type' => Controls_Manager::NUMBER,
        'default' => 4,
        'range' => [
          'px' => [
            'max' => 20,
          ],
        ],
      ]);
      $this->add_responsive_control('columns', [
        'label' => __('Columns', 'nerd-builder'),
        'type' => Controls_Manager::NUMBER,
        'prefix_class' => 'elementor-products-columns%s-',
        'default' => 4,
        'min' => 1,
        'max' => 12,
      ]);
      $this->add_control('orderby', [
        'label' => __('Order By', 'nerd-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'date',
        'options' => [
          'date' => __('Date', 'nerd-builder'),
          'title' => __('Title', 'nerd-builder'),
          'price' => __('Price', 'nerd-builder'),
          'popularity' => __('Popularity', 'nerd-builder'),
          'rating' => __('Rating', 'nerd-builder'),
          'rand' => __('Random', 'nerd-builder'),
          'menu_order' => __('Menu Order', 'nerd-builder'),
        ],
      ]);
      $this->add_control('order', [
        'label' => __('Order', 'nerd-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'desc',
        'options' => [
          'asc' => __('ASC', 'nerd-builder'),
          'desc' => __('DESC', 'nerd-builder'),
        ],
      ]);
      $this->end_controls_section();
      parent::register_controls();
      $this->start_injection([
                               'at' => 'before',
                               'of' => 'section_design_box',
                             ]);
      $this->start_controls_section('section_heading_style', [
        'label' => __('Heading', 'nerd-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
      ]);
      $this->add_control('show_heading', [
        'label' => __('Heading', 'nerd-builder'),
        'type' => Controls_Manager::SWITCHER,
        'label_off' => __('Hide', 'nerd-builder'),
        'label_on' => __('Show', 'nerd-builder'),
        'default' => 'yes',
        'return_value' => 'yes',
        'prefix_class' => 'show-heading-',
      ]);
      $this->add_control('heading_color', [
        'label' => __('Color', 'nerd-builder'),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_1,
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-wc-products .products > h2' => 'color: {{VALUE}}',
        ],
        'condition' => [
          'show_heading!' => '',
        ],
      ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
        'name' => 'heading_typography',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        'selector' => '{{WRAPPER}}.elementor-wc-products .products > h2',
        'condition' => [
          'show_heading!' => '',
        ],
      ]);
      $this->add_responsive_control('heading_text_align', [
        'label' => __('Text Align', 'nerd-builder'),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __('Left', 'nerd-builder'),
            'icon' => 'fa fa-align-left',
          ],
          'center' => [
            'title' => __('Center', 'nerd-builder'),
            'icon' => 'fa fa-align-center',
          ],
          'right' => [
            'title' => __('Right', 'nerd-builder'),
            'icon' => 'fa fa-align-right',
          ],
        ],
        'selectors' => [
          '{{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
        ],
        'condition' => [
          'show_heading!' => '',
        ],
      ]);
      $this->add_responsive_control('heading_spacing', [
        'label' => __('Spacing', 'nerd-builder'),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'show_heading!' => '',
        ],
      ]);
      $this->end_controls_section();
      $this->end_injection();
    }
  }

  protected function render() {
    global $product;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
      $preview_product = Woocommerce::get_preview_product();
      $product = wc_get_product($preview_product->ID);
    }
    else {
      $product = wc_get_product();
    }
    if ( !$product ) {
      return;
    }
    $settings = $this->get_settings_for_display();
    $args = [
      'posts_per_page' => 4,
      'columns' => 4,
      'orderby' => $settings['orderby'],
      'order' => $settings['order'],
    ];
    if ( !empty($settings['posts_per_page']) ) {
      $args['posts_per_page'] = $settings['posts_per_page'];
    }
    if ( !empty($settings['columns']) ) {
      $args['columns'] = $settings['columns'];
    }
    // Get visible related products then sort them at random.
    $args['related_products'] = array_filter(array_map('wc_get_product', wc_get_related_products($product->get_id(), $args['posts_per_page'], $product->get_upsell_ids())), 'wc_products_array_filter_visible');
    // Handle orderby.
    $args['related_products'] = wc_products_array_orderby($args['related_products'], $args['orderby'], $args['order']);
    wc_get_template('single-product/related.php', $args);
  }

  public function render_plain_content() {
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Product_Related());
