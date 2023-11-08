<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class Testimonial_Carousel extends Widget_Base {
	
	private $slide_prints_count = 0;
	
	public function get_name() {
		return Builder::$prefix . '-testimonial-carousel';
	}

	public function get_title() {
		return __( 'Testimonial Carousel', 'nerd-builder' );
	}

	public function get_icon() {
		return 'twbb-testimonial-carousel twbb-widget-icon';
	}
	
	public function get_categories() {
		return ['tenweb-widgets'];
	}
	
	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$this->add_repeater_controls( $repeater );

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'nerd-builder' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->default_slides(),
			]
		);

		$this->add_control(
			'effect',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Effect', 'nerd-builder' ),
				'default' => 'slide',
				'options' => [
					'slide' => __( 'Slide', 'nerd-builder' ),
					'fade' => __( 'Fade', 'nerd-builder' ),
					'cube' => __( 'Cube', 'nerd-builder' ),
				],
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides Per View', 'nerd-builder' ),
				'options' => [ '' => __( 'Default', 'nerd-builder' ) ] + $slides_per_view,
				'condition' => [
					'effect' => 'slide',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Slides to Scroll', 'nerd-builder' ),
				'description' => __( 'Set how many slides are scrolled per swipe.', 'nerd-builder' ),
				'options' => [ '' => __( 'Default', 'nerd-builder' ) ] + $slides_per_view,
				'condition' => [
					'effect' => 'slide',
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Height', 'nerd-builder' ),
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Width', 'nerd-builder' ),
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1140,
					],
					'%' => [
						'min' => 50,
					],
				],
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_options',
			[
				'label' => __( 'Additional Options', 'nerd-builder' ),
			]
		);

		$this->add_control(
			'show_arrows',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Arrows', 'nerd-builder' ),
				'default' => 'yes',
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'label_on' => __( 'Show', 'nerd-builder' ),
				'frontend_available' => true,
				'prefix_class' => 'tenweb-arrows-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'pagination',
			[
				'label' => __( 'Pagination', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bullets',
				'options' => [
					'' => __( 'None', 'nerd-builder' ),
					'bullets' => __( 'Dots', 'nerd-builder' ),
					'fraction' => __( 'Fraction', 'nerd-builder' ),
				],
				'prefix_class' => 'tenweb-pagination-type-',
				'render_type' => 'template',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Transition Duration', 'nerd-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label' => __( 'Autoplay Speed', 'nerd-builder' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Infinite Loop', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' => __( 'Pause on Interaction', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_size',
				'default' => 'full',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slides_style',
			[
				'label' => __( 'Slides', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'label' => __( 'Space Between', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'desktop_default' => [
					'size' => 10,
				],
				'tablet_default' => [
					'size' => 10,
				],
				'mobile_default' => [
					'size' => 10,
				],
				'render_type' => 'ui',
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'margin-right: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_control(
			'slide_background_color',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_border_size',
			[
				'label' => __( 'Border Size', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'slide_border_color',
			[
				'label' => __( 'Border Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'slide_padding',
			[
				'label' => __( 'Padding', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slide_border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'%' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper .swiper-slide' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __( 'Navigation', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_arrows',
			[
				'label' => __( 'Arrows', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Size', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-swiper-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'heading_pagination',
			[
				'label' => __( 'Pagination', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_position',
			[
				'label' => __( 'Position', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
					'outside' => __( 'Outside', 'nerd-builder' ),
					'inside' => __( 'Inside', 'nerd-builder' ),
				],
				'prefix_class' => 'tenweb-pagination-position-',
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_size',
			[
				'label' => __( 'Size', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progress' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active, {{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pagination!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection([
			'of' => 'slides',
		]);

		$this->add_control(
			'skin',
			[
				'label' => __( 'Skin', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'nerd-builder' ),
					'bubble' => __( 'Bubble', 'nerd-builder' ),
				],
				'prefix_class' => 'tenweb-testimonial--skin-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image_inline',
				'options' => [
					'image_inline' => __( 'Image Inline', 'nerd-builder' ),
					'image_stacked' => __( 'Image Stacked', 'nerd-builder' ),
					'image_above' => __( 'Image Above', 'nerd-builder' ),
					'image_left' => __( 'Image Left', 'nerd-builder' ),
					'image_right' => __( 'Image Right', 'nerd-builder' ),
				],
				'prefix_class' => 'tenweb-testimonial--layout-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'nerd-builder' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'center',
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
				'prefix_class' => 'tenweb-testimonial--align-',
			]
		);
		$this->end_injection();

		$this->start_controls_section(
			'section_skin_style',
			[
				'label' => __( 'Bubble', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'skin' => 'bubble',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'alpha' => false,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label' => __( 'Padding', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => '20',
					'bottom' => '20',
					'left' => '20',
					'right' => '20',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__footer' => 'padding-top: {{TOP}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__footer' => 'padding: 0 {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border',
			[
				'label' => __( 'Border', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label' => __( 'Border Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .tenweb-testimonial__content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label' => __( 'Border Width', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__content, {{WRAPPER}} .tenweb-testimonial__content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__content:after,
					{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'border' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_injection( [
			'at' => 'before',
			'of' => 'section_navigation',
		] );

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_gap',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__footer,
					{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__footer' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__footer' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__footer' => 'padding-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__footer' => 'padding-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__text' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .tenweb-testimonial__text',
			]
		);

		$this->add_control(
			'name_title_style',
			[
				'label' => __( 'Name', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__name' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'name_typography',
				'selector' => '{{WRAPPER}} .tenweb-testimonial__name',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_control(
			'heading_title_style',
			[
				'label' => __( 'Title', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__title' => 'color: {{VALUE}}',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tenweb-testimonial__title',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[
				'label' => __( 'Image', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => __( 'Image Size', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__content:after' => 'top: calc( {{text_padding.TOP}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px );',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_stacked:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_stacked.tenweb-testimonial--align-left .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-left .tenweb-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_stacked:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_stacked.tenweb-testimonial--align-right .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-right .tenweb-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-right) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-left .tenweb-testimonial__content:after' => 'left: calc( {{text_padding.LEFT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); right:auto;',

					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above:not(.tenweb-testimonial--align-center):not(.tenweb-testimonial--align-left) .tenweb-testimonial__content:after,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-right .tenweb-testimonial__content:after' => 'right: calc( {{text_padding.RIGHT}}{{text_padding.UNIT}} + ({{SIZE}}{{UNIT}} / 2) - 8px ); left:auto;',
				],
			]
		);

		$this->add_responsive_control(
			'image_gap',
			[
				'label' => __( 'Image Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-left .tenweb-testimonial__image + cite, 
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-left .tenweb-testimonial__image + cite,
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__image + cite, 
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__image + cite' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',

					'body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_inline.tenweb-testimonial--align-right .tenweb-testimonial__image + cite, 
					 body:not(.rtl) {{WRAPPER}}.tenweb-testimonial--layout-image_above.tenweb-testimonial--align-right .tenweb-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_inline .tenweb-testimonial__image + cite,
					 body.rtl {{WRAPPER}}.tenweb-testimonial--layout-image_above .tenweb-testimonial__image + cite' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left:0;',

					'{{WRAPPER}}.tenweb-testimonial--layout-image_stacked .tenweb-testimonial__image + cite, 
					 {{WRAPPER}}.tenweb-testimonial--layout-image_left .tenweb-testimonial__image + cite,
					 {{WRAPPER}}.tenweb-testimonial--layout-image_right .tenweb-testimonial__image + cite' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'image_border',
			[
				'label' => __( 'Border', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-style: solid',
				],
			]
		);

		$this->add_control(
			'image_border_color',
			[
				'label' => __( 'Border Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_width',
			[
				'label' => __( 'Border Width', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'image_border' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .tenweb-testimonial__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->end_injection();

		$this->update_responsive_control(
			'width',
			[
				'selectors' => [
					'{{WRAPPER}}.tenweb-arrows-yes .tenweb-testimonial-carousel-swiper' => 'width: calc( {{SIZE}}{{UNIT}} - 40px )',
					'{{WRAPPER}} .tenweb-testimonial-carousel-swiper' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->update_responsive_control(
			'slides_per_view',
			[
				'condition' => null,
			]
		);

		$this->update_control(
			'slides_to_scroll',
			[
				'condition' => null,
			]
		);

		$this->remove_control( 'effect' );
		$this->remove_responsive_control( 'height' );
		$this->remove_control( 'pagination_position' );
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control(
			'content',
			[
				'label' => __( 'Content', 'nerd-builder' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'nerd-builder' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'name',
			[
				'label' => __( 'Name', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Click Here', 'nerd-builder' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Click Here', 'nerd-builder' ),
			]
		);
	}

	private function default_slides() {
		$image_src = Utils::get_placeholder_image_src();
		return [
			[
				'content' => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'nerd-builder' ),
				'name' => __( 'John Doe', 'nerd-builder' ),
				'title' => __( 'CEO', 'nerd-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'nerd-builder' ),
				'name' => __( 'John Doe', 'nerd-builder' ),
				'title' => __( 'CEO', 'nerd-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
			[
				'content' => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'nerd-builder' ),
				'name' => __( 'John Doe', 'nerd-builder' ),
				'title' => __( 'CEO', 'nerd-builder' ),
				'image' => [
					'url' => $image_src,
				],
			],
		];
	}

	private function print_cite( $slide = array(), $location = '' ) {
		if ( empty( $slide['name'] ) && empty( $slide['title'] ) ) {
			return '';
		}

		$skin = $this->get_settings( 'skin' );
		$layout = 'bubble' === $skin ? 'image_inline' : $this->get_settings( 'layout' );
		$locations_outside = [ 'image_above', 'image_right', 'image_left' ];
		$locations_inside = [ 'image_inline', 'image_stacked' ];

		$print_outside = ( 'outside' === $location && in_array( $layout, $locations_outside ) );
		$print_inside  = ( 'inside' === $location && in_array( $layout, $locations_inside ) );

		$html = '';
		if ( $print_outside || $print_inside ) {
			$html = '<cite class="tenweb-testimonial__cite">';
			if ( ! empty( $slide['name'] ) ) {
				$html .= '<span class="tenweb-testimonial__name">' . $slide['name'] . '</span>';
			}
			if ( ! empty( $slide['title'] ) ) {
				$html .= '<span class="tenweb-testimonial__title">' . $slide['title'] . '</span>';
			}
			$html .= '</cite>';
		}

		return $html;
	}

	protected function print_slide( $slide = array(), $settings = array(), $key = '' ) {
		$this->add_render_attribute( $key . '-testimonial', [
			'class' => 'tenweb-testimonial',
		] );

		if ( ! empty( $slide['image']['url'] ) ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $slide['image']['id'], 'image_size', $settings );
			if ( ! $image_src ) {
				$image_src = $slide['image']['url'];
			}

			$this->add_render_attribute( $key . '-image', [
				'src' => $image_src,
				'alt' => !empty( $slide['name'] ) ? $slide['name'] : '',
			] );
		}
		?>
		<div <?php echo $this->get_render_attribute_string( $key . '-testimonial' ); ?>>
			<?php if ( $slide['content'] ) { ?>
				<div class="tenweb-testimonial__content">
					<div class="tenweb-testimonial__text">
						<?php echo $slide['content']; ?>
					</div>
					<?php echo $this->print_cite( $slide, 'outside' ); ?>
				</div>
			<?php } ?>
			<div class="tenweb-testimonial__footer">
				<?php if ( $slide['image']['url'] ) { ?>
					<div class="tenweb-testimonial__image">
						<img <?php echo $this->get_render_attribute_string( $key . '-image' ); ?>>
					</div>
				<?php } ?>
				<?php echo $this->print_cite( $slide, 'inside' ); ?>
			</div>
		</div>
		<?php
	}
	
	function print_slider() {
		$settings = $this->get_active_settings();
		$slides_count = count( $settings['slides'] );

		$swiperObj = [
			'slides_per_view' => $settings['slides_per_view'],
			'slides_per_view_tablet' => $settings['slides_per_view_tablet'],
			'slides_per_view_mobile' => $settings['slides_per_view_mobile'],
			'slides_to_scroll' => $settings['slides_to_scroll'],
			'slides_count' => $slides_count,
			'pagination' => $settings['pagination'],
			'show_arrows' => $settings['show_arrows'],
			'speed' => $settings['speed'],
			'autoplay' => $settings['autoplay'],
			'autoplay_speed' => $settings['autoplay_speed'],
			'loop' => $settings['loop'],
			'pause_on_interaction' => $settings['pause_on_interaction'],
			'breakpoints' => [
						'space_between' => $settings['space_between'],
						'space_between_tablet' => $settings['space_between_tablet'],
						'space_between_mobile' => $settings['space_between_mobile'],
					]
		];
		
		$this->add_render_attribute( 'tenweb-testimonial-carousel-swiper', [
			'class' => 'tenweb-testimonial-carousel-swiper swiper-container swiper-container-horizontal',
			'data-settings' => json_encode($swiperObj), 
			]
		);
		?>
		<div class="tenweb-swiper">
			<div <?php echo $this->get_render_attribute_string( 'tenweb-testimonial-carousel-swiper' ); ?>>
				<div class="swiper-wrapper">
					<?php
					foreach ( $settings['slides'] as $index => $slide ) {
						$this->slide_prints_count++;
						?>
						<div class="swiper-slide">
							<?php $this->print_slide( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count ); ?>
						</div>
					<?php } ?>
				</div>
				<?php if ( 1 < $slides_count ) { ?>
					<?php if ( $settings['pagination'] ) { ?>
						<div class="swiper-pagination"></div>
					<?php } ?>
					<?php if ( $settings['show_arrows'] ) { ?>
						<div class="tenweb-swiper-button tenweb-swiper-button-prev">
							<i class="eicon-chevron-left"></i>
						</div>
						<div class="tenweb-swiper-button tenweb-swiper-button-next">
							<i class="eicon-chevron-right"></i>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$this->print_slider();
	}
}

\Elementor\Plugin::instance()->widgets_manager->register( new Testimonial_Carousel() );
