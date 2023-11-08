<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Page extends Widget_Base {

  public function get_name() {
    return 'twbb_woocommerce-page';
  }

  public function get_title() {
    return __( 'Woocommerce Pages', 'nerd-builder' );
  }

  public function get_icon() {
    return 'twbb-woocommerce_pages twbb-widget-icon';
  }

  public function get_categories() {
    return [ Woocommerce::WOOCOMMERCE_GROUP ];
  }

  public function get_keywords() {
    return [ 'woocommerce', 'shop', 'store', 'product', 'page' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'section_product',
      [
        'label' => __( 'Element', 'nerd-builder' ),
      ]
    );


    $this->add_control(
      'element',
      [
        'label' => __( 'Page', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          '' => '— ' . __( 'Select', 'nerd-builder' ) . ' —',
          'woocommerce_cart' => __( 'Cart Page', 'nerd-builder' ),
          'product_page' => __( 'Single Product Page', 'nerd-builder' ),
          'woocommerce_checkout' => __( 'Checkout Page', 'nerd-builder' ),
          'woocommerce_order_tracking' => __( 'Order Tracking Form', 'nerd-builder' ),
          'woocommerce_my_account' => __( 'My Account', 'nerd-builder' ),
        ],
      ]
    );

    $this->add_control(
      'product_id',
      [
        'label' => __( 'Product', 'nerd-builder' ),
        'type' => 'TWBBSelectAjax',
        'post_type' => '',
        'options' => [],
        'label_block' => true,
        'filter_by' => 'product',
        'condition' => [
          'element' => [ 'product_page' ],
        ],
      ]
    );

    $this->end_controls_section();


    $this->start_controls_section('section_page_style', [
      'label' => __('Style', 'nerd-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);

    $this->add_control('title_color', [
      'label' => __('Title Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} h1' => 'color: {{VALUE}};',
        '{{WRAPPER}} h2' => 'color: {{VALUE}};',
        '{{WRAPPER}} h3' => 'color: {{VALUE}};',
        '{{WRAPPER}} th' => 'color: {{VALUE}};',
      ],
      'scheme' => [
        'type' => Scheme_Color::get_type(),
        'value' => Scheme_Color::COLOR_3,
      ],
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'title_typography',
      'label' => __('Title typography', 'nerd-builder'),
      'scheme' => Scheme_Typography::TYPOGRAPHY_3,
      'selector' => '{{WRAPPER}} h1, {{WRAPPER}} h2, {{WRAPPER}} h3, {{WRAPPER}} th',
    ]);

    $this->add_control('text_color', [
      'label' => __('Text Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}}' => 'color: {{VALUE}};',
      ],
      'scheme' => [
        'type' => Scheme_Color::get_type(),
        'value' => Scheme_Color::COLOR_3,
      ],
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'typography',
      'label' => __('Text typography', 'nerd-builder'),
      'scheme' => Scheme_Typography::TYPOGRAPHY_3,
    ]);

    $this->add_control('link_color', [
      'label' => __('Link Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}} a:not(.button)' => 'color: {{VALUE}};',
      ],
      'scheme' => [
        'type' => Scheme_Color::get_type(),
        'value' => Scheme_Color::COLOR_3,
      ],
    ]);

    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'link_typography',
      'label' => __( 'Link Typography', 'nerd-builder' ),
      'scheme' => Scheme_Typography::TYPOGRAPHY_1,
      'selector' => '{{WRAPPER}} a',
    ]);


    $this->end_controls_section();

    /* -------------Button section------------- */
    $this->start_controls_section(
      'section_button_style',
      [
        'label' => __( 'Button', 'nerd-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
        'selector' => '{{WRAPPER}} button, {{WRAPPER}} a.button',
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'button_border',
        'selector' => '{{WRAPPER}} button, {{WRAPPER}} a.button',
        'exclude' => [ 'color' ],
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => __( 'Border Radius', 'nerd-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
          '{{WRAPPER}} button, {{WRAPPER}} a.button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->add_control(
      'button_padding',
      [
        'label' => __( 'Padding', 'nerd-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', 'em' ],
        'selectors' => [
          '{{WRAPPER}} button, {{WRAPPER}} a.button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
      ]
    );

    $this->start_controls_tabs( 'button_style_tabs' );

    $this->start_controls_tab( 'button_style_normal',
                               [
                                 'label' => __( 'Normal', 'nerd-builder' ),
                               ]
    );
    $this->add_control(
      'button_text_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} button' => 'color: {{VALUE}}',
          '{{WRAPPER}} a.button' => 'color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'button_bg_color',
      [
        'label' => __( 'Background Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} button' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} a.button' => 'background-color: {{VALUE}}',
        ],
      ]
    );
    $this->add_control(
      'button_border_color',
      [
        'label' => __( 'Border Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} button' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} a.button' => 'border-color: {{VALUE}}',
        ],
      ]
    );
    $this->end_controls_tab();

    $this->start_controls_tab('button_style_hover', [
      'label' => __('Hover', 'nerd-builder'),
    ]);
    $this->add_control('button_text_hover_color', [
      'label' => __('Text Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} button:hover' => 'color: {{VALUE}}',
        '{{WRAPPER}} a.button:hover' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('button_hover_bg_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} button:hover' => 'background-color: {{VALUE}}',
        '{{WRAPPER}} a.button:hover' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->add_control('button_hover_border_color', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} button:hover' => 'border-color: {{VALUE}}',
        '{{WRAPPER}} a.button:hover' => 'border-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();

    /*----End of button Section------*/

    /*----Start Input Section------*/
    $this->start_controls_section(
      'section_input_style',
      [
        'label' => __( 'Input', 'nerd-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'input_typography',
        'selector' => '{{WRAPPER}} input, {{WRAPPER}} .select2-selection__rendered, {{WRAPPER}} textarea',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
      ]
    );

    $this->start_controls_tabs( 'tabs_input_colors' );

    $this->start_controls_tab(
      'tab_input_normal',
      [
        'label' => __( 'Normal', 'nerd-builder' ),
      ]
    );

    $this->add_control(
      'input_text_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_3,
        ],
        'selectors' => [
          '{{WRAPPER}} input' => 'color: {{VALUE}}',
          '{{WRAPPER}} textarea' => 'color: {{VALUE}}',
          '{{WRAPPER}} select' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'input_background_color',
      [
        'label' => __( 'Background Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} input' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} textarea' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} select' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} .select2-container--default.select2-selection--single' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} .select2-selection__rendered' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'input_border_color',
      [
        'label' => __( 'Border Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} input' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} textarea' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} .select2-selection__rendered' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_input_focus',
      [
        'label' => __( 'Focus', 'nerd-builder' ),
      ]
    );

    $this->add_control(
      'input_text_color_focus',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} input:focus' => 'color: {{VALUE}}',
          '{{WRAPPER}} textarea:focus' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'input_background_color_focus',
      [
        'label' => __( 'Background Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} input:focus' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} textarea:focus' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'input_border_color_focus',
      [
        'label' => __( 'Border Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} input:focus' => 'border-color: {{VALUE}}',
          '{{WRAPPER}} textarea:focus' => 'border-color: {{VALUE}}',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'border',
        'selector' => '{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} .select2-selection__rendered',
        'separator' => 'before',
      ]
    );

    $this->add_responsive_control(
      'border_radius',
      [
        'label' => __( 'Border Radius', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'default' => [
          'size' => 3,
          'unit' => 'px',
        ],
        'selectors' => [
          '{{WRAPPER}} input' => 'border-radius: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}} textarea' => 'border-radius: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}} select' => 'border-radius: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}} .select2-selection.select2-selection--single' => 'border-radius: {{SIZE}}{{UNIT}}',
          '{{WRAPPER}} .select2-selection__rendered' => 'border-radius: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->end_controls_section();




  }

  private function get_shortcode() {
    $settings = $this->get_settings();

    switch ( $settings['element'] ) {
      case '':
        return '';
        break;

      case 'product_page':
        if ( ! empty( $settings['product_id'] ) ) {
          $product_data = get_post( $settings['product_id'] );
          $product = ! empty( $product_data ) && in_array( $product_data->post_type, [ 'product', 'product_variation' ] ) ? wc_setup_product_data( $product_data ) : false;
        }

        if ( empty( $product ) && current_user_can( 'manage_options' ) ) {
          return __( 'Please set a valid product', 'nerd-builder' );
        }

        $this->add_render_attribute( 'shortcode', 'id', $settings['product_id'] );
        break;

      case 'woocommerce_cart':
      case 'woocommerce_checkout':
      case 'woocommerce_order_tracking':
        break;
    }

    $shortcode = sprintf( '[%s %s]', $settings['element'], $this->get_render_attribute_string( 'shortcode' ) );

    return $shortcode;
  }

  public function add_products_post_class_filter() {
    add_filter( 'post_class', [ $this, 'add_product_post_class' ] );
  }

  public function remove_products_post_class_filter() {
    remove_filter( 'post_class', [ $this, 'add_product_post_class' ] );
  }

  public function add_product_post_class( $classes ) {
    $classes[] = 'product';

    return $classes;
  }

  protected function render() {
    $shortcode = $this->get_shortcode();

    if ( empty( $shortcode ) ) {
      return;
    }

    $this->add_products_post_class_filter();

    $html = do_shortcode( $shortcode );

    if ( 'woocommerce_checkout' === $this->get_settings( 'element' ) && '<div class="woocommerce"></div>' === $html ) {
      $html = '<div class="woocommerce">' . __( 'Your cart is currently empty.', 'nerd-builder' ) . '</div>';
    }

    echo $html;

    $this->remove_products_post_class_filter();
  }

  public function render_plain_content() {
    echo $this->get_shortcode();
  }
}
\Elementor\Plugin::instance()->widgets_manager->register( new Page() );
