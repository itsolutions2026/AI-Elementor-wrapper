<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Widget_Base;
use Tenweb_Builder\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Blockquote extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . '_blockquote';
	}

	public function get_title() {
		return __( 'Blockquote', 'nerd-builder' );
	}

	public function get_icon() {
		return 'twbb-blockquotes twbb-widget-icon';
	}

	public function get_categories() {
		return [ 'tenweb-widgets' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_blockquote_content',
			[
				'label' => __( 'Blockquote', 'nerd-builder' ),
			]
		);

		$this->add_control(
			Builder::$prefix . '_blockquote_skin',
			[
				'label' => __( 'Skin', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'border' => __( 'Border', 'nerd-builder' ),
					'quotation' => __( 'Quotation', 'nerd-builder' ),
					'boxed' => __( 'Boxed', 'nerd-builder' ),
					'clean' => __( 'Clean', 'nerd-builder' ),
				],
				'default' => 'border',
				'prefix_class' => Builder::$prefix . '_elementor-blockquote--skin-',
			]
		);

		$this->add_control(
			Builder::$prefix . '_alignment',
			[
				'label' => __( 'Alignment', 'nerd-builder' ),
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
				'prefix_class' => Builder::$prefix . '_elementor-blockquote--align-',
				'condition' => [
					Builder::$prefix . '_blockquote_skin' => 'border',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			Builder::$prefix . '_blockquote_content',
			[
				'label' => __( 'Content', 'nerd-builder' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'nerd-builder' ) . '. ' . __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'nerd-builder' ),
				'placeholder' => __( 'Enter your quote', 'nerd-builder' ),
				'rows' => 10,
			]
		);

		$this->add_control(
			Builder::$prefix . '_author_name',
			[
				'label' => __( 'Author', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'John Doe', 'nerd-builder' ),
				'label_block' => false,
				'separator' => 'after',
			]
		);

		$this->add_control(
			Builder::$prefix . '_tweet_button',
			[
				'label' => __( 'Tweet Button', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'nerd-builder' ),
				'label_off' => __( 'Off', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			Builder::$prefix . '_tweet_button_view',
			[
				'label' => __( 'View', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'icon-text' => 'Icon & Text',
					'icon' => 'Icon',
					'text' => 'Text',
				],
				'prefix_class' => Builder::$prefix . '_elementor-blockquote--button-view-',
				'default' => 'icon-text',
				'render_type' => 'template',
				'condition' => [
					Builder::$prefix . '_tweet_button' => 'yes',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_tweet_button_skin',
			[
				'label' => __( 'Skin', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'classic' => 'Classic',
					'bubble' => 'Bubble',
					'link' => 'Link',
				],
				'default' => 'classic',
				'prefix_class' => Builder::$prefix . '_elementor-blockquote--button-skin-',
				'condition' => [
					Builder::$prefix . '_tweet_button' => 'yes',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_tweet_button_label',
			[
				'label' => __( 'Label', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tweet', 'nerd-builder' ),
				'label_block' => false,
				'condition' => [
					Builder::$prefix . '_tweet_button' => 'yes',
					Builder::$prefix . '_tweet_button_view!' => 'icon',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_user_name',
			[
				'label' => __( 'Username', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'placeholder' => '@username',
				'condition' => [
					Builder::$prefix . '_tweet_button' => 'yes',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_url_type',
			[
				'label' => __( 'Target URL', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'current_page' => __( 'Current Page', 'nerd-builder' ),
					'none' => __( 'None', 'nerd-builder' ),
					'custom' => __( 'Custom', 'nerd-builder' ),
				],
				'default' => 'current_page',
				'condition' => [
					Builder::$prefix . '_tweet_button' => 'yes',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_url',
			[
				'label' => __( 'Link', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'url',
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder' => __( 'https://your-link.com', 'nerd-builder' ),
				'label_block' => true,
				'condition' => [
					Builder::$prefix . '_url_type' => 'custom',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			Builder::$prefix . '_section_content_style',
			[
				'label' => __( 'Content', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			Builder::$prefix . '_content_text_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__content' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote__content',
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_content_gap',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__content +footer' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_heading_author_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Author', 'nerd-builder' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			Builder::$prefix . '_author_text_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__author' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote__author',
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_author_gap',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__author' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					Builder::$prefix . '_alignment' => 'center',
					Builder::$prefix . '_tweet_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			Builder::$prefix . '_section_button_style',
			[
				'label' => __( 'Button', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_button_size',
			[
				'label' => __( 'Size', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.5,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button' => 'font-size: calc({{SIZE}}{{UNIT}} * 10);',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem' ],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_color_source',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'official' => __( 'Official', 'nerd-builder' ),
					'custom' => __( 'Custom', 'nerd-builder' ),
				],
				'default' => 'official',
				'prefix_class' => Builder::$prefix . '_elementor-blockquote--button-color-',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			Builder::$prefix . '_tab_button_normal',
			[
				'label' => __( 'Normal', 'nerd-builder' ),
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_background_color',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button' => 'background-color: {{VALUE}}',
					'body:not(.rtl) {{WRAPPER}} .twbb_elementor-blockquote__tweet-button:before, body {{WRAPPER}}.twbb_elementor-blockquote--align-left .twbb_elementor-blockquote__tweet-button:before' => 'border-right-color: {{VALUE}}; border-left-color: transparent',
					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote__tweet-button:before, body {{WRAPPER}}.twbb_elementor-blockquote--align-right .twbb_elementor-blockquote__tweet-button:before' => 'border-left-color: {{VALUE}}; border-right-color: transparent',
				],
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
					Builder::$prefix . '_tweet_button_skin!' => 'link',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_text_color',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button' => 'color: {{VALUE}}',
				],
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			Builder::$prefix . '_tab_button_hover',
			[
				'label' => __( 'Hover', 'nerd-builder' ),
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_background_color_hover',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button:hover' => 'background-color: {{VALUE}}',

					'body:not(.rtl) {{WRAPPER}} .elementor-blockquote__tweet-button:hover:before, body {{WRAPPER}}.twbb_elementor-blockquote--align-left .twbb_elementor-blockquote__tweet-button:hover:before' => 'border-right-color: {{VALUE}}; border-left-color: transparent',

					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote__tweet-button:before, body {{WRAPPER}}.twbb_elementor-blockquote--align-right .twbb_elementor-blockquote__tweet-button:hover:before' => 'border-left-color: {{VALUE}}; border-right-color: transparent',
				],
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
					Builder::$prefix . '_tweet_button_skin!' => 'link',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_text_color_hover',
			[
				'label' => __( 'Text Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__tweet-button:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					Builder::$prefix . '_button_color_source' => 'custom',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote__tweet-button span, {{WRAPPER}} .twbb_elementor-blockquote__tweet-button i',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			Builder::$prefix . '_section_border_style',
			[
				'label' => __( 'Border', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					Builder::$prefix . '_blockquote_skin' => 'border',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_border_style' );

		$this->start_controls_tab(
			Builder::$prefix . '_tab_border_normal',
			[
				'label' => __( 'Normal', 'nerd-builder' ),
			]
		);

		$this->add_control(
			Builder::$prefix . '_border_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_border_cwidth',
			[
				'label' => __( 'Width', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .twbb_elementor-blockquote' => 'border-left-width: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote' => 'border-right-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_border_gap',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .twbb_elementor-blockquote' => 'padding-left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote' => 'padding-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			Builder::$prefix . '_tab_border_hover',
			[
				'label' => __( 'Hover', 'nerd-builder' ),
			]
		);

		$this->add_control(
			Builder::$prefix . '_border_color_hover',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_border_width_hover',
			[
				'label' => __( 'Width', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .twbb_elementor-blockquote:hover' => 'border-left-width: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote:hover' => 'border-right-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_border_gap_hover',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .twbb_elementor-blockquote:hover' => 'padding-left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .twbb_elementor-blockquote:hover' => 'padding-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			Builder::$prefix . '_border_vertical_padding',
			[
				'label' => __( 'Vertical Padding', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
				'condition' => [
					Builder::$prefix . '_blockquote_skin' => 'border',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			Builder::$prefix . '_section_box_style',
			[
				'label' => __( 'Box', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					Builder::$prefix . '_blockquote_skin' => 'boxed',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_box_padding',
			[
				'label' => __( 'Padding', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote' => 'padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_box_style' );

		$this->start_controls_tab(
			Builder::$prefix . '_tab_box_normal',
			[
				'label' => __( 'Normal', 'nerd-builder' ),
			]
		);

		$this->add_control(
			Builder::$prefix . '_box_background_color',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote',
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_box_border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_box_hover',
			[
				'label' => __( 'Hover', 'nerd-builder' ),
			]
		);

		$this->add_control(
			Builder::$prefix . '_box_background_color_hover',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border_hover',
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote:hover',
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_box_border_radius_hover',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote:hover' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_box_shadow_hover',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .twbb_elementor-blockquote:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			Builder::$prefix . '_section_quote_style',
			[
				'label' => __( 'Quote', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					Builder::$prefix . '_blockquote_skin' => 'quotation',
				],
			]
		);

		$this->add_control(
			Builder::$prefix . '_quote_text_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_quote_size',
			[
				'label' => __( 'Size', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.5,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote:before' => 'font-size: calc({{SIZE}}{{UNIT}} * 100)',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_quote_gap',
			[
				'label' => __( 'Gap', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_elementor-blockquote__content' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$tweet_button_view = $settings[Builder::$prefix . '_tweet_button_view'];
		$share_link = 'https://twitter.com/intent/tweet';

		$text = rawurlencode( $settings[Builder::$prefix . '_blockquote_content'] );

		if ( ! empty( $settings[Builder::$prefix . '_author_name'] ) ) {
			$text .= ' — ' . $settings[Builder::$prefix . '_author_name'];
		}

		$share_link = add_query_arg( 'text', $text, $share_link );

		if ( 'current_page' === $settings[Builder::$prefix . '_url_type'] ) {
			$share_link = add_query_arg( 'url', rawurlencode( home_url() . add_query_arg( false, false ) ), $share_link );
		} elseif ( 'custom' === $settings[Builder::$prefix . '_url_type'] ) {
			$share_link = add_query_arg( 'url', rawurlencode( $settings[Builder::$prefix . '_url'] ), $share_link );
		}

		if ( ! empty( $settings[Builder::$prefix . '_user_name'] ) ) {
			$user_name = $settings[Builder::$prefix . '_user_name'];
			if ( '@' === substr( $user_name, 0, 1 ) ) {
				$user_name = substr( $user_name, 1 );
			}
			$share_link = add_query_arg( 'via', rawurlencode( $user_name ), $share_link );
		}

		$this->add_render_attribute( [
			Builder::$prefix . '_blockquote_content' => [ 'class' => 'twbb_elementor-blockquote__content' ],
			Builder::$prefix . '_author_name' => [ 'class' => 'twbb_elementor-blockquote__author' ],
			Builder::$prefix . '_tweet_button_label' => [ 'class' => 'twbb_elementor-blockquote__tweet-label' ],
		] );

		$this->add_inline_editing_attributes( Builder::$prefix . '_blockquote_content' );
		$this->add_inline_editing_attributes( Builder::$prefix . '_author_name', 'none' );
		$this->add_inline_editing_attributes( Builder::$prefix . '_tweet_button_label', 'none' );
		?>
		<blockquote class="twbb_elementor-blockquote">
			<p <?php echo $this->get_render_attribute_string( Builder::$prefix . '_blockquote_content' ); ?>>
				<?php echo $settings[Builder::$prefix . '_blockquote_content']; ?>
			</p>
			<?php if ( ! empty( $settings[Builder::$prefix . '_author_name'] ) || 'yes' === $settings[Builder::$prefix . '_tweet_button'] ) : ?>
				<footer>
					<?php if ( ! empty( $settings[Builder::$prefix . '_author_name'] ) ) : ?>
						<cite <?php echo $this->get_render_attribute_string( Builder::$prefix . '_author_name' ); ?>><?php echo $settings[Builder::$prefix . '_author_name']; ?></cite>
					<?php endif ?>
					<?php if ( 'yes' === $settings[Builder::$prefix . '_tweet_button'] ) : ?>
						<a href="<?php echo esc_attr( $share_link ); ?>" class='twbb_elementor-blockquote__tweet-button' target="_blank">
							<?php if ( 'text' !== $tweet_button_view ) : ?>
								<i class="fab fa-twitter" aria-hidden="true"></i>
								<?php if ( 'icon-text' !== $tweet_button_view ) : ?>
									<span class="elementor-screen-only"><?php esc_html_e( 'Tweet', 'nerd-builder' ); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<?php if ( 'icon-text' === $tweet_button_view || 'text' === $tweet_button_view ) : ?>
								<span <?php echo $this->get_render_attribute_string( Builder::$prefix . '_tweet_button_label' ); ?>><?php echo $settings[Builder::$prefix . '_tweet_button_label']; ?></span>
							<?php endif; ?>
						</a>
					<?php endif ?>
				</footer>
			<?php endif ?>
		</blockquote>
		<?php
	}


}
\Elementor\Plugin::instance()->widgets_manager->register(new Blockquote());
