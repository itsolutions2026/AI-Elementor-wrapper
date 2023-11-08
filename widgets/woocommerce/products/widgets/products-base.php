<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

abstract class Products_Base extends Widget_Base {

  protected function register_controls() {

    $this->start_controls_section('section_products_style', [
      'label' => __('Products', 'nerd-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);
    $this->add_control('wc_style_warning', [
      'type' => Controls_Manager::RAW_HTML,
      'raw' => __('The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'nerd-builder'),
      'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
    ]);
    $this->add_control('products_class', [
      'type' => Controls_Manager::HIDDEN,
      'default' => 'wc-products',
      'prefix_class' => 'elementor-',
    ]);
    $this->add_control('column_gap', [
      'label' => __('Columns Gap', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'default' => [
        'size' => 20,
      ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 100,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-column-gap: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_control('row_gap', [
      'label' => __('Rows Gap', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'default' => [
        'size' => 40,
      ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 100,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products  ul.products' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('align', [
      'label' => __('Alignment', 'nerd-builder'),
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
      'prefix_class' => 'elementor-product-loop-item--align-',
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'text-align: {{VALUE}}',
      ],
    ]);
    $this->add_control('heading_image_style', [
      'label' => __('Image', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
      'condition' => [
          'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_control('thumbnail_image_width', [
      'label' => __('Thumbnail image width', 'nerd-builder'),
      'type' => Controls_Manager::SELECT,
      'options' => $this->get_all_image_sizes(),
      'default' => 'shop_catalog%%600',
	  'render_type' => 'ui',
      'condition' => [
          'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_control('thumbnail_image_width_custom', [
	  'type' => Controls_Manager::TEXT,
	  'label_block' => false,
      'label' => __('Width', 'nerd-builder'),
      'description' => __('You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'nerd-builder'),
      'condition' => [
        'thumbnail_image_width' => 'custom',
         'hide_products_images!' => 'yes',
      ],
      'separator' => 'none',
    ]);
    $this->add_control('single_image_width', [
      'label' => __('Single image width', 'nerd-builder'),
      'type' => Controls_Manager::SELECT,
      'options' => $this->get_all_image_sizes(),
      'default' => 'shop_single%%800',
	  'render_type' => 'ui',
      'condition' => [
          'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_control('single_image_width_custom', [
      'type' => Controls_Manager::TEXT,
	  'label' => __('Width', 'nerd-builder'),
	  'label_block' => false,
	  'description' => __('You can crop the original image size to any custom size. You can also set a single value for height or width in order to keep the original size ratio.', 'nerd-builder'),
      'condition' => [
        'single_image_width' => 'custom',
        'hide_products_images!' => 'yes',
      ],
      'separator' => 'none',
    ]);
    $this->add_group_control(Group_Control_Border::get_type(), [
      'name' => 'image_border',
      'selector' => '{{WRAPPER}}.elementor-wc-products .attachment-woocommerce_thumbnail',
      'condition' => [
         'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_responsive_control('image_border_radius', [
      'label' => __('Border Radius', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', '%' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products .attachment-woocommerce_thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
      ],
      'condition' => [
         'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_responsive_control('image_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products .attachment-woocommerce_thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
      ],
      'condition' => [
         'hide_products_images!' => 'yes',
      ],
    ]);
    $this->add_control('heading_title_style', [
      'label' => __('Title', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
      'condition' => [
         'hide_products_titles!' => 'yes',
      ],
    ]);
    $this->add_control('title_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'global' => [
          'default' => Global_Colors::COLOR_PRIMARY,
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title' => 'color: {{VALUE}}',
      ],
      'condition' => [
          'hide_products_titles!' => 'yes',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'title_typography',
      'global' => [
          'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title',
      'condition' => [
          'hide_products_titles!' => 'yes',
      ],
    ]);
    $this->add_responsive_control('title_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'range' => [
        'em' => [
          'min' => 0,
          'max' => 5,
          'step' => 0.1,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
      ],
      'condition' => [
          'hide_products_titles!' => 'yes',
      ],
    ]);
    $this->add_control('heading_desc_style', [
      'label' => __('Description', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
      'condition' => [
          'hide_products_description!' => 'yes',
      ],
    ]);
    $this->add_control('desc_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'global' => [
          'default' => Global_Colors::COLOR_TEXT,
      ],
      'selectors' => [
          '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc' => 'color: {{VALUE}}',
      ],
      'condition' => [
        'hide_products_description!' => 'yes',
      ],
    ]);
      $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'desc_typography',
      'global' => [
          'default' => Global_Typography::TYPOGRAPHY_TEXT,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc',
      'condition' => [
         'hide_products_description!' => 'yes',
      ],
    ]);
    $this->add_responsive_control('desc_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'range' => [
          'em' => [
              'min' => 0,
              'max' => 5,
              'step' => 0.1,
          ],
      ],
      'selectors' => [
          '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
      ],
      'condition' => [
        'hide_products_description!' => 'yes',
      ],
    ]);
    $this->add_control('heading_rating_style', [
      'label' => __('Rating', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->add_control('star_color', [
      'label' => __('Star Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('empty_star_color', [
      'label' => __('Empty Star Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating::before' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('star_size', [
      'label' => __('Star Size', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'default' => [
        'unit' => 'em',
      ],
      'range' => [
        'em' => [
          'min' => 0,
          'max' => 4,
          'step' => 0.1,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('rating_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'range' => [
        'em' => [
          'min' => 0,
          'max' => 5,
          'step' => 0.1,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}} !important',
      ],
    ]);
    $this->add_control('heading_price_style', [
      'label' => __('Price', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->add_control('price_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'global' => [
         'default' => Global_Colors::COLOR_PRIMARY,
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .price' => 'color: {{VALUE}}',
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .price ins' => 'color: {{VALUE}}',
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .price ins .amount' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'price_typography',
      'global' => [
          'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .price',
    ]);
    $this->add_control('heading_old_price_style', [
      'label' => __('Regular Price', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->add_control('old_price_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'global' => [
         'default' => Global_Colors::COLOR_PRIMARY,
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del' => 'color: {{VALUE}}',
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del .amount' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'old_price_typography',
      'global' => [
         'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del .amount  ',
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .price del ',
    ]);
    $this->add_control('heading_button_style', [
      'label' => __('Button', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->start_controls_tabs('tabs_button_style');
    $this->start_controls_tab('tab_button_normal', [
      'label' => __('Normal', 'nerd-builder'),
    ]);
    $this->add_control('button_text_color', [
      'label' => __('Text Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'default' => '',
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'color: {{VALUE}};',
      ],
    ]);
    $this->add_control('button_background_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'background-color: {{VALUE}};',
      ],
    ]);
    $this->add_control('button_border_color', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'border-color: {{VALUE}};',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'button_typography',
      'global' => [
         'default' => Global_Typography::TYPOGRAPHY_ACCENT,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .button',
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('tab_button_hover', [
      'label' => __('Hover', 'nerd-builder'),
    ]);
    $this->add_control('button_hover_color', [
      'label' => __('Text Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'color: {{VALUE}};',
      ],
    ]);
    $this->add_control('button_hover_background_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'background-color: {{VALUE}};',
      ],
    ]);
    $this->add_control('button_hover_border_color', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover' => 'border-color: {{VALUE}};',
      ],
    ]);
    $this->end_controls_tab();
    $this->end_controls_tabs();
    $this->add_group_control(Group_Control_Border::get_type(), [
      'name' => 'button_border',
      'exclude' => [ 'color' ],
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .button',
      'separator' => 'before',
    ]);
    $this->add_control('button_border_radius', [
      'label' => __('Border Radius', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', '%' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
      ],
    ]);
    $this->add_control('button_text_padding', [
      'label' => __('Text Padding', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', 'em', '%' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
      ],
    ]);
    $this->add_responsive_control('button_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'margin-top: {{SIZE}}{{UNIT}} !important',
      ],
    ]);
    $this->add_control('heading_view_cart_style', [
      'label' => __('View Cart', 'nerd-builder'),
      'type' => Controls_Manager::HEADING,
      'separator' => 'before',
    ]);
    $this->add_control('view_cart_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products .added_to_cart' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'view_cart_typography',
      'global' => [
         'default' => Global_Typography::TYPOGRAPHY_ACCENT,
      ],
      'selector' => '{{WRAPPER}}.elementor-wc-products .added_to_cart',
    ]);
    $this->end_controls_section();
    $this->start_controls_section('section_design_box', [
      'label' => __('Box', 'nerd-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);
    $this->add_control('box_border_width', [
      'label' => __('Border Width', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px' ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 50,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
      ],
    ]);
    $this->add_control('box_border_radius', [
      'label' => __('Border Radius', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', '%' ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 200,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-radius: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('box_padding', [
      'label' => __('Box Padding', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', 'em' ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 50,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
      ],
    ]);
    $this->add_responsive_control('content_padding', [
      'label' => __('Content Padding', 'nerd-builder'),
      'type' => Controls_Manager::DIMENSIONS,
      'size_units' => [ 'px', 'em' ],
      'range' => [
        'px' => [
          'min' => 0,
          'max' => 50,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .star-rating,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .price,
		{{WRAPPER}}.elementor-wc-products ul.products li.product .add_to_cart_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
      ],
    ]);
    $this->start_controls_tabs('box_style_tabs');
    $this->start_controls_tab('classic_style_normal', [
      'label' => __('Normal', 'nerd-builder'),
    ]);
    $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
      'name' => 'box_shadow',
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product',
    ]);
    $this->add_control('box_bg_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->add_control('box_border_color', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product' => 'border-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('classic_style_hover', [
      'label' => __('Hover', 'nerd-builder'),
    ]);
    $this->add_group_control(Group_Control_Box_Shadow::get_type(), [
      'name' => 'box_shadow_hover',
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover',
    ]);
    $this->add_control('box_bg_color_hover', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->add_control('box_border_color_hover', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product:hover' => 'border-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->end_controls_tabs();
    $this->end_controls_section();
    $this->start_controls_section('section_pagination_style', [
      'label' => __('Pagination', 'nerd-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
      'condition' => [
        'paginate' => 'yes',
      ],
    ]);
    $this->add_control('pagination_spacing', [
      'label' => __('Spacing', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination' => 'margin-top: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_control('show_pagination_border', [
      'label' => __('Border', 'nerd-builder'),
      'type' => Controls_Manager::SWITCHER,
      'label_off' => __('Hide', 'nerd-builder'),
      'label_on' => __('Show', 'nerd-builder'),
      'default' => 'yes',
      'return_value' => 'yes',
      'prefix_class' => 'elementor-show-pagination-border-',
    ]);
    $this->add_control('pagination_border_color', [
      'label' => __('Border Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul' => 'border-color: {{VALUE}}',
        '{{WRAPPER}} nav.woocommerce-pagination ul li' => 'border-right-color: {{VALUE}}; border-left-color: {{VALUE}}',
      ],
      'condition' => [
        'show_pagination_border' => 'yes',
      ],
    ]);
    $this->add_control('pagination_padding', [
      'label' => __('Padding', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'range' => [
        'em' => [
          'min' => 0,
          'max' => 2,
          'step' => 0.1,
        ],
      ],
      'size_units' => [ 'em' ],
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li a, {{WRAPPER}} nav.woocommerce-pagination ul li span' => 'padding: {{SIZE}}{{UNIT}}',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'pagination_typography',
      'selector' => '{{WRAPPER}} nav.woocommerce-pagination',
    ]);
    $this->start_controls_tabs('pagination_style_tabs');
    $this->start_controls_tab('pagination_style_normal', [
      'label' => __('Normal', 'nerd-builder'),
    ]);
    $this->add_control('pagination_link_color', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li a' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('pagination_link_bg_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li a' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('pagination_style_hover', [
      'label' => __('Hover', 'nerd-builder'),
    ]);
    $this->add_control('pagination_link_color_hover', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li a:hover' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('pagination_link_bg_color_hover', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li a:hover' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->start_controls_tab('pagination_style_active', [
      'label' => __('Active', 'nerd-builder'),
    ]);
    $this->add_control('pagination_link_color_active', [
      'label' => __('Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li span.current' => 'color: {{VALUE}}',
      ],
    ]);
    $this->add_control('pagination_link_bg_color_active', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}} nav.woocommerce-pagination ul li span.current' => 'background-color: {{VALUE}}',
      ],
    ]);
    $this->end_controls_tab();
    $this->end_controls_tabs();
    $this->end_controls_section();
    $this->start_controls_section('sale_flash_style', [
      'label' => __('Sale Flash', 'nerd-builder'),
      'tab' => Controls_Manager::TAB_STYLE,
    ]);
    $this->add_control('show_onsale_flash', [
      'label' => __('Sale Flash', 'nerd-builder'),
      'type' => Controls_Manager::SWITCHER,
      'label_off' => __('Hide', 'nerd-builder'),
      'label_on' => __('Show', 'nerd-builder'),
      'separator' => 'before',
      'default' => 'yes',
      'return_value' => 'yes',
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'display: block',
      ],
    ]);
    $this->add_control('onsale_text_color', [
      'label' => __('Text Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'color: {{VALUE}}',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_text_background_color', [
      'label' => __('Background Color', 'nerd-builder'),
      'type' => Controls_Manager::COLOR,
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'background-color: {{VALUE}}',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_group_control(Group_Control_Typography::get_type(), [
      'name' => 'onsale_typography',
      'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale',
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_border_radius', [
      'label' => __('Border Radius', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'border-radius: {{SIZE}}{{UNIT}}',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_width', [
      'label' => __('Width', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'min-width: {{SIZE}}{{UNIT}};',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_height', [
      'label' => __('Height', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_horizontal_position', [
      'label' => __('Position', 'nerd-builder'),
      'type' => Controls_Manager::CHOOSE,
      'label_block' => FALSE,
      'options' => [
        'left' => [
          'title' => __('Left', 'nerd-builder'),
          'icon' => 'eicon-h-align-left',
        ],
        'right' => [
          'title' => __('Right', 'nerd-builder'),
          'icon' => 'eicon-h-align-right',
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => '{{VALUE}}',
      ],
      'selectors_dictionary' => [
        'left' => 'right: auto; left: 0',
        'right' => 'left: auto; right: 0',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->add_control('onsale_distance', [
      'label' => __('Distance', 'nerd-builder'),
      'type' => Controls_Manager::SLIDER,
      'size_units' => [ 'px', 'em' ],
      'range' => [
        'px' => [
          'min' => -20,
          'max' => 20,
        ],
        'em' => [
          'min' => -2,
          'max' => 2,
        ],
      ],
      'selectors' => [
        '{{WRAPPER}}.elementor-wc-products ul.products li.product span.onsale' => 'margin: {{SIZE}}{{UNIT}};',
      ],
      'condition' => [
        'show_onsale_flash' => 'yes',
      ],
    ]);
    $this->end_controls_section();
  }

  /**
   * Get all image sizes.
   *
   * @return array
   */
  private function get_all_image_sizes() {
    global $_wp_additional_image_sizes;
    $default_image_sizes = [ 'thumbnail', 'medium', 'medium_large', 'large' ];
    $image_sizes = [];
    foreach ( $default_image_sizes as $size ) {
      $image_sizes[$size] = [
        'width' => (int) get_option($size . '_size_w'),
        'height' => (int) get_option($size . '_size_h'),
        'crop' => (bool) get_option($size . '_crop'),
      ];
    }
    if ( $_wp_additional_image_sizes ) {
      $image_sizes = array_merge($image_sizes, $_wp_additional_image_sizes);
    }
    $sizes = [];
    if ( !empty($image_sizes) ) {
      foreach ( $image_sizes as $key => $size ) {
        $_key = $key . '%%' . $size['width'];
        $sizes[$_key] = ucwords(str_replace('_', ' ', $key)) . ' - (' . $size['width'] . 'px)';
      }
    }
    $sizes = array_merge($sizes, [ 'custom' => __('Custom', 'nerd-builder') ]);

    return $sizes;
  }
}
