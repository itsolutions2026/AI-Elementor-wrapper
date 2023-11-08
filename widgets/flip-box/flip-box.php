<?php

namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Flip_Box extends Widget_Base {

  public function get_name(){
    return 'twbb-flip-box';
  }

  public function get_title(){
    return __('Flip Box', 'nerd-builder');
  }

  public function get_icon(){
    return 'twbb-flip-box twbb-widget-icon';
  }

  public function get_categories() {
    return [ 'tenweb-widgets' ];
  }

  protected function register_controls() {

      $this->start_controls_section(
          'section_side_a_content',
          [
              'label' => __( 'Front', 'nerd-builder' ),
          ]
      );

	  $this->start_controls_tabs( 'side_a_content_tabs' );

	  $this->start_controls_tab( 'side_a_content_tab', [ 'label' => __( 'Content', 'nerd-builder' ) ] );

	  $this->add_control(
		  'graphic_element',
		  [
			  'label' => __( 'Graphic Element', 'nerd-builder' ),
			  'type' => Controls_Manager::CHOOSE,
			  'options' => [
				  'none' => [
					  'title' => __( 'None', 'nerd-builder' ),
					  'icon' => 'eicon-ban',
				  ],
				  'image' => [
					  'title' => __( 'Image', 'nerd-builder' ),
					  'icon' => 'fa fa-picture-o',
				  ],
				  'icon' => [
					  'title' => __( 'Icon', 'nerd-builder' ),
					  'icon' => 'eicon-star',
				  ],
			  ],
			  'default' => 'icon',
		  ]
	  );

	  $this->add_control(
		  'image',
		  [
			  'label' => __( 'Choose Image', 'nerd-builder' ),
			  'type' => Controls_Manager::MEDIA,
			  'default' => [
				  'url' => Utils::get_placeholder_image_src(),
			  ],
			  'dynamic' => [
				  'active' => true,
			  ],
			  'condition' => [
				  'graphic_element' => 'image',
			  ],
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Image_Size::get_type(),
		  [
			  'name' => 'image', // Actually its `image_size`
			  'default' => 'thumbnail',
			  'condition' => [
				  'graphic_element' => 'image',
			  ],
		  ]
	  );

	  $this->add_control(
		  'selected_icon',
		  [
			  'label' => __( 'Icon', 'nerd-builder' ),
			  'type' => Controls_Manager::ICONS,
			  'fa4compatibility' => 'icon',
			  'default' => [
				  'value' => 'fas fa-star',
				  'library' => 'fa-solid',
			  ],
			  'condition' => [
				  'graphic_element' => 'icon',
			  ],
		  ]
	  );

	  $this->add_control(
		  'icon_view',
		  [
			  'label' => __( 'View', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'options' => [
				  'default' => __( 'Default', 'nerd-builder' ),
				  'stacked' => __( 'Stacked', 'nerd-builder' ),
				  'framed' => __( 'Framed', 'nerd-builder' ),
			  ],
			  'default' => 'default',
			  'condition' => [
				  'graphic_element' => 'icon',
			  ],
		  ]
	  );

	  $this->add_control(
		  'icon_shape',
		  [
			  'label' => __( 'Shape', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'options' => [
				  'circle' => __( 'Circle', 'nerd-builder' ),
				  'square' => __( 'Square', 'nerd-builder' ),
			  ],
			  'default' => 'circle',
			  'condition' => [
				  'icon_view!' => 'default',
				  'graphic_element' => 'icon',
			  ],
		  ]
	  );

	  $this->add_control(
		  'title_text_a',
		  [
			  'label' => __( 'Title & Description', 'nerd-builder' ),
			  'type' => Controls_Manager::TEXT,
			  'default' => __( 'This is the heading', 'nerd-builder' ),
			  'placeholder' => __( 'Enter your title', 'nerd-builder' ),
			  'dynamic' => [
				  'active' => true,
			  ],
			  'label_block' => true,
			  'separator' => 'before',
		  ]
	  );

	  $this->add_control(
		  'description_text_a',
		  [
			  'label' => __( 'Description', 'nerd-builder' ),
			  'type' => Controls_Manager::TEXTAREA,
			  'default' => __( 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'nerd-builder' ),
			  'placeholder' => __( 'Enter your description', 'nerd-builder' ),
			  'separator' => 'none',
			  'dynamic' => [
				  'active' => true,
			  ],
			  'rows' => 10,
			  'show_label' => false,
		  ]
	  );

	  $this->end_controls_tab();

	  $this->start_controls_tab( 'side_a_background_tab', [ 'label' => __( 'Background', 'nerd-builder' ) ] );

	  $this->add_group_control(
		  Group_Control_Background::get_type(),
		  [
			  'name' => 'background_a',
			  'types' => [ 'classic', 'gradient' ],
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__front',
		  ]
	  );

	  $this->add_control(
		  'background_overlay_a',
		  [
			  'label' => __( 'Background Overlay', 'nerd-builder' ),
			  'type' => Controls_Manager::COLOR,
			  'default' => '',
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay' => 'background-color: {{VALUE}};',
			  ],
			  'separator' => 'before',
			  'condition' => [
				  'background_a_image[id]!' => '',
			  ],
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Css_Filter::get_type(),
		  [
			  'name' => 'background_overlay_a_filters',
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay',
			  'condition' => [
				  'background_overlay_a!' => '',
			  ],
		  ]
	  );

	  $this->add_control(
		  'background_overlay_a_blend_mode',
		  [
			  'label' => __( 'Blend Mode', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'options' => [
				  '' => __( 'Normal', 'nerd-builder' ),
				  'multiply' => 'Multiply',
				  'screen' => 'Screen',
				  'overlay' => 'Overlay',
				  'darken' => 'Darken',
				  'lighten' => 'Lighten',
				  'color-dodge' => 'Color Dodge',
				  'color-burn' => 'Color Burn',
				  'hue' => 'Hue',
				  'saturation' => 'Saturation',
				  'color' => 'Color',
				  'exclusion' => 'Exclusion',
				  'luminosity' => 'Luminosity',
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay' => 'mix-blend-mode: {{VALUE}}',
			  ],
			  'condition' => [
				  'background_overlay_a!' => '',
			  ],
		  ]
	  );

	  $this->end_controls_tab();

	  $this->end_controls_tabs();

	  $this->end_controls_section();

	  $this->start_controls_section(
		  'section_side_b_content',
		  [
			  'label' => __( 'Back', 'nerd-builder' ),
		  ]
	  );

	  $this->start_controls_tabs( 'side_b_content_tabs' );

	  $this->start_controls_tab( 'side_b_content_tab', [ 'label' => __( 'Content', 'nerd-builder' ) ] );

	  $this->add_control(
		  'title_text_b',
		  [
			  'label' => __( 'Title & Description', 'nerd-builder' ),
			  'type' => Controls_Manager::TEXT,
			  'default' => __( 'This is the heading', 'nerd-builder' ),
			  'placeholder' => __( 'Enter your title', 'nerd-builder' ),
			  'dynamic' => [
				  'active' => true,
			  ],
			  'label_block' => true,
		  ]
	  );

	  $this->add_control(
		  'description_text_b',
		  [
			  'label' => __( 'Description', 'nerd-builder' ),
			  'type' => Controls_Manager::TEXTAREA,
			  'default' => __( 'Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'nerd-builder' ),
			  'placeholder' => __( 'Enter your description', 'nerd-builder' ),
			  'separator' => 'none',
			  'dynamic' => [
				  'active' => true,
			  ],
			  'rows' => 10,
			  'show_label' => false,
		  ]
	  );

	  $this->add_control(
		  'button_text',
		  [
			  'label' => __( 'Button Text', 'nerd-builder' ),
			  'type' => Controls_Manager::TEXT,
			  'default' => __( 'Click Here', 'nerd-builder' ),
			  'dynamic' => [
				  'active' => true,
			  ],
			  'separator' => 'before',
		  ]
	  );

	  $this->add_control(
		  'link',
		  [
			  'label' => __( 'Link', 'nerd-builder' ),
			  'type' => Controls_Manager::URL,
			  'dynamic' => [
				  'active' => true,
			  ],
			  'placeholder' => __( 'https://your-link.com', 'nerd-builder' ),
		  ]
	  );

	  $this->add_control(
		  'link_click',
		  [
			  'label' => __( 'Apply Link On', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'options' => [
				  'box' => __( 'Whole Box', 'nerd-builder' ),
				  'button' => __( 'Button Only', 'nerd-builder' ),
			  ],
			  'default' => 'button',
			  'condition' => [
				  'link[url]!' => '',
			  ],
		  ]
	  );

	  $this->end_controls_tab();

	  $this->start_controls_tab( 'side_b_background_tab', [ 'label' => __( 'Background', 'nerd-builder' ) ] );

	  $this->add_group_control(
		  Group_Control_Background::get_type(),
		  [
			  'name' => 'background_b',
			  'types' => [ 'classic', 'gradient' ],
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__back',
		  ]
	  );

	  $this->add_control(
		  'background_overlay_b',
		  [
			  'label' => __( 'Background Overlay', 'nerd-builder' ),
			  'type' => Controls_Manager::COLOR,
			  'default' => '',
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay' => 'background-color: {{VALUE}};',
			  ],
			  'separator' => 'before',
			  'condition' => [
				  'background_b_image[id]!' => '',
			  ],
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Css_Filter::get_type(),
		  [
			  'name' => 'background_overlay_b_filters',
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay',
			  'condition' => [
				  'background_overlay_b!' => '',
			  ],
		  ]
	  );

	  $this->add_control(
		  'background_overlay_b_blend_mode',
		  [
			  'label' => __( 'Blend Mode', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'options' => [
				  '' => __( 'Normal', 'nerd-builder' ),
				  'multiply' => 'Multiply',
				  'screen' => 'Screen',
				  'overlay' => 'Overlay',
				  'darken' => 'Darken',
				  'lighten' => 'Lighten',
				  'color-dodge' => 'Color Dodge',
				  'color-burn' => 'Color Burn',
				  'hue' => 'Hue',
				  'saturation' => 'Saturation',
				  'color' => 'Color',
				  'exclusion' => 'Exclusion',
				  'luminosity' => 'Luminosity',
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay' => 'mix-blend-mode: {{VALUE}}',
			  ],
			  'condition' => [
				  'background_overlay_b!' => '',
			  ],
		  ]
	  );

	  $this->end_controls_tab();

	  $this->end_controls_tabs();

	  $this->end_controls_section();

	  $this->start_controls_section(
		  'section_box_settings',
		  [
			  'label' => __( 'Settings', 'nerd-builder' ),
		  ]
	  );

	  $this->add_responsive_control(
		  'height',
		  [
			  'label' => __( 'Height', 'nerd-builder' ),
			  'type' => Controls_Manager::SLIDER,
			  'range' => [
				  'px' => [
					  'min' => 100,
					  'max' => 1000,
				  ],
				  'vh' => [
					  'min' => 10,
					  'max' => 100,
				  ],
			  ],
			  'size_units' => [ 'px', 'vh' ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box' => 'height: {{SIZE}}{{UNIT}};',
			  ],
		  ]
	  );

	  $this->add_control(
		  'border_radius',
		  [
			  'label' => __( 'Border Radius', 'nerd-builder' ),
			  'type' => Controls_Manager::SLIDER,
			  'size_units' => [ 'px', '%' ],
			  'default' => [
				  'unit' => 'px',
				  'size' => 10,
			  ],
			  'range' => [
				  'px' => [
					  'min' => 0,
					  'max' => 200,
				  ],
			  ],
			  'separator' => 'after',
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__layer, {{WRAPPER}} .tenweb-flip-box__layer__overlay' => 'border-radius: {{SIZE}}{{UNIT}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'flip_effect',
		  [
			  'label' => __( 'Flip Effect', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'default' => 'flip',
			  'options' => [
				  'flip' => 'Flip',
				  'slide' => 'Slide',
				  'push' => 'Push',
				  'zoom-in' => 'Zoom In',
				  'zoom-out' => 'Zoom Out',
				  'fade' => 'Fade',
			  ],
			  'prefix_class' => 'tenweb-flip-box--effect-',
		  ]
	  );

	  $this->add_control(
		  'flip_direction',
		  [
			  'label' => __( 'Flip Direction', 'nerd-builder' ),
			  'type' => Controls_Manager::SELECT,
			  'default' => 'up',
			  'options' => [
				  'left' => __( 'Left', 'nerd-builder' ),
				  'right' => __( 'Right', 'nerd-builder' ),
				  'up' => __( 'Up', 'nerd-builder' ),
				  'down' => __( 'Down', 'nerd-builder' ),
			  ],
			  'condition' => [
				  'flip_effect!' => [
					  'fade',
					  'zoom-in',
					  'zoom-out',
				  ],
			  ],
			  'prefix_class' => 'tenweb-flip-box--direction-',
		  ]
	  );

	  $this->add_control(
		  'flip_3d',
		  [
			  'label' => __( '3D Depth', 'nerd-builder' ),
			  'type' => Controls_Manager::SWITCHER,
			  'label_on' => __( 'On', 'nerd-builder' ),
			  'label_off' => __( 'Off', 'nerd-builder' ),
			  'return_value' => 'tenweb-flip-box--3d',
			  'default' => 'tenweb-flip-box--3d',
			  'prefix_class' => '',
			  'condition' => [
				  'flip_effect' => 'flip',
			  ],
		  ]
	  );

	  $this->end_controls_section();

	  $this->start_controls_section(
	      'section_style_a',
          [
            'label' => __( 'Front', 'nerd-builder' ),
            'tab' => Controls_Manager::TAB_STYLE,
          ]
      );

	  $this->add_responsive_control(
		  'padding_a',
		  [
			  'label' => __( 'Padding', 'nerd-builder' ),
			  'type' => Controls_Manager::DIMENSIONS,
			  'size_units' => [ 'px', 'em', '%' ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			  ],
		  ]
	  );

	  $this->add_control(
		  'alignment_a',
		  [
			  'label' => __( 'Alignment', 'nerd-builder' ),
			  'type' => Controls_Manager::CHOOSE,
			  'label_block' => false,
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
			  'default' => 'center',
			  'prefix_class' => 'tenweb-flip-box__front-align-',
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay' => 'text-align: {{VALUE}}',
			  ],
		  ]
	  );

	  $this->add_control(
		  'vertical_position_a',
		  [
			  'label' => __( 'Vertical Position', 'nerd-builder' ),
			  'type' => Controls_Manager::CHOOSE,
			  'label_block' => false,
			  'options' => [
				  'top' => [
					  'title' => __( 'Top', 'nerd-builder' ),
					  'icon' => 'eicon-v-align-top',
				  ],
				  'middle' => [
					  'title' => __( 'Middle', 'nerd-builder' ),
					  'icon' => 'eicon-v-align-middle',
				  ],
				  'bottom' => [
					  'title' => __( 'Bottom', 'nerd-builder' ),
					  'icon' => 'eicon-v-align-bottom',
				  ],
			  ],
			  'default' => 'middle',
			  'selectors_dictionary' => [
				  'top' => 'flex-start',
				  'middle' => 'center',
				  'bottom' => 'flex-end',
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__overlay' => 'justify-content: {{VALUE}}',
			  ],
			  'separator' => 'after',
		  ]
	  );

	  $this->add_group_control(
		  Group_Control_Border::get_type(),
		  [
			  'name' => 'border_a',
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__front',
			  'separator' => 'before',
		  ]
	  );

	  $this->add_control(
	      'heading_image_style',
          [
              'type' => Controls_Manager::HEADING,
              'label' => __( 'Image', 'nerd-builder' ),
              'condition' => [
                 'graphic_element' => 'image',
              ],
              'separator' => 'before',
          ]
      );

    $this->add_control(
      'image_spacing',
      [
        'label' => __( 'Spacing', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'graphic_element' => 'image',
        ],
      ]
    );

    $this->add_control(
      'image_width',
      [
        'label' => __( 'Size (%)', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ '%' ],
        'default' => [
          'unit' => '%',
        ],
        'range' => [
          '%' => [
            'min' => 5,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__image img' => 'width: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'graphic_element' => 'image',
        ],
      ]
    );

    $this->add_control(
      'image_opacity',
      [
        'label' => __( 'Opacity', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 1,
        ],
        'range' => [
          'px' => [
            'max' => 1,
            'min' => 0.10,
            'step' => 0.01,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__image' => 'opacity: {{SIZE}};',
        ],
        'condition' => [
          'graphic_element' => 'image',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'image_border',
        'selector' => '{{WRAPPER}} .tenweb-flip-box__image img',
        'condition' => [
          'graphic_element' => 'image',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'image_border_radius',
      [
        'label' => __( 'Border Radius', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 200,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__image img' => 'border-radius: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'graphic_element' => 'image',
        ],
      ]
    );

    $this->add_control(
      'heading_icon_style',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Icon', 'nerd-builder' ),
        'condition' => [
          'graphic_element' => 'icon',
        ],
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'icon_spacing',
      [
        'label' => __( 'Spacing', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'unit' => 'px'
        ],
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );

    $this->add_control(
      'icon_primary_color',
      [
        'label' => __( 'Primary Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
        ],
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );

    $this->add_control(
      'icon_secondary_color',
      [
        'label' => __( 'Secondary Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'graphic_element' => 'icon',
          'icon_view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'icon_size',
      [
        'label' => __( 'Icon Size', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'unit' => 'px'
        ],
        'range' => [
          'px' => [
            'min' => 6,
            'max' => 300,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );

    $this->add_control(
      'icon_padding',
      [
        'label' => __( 'Icon Padding', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
          '{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
        ],
        'range' => [
          'em' => [
            'min' => 0,
            'max' => 5,
          ],
        ],
        'condition' => [
          'graphic_element' => 'icon',
          'icon_view!' => 'default',
        ],
      ]
    );

    $this->add_control(
      'icon_rotate',
      [
        'label' => __( 'Icon Rotate', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 0,
          'unit' => 'deg',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
        ],
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );

    $this->add_control(
      'icon_border_width',
      [
        'label' => __( 'Border Width', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'selectors' => [
          '{{WRAPPER}} .elementor-icon' => 'border-width: {{SIZE}}{{UNIT}}',
        ],
        'condition' => [
          'graphic_element' => 'icon',
          'icon_view' => 'framed',
        ],
      ]
    );

    $this->add_control(
      'icon_border_radius',
      [
        'label' => __( 'Border Radius', 'nerd-builder' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
          '{{WRAPPER}} .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        'condition' => [
          'graphic_element' => 'icon',
          'icon_view!' => 'default',
        ],
      ]
    );

    $this->add_control(
      'heading_title_style_a',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Title', 'nerd-builder' ),
        'separator' => 'before',
        'condition' => [
          'title_text_a!' => '',
        ],
      ]
    );

    $this->add_control(
      'title_spacing_a',
      [
        'label' => __( 'Spacing', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'description_text_a!' => '',
          'title_text_a!' => '',
        ],
      ]
    );

    $this->add_control(
      'title_color_a',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__title' => 'color: {{VALUE}}',

        ],
        'condition' => [
          'title_text_a!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'title_typography_a',
        'global' => [
	        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
        ],
        'selector' => '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__title',
        'condition' => [
          'title_text_a!' => '',
        ],
      ]
    );

    $this->add_control(
      'heading_description_style_a',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Description', 'nerd-builder' ),
        'separator' => 'before',
        'condition' => [
          'description_text_a!' => '',
        ],
      ]
    );

    $this->add_control(
      'description_color_a',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__description' => 'color: {{VALUE}}',

        ],
        'condition' => [
          'description_text_a!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'description_typography_a',
        'global' => [
	        'default' => Global_Typography::TYPOGRAPHY_TEXT,
        ],
        'selector' => '{{WRAPPER}} .tenweb-flip-box__front .tenweb-flip-box__layer__description',
        'condition' => [
          'description_text_a!' => '',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_style_b',
      [
        'label' => __( 'Back', 'nerd-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

	  $this->add_responsive_control(
		  'padding_b',
		  [
			  'label' => __( 'Padding', 'nerd-builder' ),
			  'type' => Controls_Manager::DIMENSIONS,
			  'size_units' => [ 'px', 'em', '%' ],
			  'selectors' => [
				  '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			  ],
		  ]
	  );

    $this->add_control(
      'alignment_b',
      [
        'label' => __( 'Alignment', 'nerd-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
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
        'default' => 'center',
        'prefix_class' => 'tenweb-flip-box__back-align-',
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay' => 'text-align: {{VALUE}}',
          '{{WRAPPER}} .tenweb-flip-box__button' => 'margin-{{VALUE}}: 0',
        ],
      ]
    );

    $this->add_control(
      'vertical_position_b',
      [
        'label' => __( 'Vertical Position', 'nerd-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'top' => [
            'title' => __( 'Top', 'nerd-builder' ),
            'icon' => 'eicon-v-align-top',
          ],
          'middle' => [
            'title' => __( 'Middle', 'nerd-builder' ),
            'icon' => 'eicon-v-align-middle',
          ],
          'bottom' => [
            'title' => __( 'Bottom', 'nerd-builder' ),
            'icon' => 'eicon-v-align-bottom',
          ],
        ],
        'default' => 'middle',
        'selectors_dictionary' => [
          'top' => 'flex-start',
          'middle' => 'center',
          'bottom' => 'flex-end',
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__overlay' => 'justify-content: {{VALUE}}',
        ],
        'separator' => 'after',
      ]
    );

	  $this->add_group_control(
		  Group_Control_Border::get_type(),
		  [
			  'name' => 'border_b',
			  'selector' => '{{WRAPPER}} .tenweb-flip-box__back',
			  'separator' => 'before',
		  ]
	  );

    $this->add_control(
      'heading_title_style_b',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Title', 'nerd-builder' ),
        'separator' => 'before',
        'condition' => [
          'title_text_b!' => '',
        ],
      ]
    );

    $this->add_control(
      'title_spacing_b',
      [
        'label' => __( 'Spacing', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'title_text_b!' => '',
        ],
      ]
    );

    $this->add_control(
      'title_color_b',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__title' => 'color: {{VALUE}}',

        ],
        'condition' => [
          'title_text_b!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'title_typography_b',
        'global' => [
	        'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
        ],
        'selector' => '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__title',
        'condition' => [
          'title_text_b!' => '',
        ],
      ]
    );

    $this->add_control(
      'heading_description_style_b',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Description', 'nerd-builder' ),
        'separator' => 'before',
        'condition' => [
          'description_text_b!' => '',
        ],
      ]
    );

    $this->add_control(
      'description_spacing_b',
      [
        'label' => __( 'Spacing', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
        ],
        'condition' => [
          'description_text_b!' => '',
          'button_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'description_color_b',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__description' => 'color: {{VALUE}}',

        ],
        'condition' => [
          'description_text_b!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'description_typography_b',
        'global' => [
	        'default' => Global_Typography::TYPOGRAPHY_TEXT,
        ],
        'selector' => '{{WRAPPER}} .tenweb-flip-box__back .tenweb-flip-box__layer__description',
        'condition' => [
          'description_text_b!' => '',
        ],
      ]
    );

    $this->add_control(
      'heading_button',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Button', 'nerd-builder' ),
        'separator' => 'before',
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_size',
      [
        'label' => __( 'Size', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'xs',
        'options' => [
          'xs' => __( 'Extra Small', 'nerd-builder' ),
          'sm' => __( 'Small', 'nerd-builder' ),
          'md' => __( 'Medium', 'nerd-builder' ),
          'lg' => __( 'Large', 'nerd-builder' ),
          'xl' => __( 'Extra Large', 'nerd-builder' ),
        ],
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'button_typography',
        'selector' => '{{WRAPPER}} .tenweb-flip-box__button',
        'global' => [
	        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
        ],
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->start_controls_tabs( 'button_tabs' );

    $this->start_controls_tab( 'normal',
      [
        'label' => __( 'Normal', 'nerd-builder' ),
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_text_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_background_color',
      [
        'label' => __( 'Background Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_border_color',
      [
          'label' => __( 'Border Color', 'nerd-builder' ),
          'type' => Controls_Manager::COLOR,
          'selectors' => [
              '{{WRAPPER}} .tenweb-flip-box__button' => 'border-color: {{VALUE}};',
          ],
      ]
    );

    $this->end_controls_tab();

    $this->start_controls_tab(
      'hover',
      [
        'label' => __( 'Hover', 'nerd-builder' ),
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_hover_text_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_background_color',
      [
        'label' => __( 'Background Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button:hover' => 'background-color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'button_hover_border_color',
      [
        'label' => __( 'Border Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button:hover' => 'border-color: {{VALUE}};',
        ],
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->add_group_control(
      Group_Control_Border::get_type(),
      [
        'name' => 'button_border',
        'selector' => '{{WRAPPER}} .tenweb-flip-box__button',
        'separator' => 'before',
        'condition' => [
          'button_text!' => '',
        ],
      ]
    );

    $this->add_control(
      'button_border_radius',
      [
        'label' => __( 'Border Radius', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 100,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .tenweb-flip-box__button' => 'border-radius: {{SIZE}}{{UNIT}};',
        ],
        'separator' => 'after',
      ]
    );

    $this->end_controls_section();

  }

  protected function render() {
    $settings = $this->get_settings_for_display();
    $wrapper_tag = 'div';
    $button_tag = 'a';
	$migration_allowed = Icons_Manager::is_migration_allowed();
    $this->add_render_attribute( 'button', 'class', [
        'tenweb-flip-box__button',
        'elementor-button',
        'tenweb-button',
        'elementor-size-' . $settings['button_size'],
      ]
    );

    $this->add_render_attribute( 'wrapper', 'class', 'tenweb-flip-box__layer tenweb-flip-box__back' );
    if ( ! empty( $settings['link']['url'] ) ) {
      $link_element = 'button';

      if ( 'box' === $settings['link_click'] ) {
          $wrapper_tag = 'a';
          $button_tag = 'span';
          $link_element = 'wrapper';
      }

      $this->add_link_attributes( $link_element, $settings['link'] );
    }

    if ( 'icon' === $settings['graphic_element'] ) {
      $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-icon-wrapper' );
      $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-view-' . $settings['icon_view'] );
      if ( 'default' != $settings['icon_view'] ) {
          $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-shape-' . $settings['icon_shape'] );
      }

      if ( ! isset( $settings['icon'] ) && ! $migration_allowed ) {
          // add old default
          $settings['icon'] = 'fa fa-star';
      }

      if ( ! empty( $settings['icon'] ) ) {
          $this->add_render_attribute( 'icon', 'class', $settings['icon'] );
      }
    }

    $has_icon = ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] );
    $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
    $is_new = empty( $settings['icon'] ) && $migration_allowed;

    ?>
    <div class="tenweb-flip-box">
      <div class="tenweb-flip-box__layer tenweb-flip-box__front">
        <div class="tenweb-flip-box__layer__overlay">
          <div class="tenweb-flip-box__layer__inner">
              <?php if ( 'image' === $settings['graphic_element'] && ! empty( $settings['image']['url'] ) ) : ?>
                  <div class="elementor-flip-box__image">
                      <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings ); ?>
                  </div>
              <?php elseif ( 'icon' === $settings['graphic_element'] && $has_icon ) : ?>
                  <div <?php echo $this->get_render_attribute_string( 'icon-wrapper' ); ?>>
                      <div class="elementor-icon tenweb-icon">
                          <?php if ( $is_new || $migrated ) :
                              Icons_Manager::render_icon( $settings['selected_icon'] );
                          else : ?>
                              <i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i>
                          <?php endif; ?>
                      </div>
                  </div>
              <?php endif;

            if ( !empty( $settings['title_text_a'] ) ) {
            ?>
              <h3 class="tenweb-flip-box__layer__title">
                <?php echo $settings['title_text_a']; ?>
              </h3>
            <?php
            }

            if ( !empty( $settings['description_text_a'] ) ) {
            ?>
              <div class="tenweb-flip-box__layer__description">
                <?php echo $settings['description_text_a']; ?>
              </div>
            <?php
            }
            ?>
          </div>
        </div>
      </div>
        <<?php echo $wrapper_tag; ?> <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
        <div class="tenweb-flip-box__layer__overlay">
          <div class="tenweb-flip-box__layer__inner">
            <?php
            if ( !empty( $settings['title_text_b'] ) ) {
            ?>
              <h3 class="tenweb-flip-box__layer__title">
                <?php echo $settings['title_text_b']; ?>
              </h3>
            <?php
            }

            if ( !empty( $settings['description_text_b'] ) ) {
            ?>
              <div class="tenweb-flip-box__layer__description">
                <?php echo $settings['description_text_b']; ?>
              </div>
            <?php
            }

            if ( !empty( $settings['button_text'] ) ) {
            ?>
            <<?php echo $button_tag; ?> <?php echo $this->get_render_attribute_string( 'button' ); ?>>
              <?php echo $settings['button_text']; ?>
            </<?php echo $button_tag; ?>>
            <?php
            }
            ?>
        </div>
      </div>
      </<?php echo $wrapper_tag; ?>>
    </div>
    <?php
  }

}

\Elementor\Plugin::instance()->widgets_manager->register(new Flip_Box());
