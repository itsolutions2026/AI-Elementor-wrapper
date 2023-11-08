<?php
namespace Tenweb_Builder\Widgets\Woocommerce\Products;
use Tenweb_Builder\Widgets\Woocommerce\Products\Widgets\Products_Base;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Upsell extends Products_Base {

	public function get_name() {
		return 'twbb_woocommerce-product-upsell';
	}

	public function get_title() {
		return __( 'Upsells', 'nerd-builder');
	}

	public function get_icon() {
		return 'twbb-upsellls twbb-widget-icon';
	}

	public function get_categories() {
		return [ Woocommerce::WOOCOMMERCE_BUILDER_GROUP ];
	}

	public function get_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'upsell', 'product' ];
	}

	protected function register_controls() {

    $this->start_controls_section(
      'section_upsell_content',
      [
        'label' => __( 'Upsells', 'nerd-builder' ),
      ]
    );

    $this->add_responsive_control(
      'columns',
      [
        'label' => __( 'Columns', 'nerd-builder' ),
        'type' => Controls_Manager::NUMBER,
        'prefix_class' => 'elementor-products-columns%s-',
        'default' => 4,
        'min' => 1,
        'max' => 12,
      ]
    );

    $this->add_control(
      'orderby',
      [
        'label' => __( 'Order By', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'date',
        'options' => [
          'date' => __( 'Date', 'nerd-builder' ),
          'title' => __( 'Title', 'nerd-builder' ),
          'price' => __( 'Price', 'nerd-builder' ),
          'popularity' => __( 'Popularity', 'nerd-builder' ),
          'rating' => __( 'Rating', 'nerd-builder' ),
          'rand' => __( 'Random', 'nerd-builder' ),
          'menu_order' => __( 'Menu Order', 'nerd-builder' ),
        ],
      ]
    );

    $this->add_control(
      'order',
      [
        'label' => __( 'Order', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'desc',
        'options' => [
          'asc' => __( 'ASC', 'nerd-builder' ),
          'desc' => __( 'DESC', 'nerd-builder' ),
        ],
      ]
    );

    $this->end_controls_section();

		parent::register_controls();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_design_box',
		] );

		$this->start_controls_section(
			'section_heading_style',
			[
				'label' => __( 'Heading', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_heading',
			[
				'label' => __( 'Heading', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'label_on' => __( 'Show', 'nerd-builder' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'prefix_class' => 'show-heading-',
			]
		);

		$this->add_control(
			'heading_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
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
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'heading_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}}.elementor-wc-products .products > h2',
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_text_align',
			[
				'label' => __( 'Text Align', 'nerd-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'nerd-builder' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'nerd-builder' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'nerd-builder' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'heading_spacing',
			[
				'label' => __( 'Spacing', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products .products > h2' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_heading!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	protected function render() {
    global $product;
    if ( Woocommerce::is_template_page() && Woocommerce::get_preview_product() ) {
      $product = Woocommerce::get_upsell_product();
    }
    else {
      $product = wc_get_product();
    }
    if ( empty($product) ) {
      return;
    }


		$settings = $this->get_settings_for_display();
		$limit = '-1';
		$columns = 4;
		$orderby = 'rand';
		$order = 'desc';

		if ( ! empty( $settings['columns'] ) ) {
			$columns = $settings['columns'];
		}

		if ( ! empty( $settings['orderby'] ) ) {
			$orderby = $settings['orderby'];
		}

		if ( ! empty( $settings['order'] ) ) {
			$order = $settings['order'];
		}

		woocommerce_upsell_display( $limit, $columns, $orderby, $order );
	}

	public function render_plain_content() {}
}
\Elementor\Plugin::instance()->widgets_manager->register(new Product_Upsell());
