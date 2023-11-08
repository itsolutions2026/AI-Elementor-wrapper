<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Call_To_Action extends Widget_Base {

	public function get_name() {
		return Builder::$prefix .'_call-to-action';
	}

	public function get_title() {
		return __('Call to Action', 'nerd-builder');
	}

	public function get_icon() {
		return 'twbb-call-to-action twbb-widget-icon';
	}

	public function get_categories(){
		return ['tenweb-widgets'];
	}

	protected function register_controls() {
		/* start Image section */
		$this->start_controls_section( 'section_main_image',
			[
				'label' => __('Image', 'nerd-builder'),
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_position',
			[
				'label' => __('Position', 'nerd-builder'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'nerd-builder'),
						'icon'  => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'nerd-builder'),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __('Right', 'nerd-builder'),
						'icon'  => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => __('Bottom', 'nerd-builder'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'desktop_default' => 'left',
				'tablet_default' => 'top',
				'mobile_default' => 'top',
				'prefix_class' => Builder::$prefix .'_cta%s-position-image-'
			]
		);

    $this->add_control(
      'graphic_element',
      [
        'label' => __( 'Graphic Element', 'nerd-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'none' => [
            'title' => __( 'None', 'nerd-builder' ),
            'icon' => 'eicon-ban',
          ],
          Builder::$prefix . '_bg_image' => [
            'title' => __( 'Image', 'nerd-builder' ),
            'icon' => 'fa fa-picture-o',
          ],
          'icon' => [
            'title' => __( 'Icon', 'nerd-builder' ),
            'icon' => 'eicon-star',
          ],
        ],
        'default' => Builder::$prefix . '_bg_image',
      ]
    );

    $this->add_control(
      Builder::$prefix . '_bg_image',
      [
        'label' => __( 'Choose Image', 'nerd-builder' ),
        'type' => Controls_Manager::MEDIA,
        'default' => [
          'url' => Utils::get_placeholder_image_src(),
        ],
        'dynamic' => [
          'active' => true,
        ],
        'show_label' => false,
        'condition' => [
          'graphic_element' => Builder::$prefix . '_bg_image',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Image_Size::get_type(),
      [
        'name' => Builder::$prefix .'_bg_image', // Actually its `image_size`
        'label' => __('Image Resolution', 'nerd-builder'),
        'default' => 'large',
        'condition' => [
          'graphic_element' => Builder::$prefix . '_bg_image',
          Builder::$prefix . '_bg_image[id]!' => '',
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


		$this->end_controls_section();
	/* end Image section */

	/* start Content section */
		$this->start_controls_section( 'section_content',
			[
				'label' => __('Content', 'nerd-builder'),
			]
		);
		
		$this->add_control( Builder::$prefix . '_title', [
				'label' => __('Title', 'nerd-builder'),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'This is the heading', Builder::$prefix),
				'placeholder' => __( 'Enter your title', Builder::$prefix),
				'label_block' => true
			]
		);
		$this->add_control( Builder::$prefix . '_title_tag', [
				'label' => __('Title HTML Tag', 'nerd-builder'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
				],
				'default' => 'h2',
				'condition' => [
					Builder::$prefix . '_title!' => '',
				]
			]
		);
		$this->add_control( Builder::$prefix . '_description', [
				'label' => __('Description', 'nerd-builder'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __('Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'nerd-builder'),
				'placeholder' => __('Enter your description', 'nerd-builder'),
				'rows' => 5
			]
		);
		$this->add_control( Builder::$prefix . '_whole_box', [
				'label' => __('Apply Link On', 'nerd-builder'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'box' => __('Whole Box', 'nerd-builder'),
					'button' => __('Button Only', 'nerd-builder'),
				],
				'default' => 'button'
			]
		);
		$this->add_control(
			Builder::$prefix . '_link_whole_box',
			[
				'label' => __('Link', 'nerd-builder'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-one-link.com', 'nerd-builder'),
				'condition' => [
					Builder::$prefix . '_whole_box' => 'box',
				]
			]
		);
		$this->end_controls_section();
	/* end Content section */

	/* Botton section */
		$this->start_controls_section( 'button',
			[
				'label' => __('First Button', 'nerd-builder'),
			]
		);
		$this->add_control(
			Builder::$prefix . '_enable_button_one',
			[
				'label' => __('Enable button', 'nerd-builder'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes'
			]
		);
		$this->add_control(
			Builder::$prefix . '_button_one',
			[
				'label' => __('Text', 'nerd-builder'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Click Here', 'nerd-builder')
			]
		);
		$this->add_control(
			Builder::$prefix . '_link_one',
			[
				'label' => __('Link', 'nerd-builder'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-one-link.com', 'nerd-builder'),
                'default' => [
                    'url' => '#',
                ],
				'condition' => [
					Builder::$prefix . '_whole_box' => 'button',
				]
			]
		);

    $this->add_responsive_control(
      Builder::$prefix . '_buttons_position',
      [
        'label' => __('Position', 'nerd-builder'),
        'type' => Controls_Manager::CHOOSE,
        'label_block' => false,
        'options' => [
          'right' => [
            'title' => __('Right', 'nerd-builder'),
            'icon'  => 'eicon-h-align-right',
          ],
          'bottom' => [
            'title' => __('Bottom', 'nerd-builder'),
            'icon'  => 'eicon-v-align-bottom',
          ],
        ],
        'desktop_default' => 'bottom',
        'tablet_default' => 'bottom',
        'mobile_default' => 'bottom',
        'prefix_class' => Builder::$prefix .'_cta%s-position-button-'
      ]
    );

		$this->end_controls_section();
		
		$this->start_controls_section('second_button',
			[
				'label' => __('Second Button', 'nerd-builder'),
			]
		);
			$this->add_control( Builder::$prefix . '_enable_button_two', [
					'label' => __('Enable button', 'nerd-builder'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'nerd-builder'),
					'label_off' => __('Hide', 'nerd-builder'),
					'default' => 'on'
				]
			);
			$this->add_control( Builder::$prefix . '_button_two', [
					'label' => __('Text', 'nerd-builder'),
					'type' => Controls_Manager::TEXT,
					'default' => __('Click Here', 'nerd-builder')
				]
			);

			$this->add_control( Builder::$prefix . '_link_two', [
					'label' => __('Link', 'nerd-builder'),
					'type' => Controls_Manager::URL,
					'placeholder' => __('https://your-two-link.com', 'nerd-builder'),
                    'default' => [
                        'url' => '#',
                    ],
					'condition' => [
						Builder::$prefix . '_whole_box' => 'button',
					]
				]
			);

			$this->add_responsive_control( Builder::$prefix . '_position_button', [
					'label' => __('Button alignment', 'nerd-builder'),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'vertical' => [
							'title' => __('Vertical', 'nerd-builder'),
							'icon' => 'eicon-navigation-vertical'
						],
						'horizontal' => [
							'title' => __('Horizontal', 'nerd-builder'),
							'icon' => 'eicon-navigation-horizontal'
						]
					],
					'default' => 'horizontal',
					'prefix_class' => Builder::$prefix .'_cta-position-button%s-'
				]
			);
		$this->end_controls_section();
		/* end botton section */

    $this->start_controls_section(
      'section_ribbon',
      [
        'label' => __( 'Ribbon', 'nerd-builder' ),
      ]
    );

    $this->add_control(
      'ribbon_title',
      [
        'label' => __( 'Title', 'nerd-builder' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
        ],
      ]
    );

    $this->add_control(
      'ribbon_horizontal_position',
      [
        'label' => __( 'Position', 'nerd-builder' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
          'left' => [
            'title' => __( 'Left', 'nerd-builder' ),
            'icon' => 'eicon-h-align-left',
          ],
          'right' => [
            'title' => __( 'Right', 'nerd-builder' ),
            'icon' => 'eicon-h-align-right',
          ],
        ],
        'condition' => [
          'ribbon_title!' => '',
        ],
      ]
    );

    $this->end_controls_section();

		$this->start_controls_section( 'box_style',
			[
				'label' => __('Box', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control( 
			Builder::$prefix . '_min-height',
			[
				'label' => __('Min. Height', 'nerd-builder'),
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
				'size_units' => ['px', 'vh'],
				'desktop_default' => [
				  'unit' => 'px',
				  'size' => 100,
				],
				'tablet_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'mobile_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-content-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_alignment',
			[
				'label' => __('Alignment', 'nerd-builder'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'desktop_default' => 'left',
				'tablet_default' => 'center',
				'mobile_default' => 'center',
				'prefix_class' => Builder::$prefix .'_cta-button-wrapper-',
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-content-wrapper' => 'text-align: {{VALUE}};',
				]
			]
		);

		$this->add_responsive_control ( Builder::$prefix . '_vertical_position', [
				'label' => __('Vertical Position', 'nerd-builder'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __('Top', 'nerd-builder'),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'nerd-builder'),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'nerd-builder'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'default' => 'middle',
				'prefix_class' => 'twbb_cta-valign-align-',
			]
		);

		$this->add_responsive_control( Builder::$prefix .'_padding', [
				'label' => __('Padding', 'nerd-builder'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section( 'image_style',
			[
				'label' => __('Image', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
          'graphic_element' => Builder::$prefix . '_bg_image',
        ],
			]
		);
      $this->add_responsive_control(  Builder::$prefix . 'button_border_type',
        [
          'label' => __( 'Border Type', 'nerd-builder' ),
          'type' => Controls_Manager::SELECT,
          'options' => [
            '' => __( 'None', 'nerd-builder' ),
            'solid' => __( 'Solid', 'nerd-builder' ),
            'double' => __( 'Double', 'nerd-builder' ),
            'dotted' => __( 'Dotted', 'nerd-builder' ),
            'dashed' => __( 'Dashed', 'nerd-builder' ),
            'groove' => __( 'Groove', 'nerd-builder' ),
          ],
          'selectors' => [
            '{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'border-style:{{VALUE}};'
          ],
        ]
      );
      $this->add_responsive_control(  Builder::$prefix . 'button_border_color',
        [
          'label' => __( 'Border Color', 'nerd-builder' ),
          'type' => Controls_Manager::COLOR,
          'selectors' => [
            '{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'border-color:{{VALUE}};'
          ],
        ]
      );
      $this->add_responsive_control(  Builder::$prefix . 'button_border_Width',
        [
          'label' => __('Border Width', 'nerd-builder'),
          'type' => Controls_Manager::SLIDER,
          'size_units' => ['px'],
          'range' => [
            'px' => [
              'min' => 0,
              'max' => 15,
            ],
          ],
          'selectors' => [
            '{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'border-width:{{SIZE}}{{UNIT}};'
          ],
        ]
      );
      $this->add_responsive_control( Builder::$prefix . 'button_border_radius',
        [
          'label' => __( 'Border Radius', 'nerd-builder' ),
          'type' => Controls_Manager::SLIDER,
          'size_units' => ['px', '%'],
          'range' => [
            'px' => [
              'min' => 0,
              'max' => 200,
            ],
            '%' => [
              'min' => 0,
              'max' => 50,
            ],
          ],
          'desktop_default' => [
            'unit' => '%',
            'size' => 0,
          ],
          'tablet_default' => [
            'unit' => '%',
            'size' => 0,
          ],
          'mobile_default' => [
            'unit' => '%',
            'size' => 0,
          ],
          'selectors' => [
            '{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'border-radius: {{SIZE}}{{UNIT}};',
          ],
          'separator' => 'after',
        ]
      );
			$this->add_responsive_control( Builder::$prefix . '_image_min_width', [
					'label' => __('Min. Width', 'nerd-builder'),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', '%' ],
          'desktop_default' => [
            'unit' => 'px',
            'size' => '',
          ],
          'tablet_default' => [
            'unit' => 'px',
            'size' => '',
          ],
          'mobile_default' => [
            'unit' => 'px',
            'size' => '',
          ],
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'min-width: {{SIZE}}{{UNIT}}',
					],
          'condition' => [
            'graphic_element' => Builder::$prefix . '_bg_image',
          ],
				]
			);

			$this->add_responsive_control( Builder::$prefix . '_image_min_height', [
					'label' => __('Min. Height', 'nerd-builder'),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 500,
						],
						'vh' => [
							'min' => 0,
							'max' => 100,
						],
					],
					'size_units' => [ 'px', 'vh' ],
					'desktop_default' => [
					  'unit' => 'px',
					  'size' => '',
					],
					'tablet_default' => [
					  'unit' => 'px',
					  'size' => '',
					],
					'mobile_default' => [
					  'unit' => 'px',
					  'size' => '',
					],
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-image-background-wrapper' => 'min-height: {{SIZE}}{{UNIT}}',
					],
          'condition' => [
            'graphic_element' => Builder::$prefix . '_bg_image',
          ],
				]
			);

    $this->add_control( Builder::$prefix . '_image_background_size', [
        'label' => __('Background Size', 'nerd-builder'),
        'type' => Controls_Manager::SELECT,
        'default' => 'contain',
        'options' => [
          'auto' => __('Auto', 'nerd-builder'),
          'contain' => __('Contain', 'nerd-builder'),
          'cover' => __('Cover', 'nerd-builder'),
          'inherit' => __('Inherit', 'nerd-builder'),
          'initial' => __('Initial', 'nerd-builder'),
          'unset' => __('Unset', 'nerd-builder'),
        ],
        'condition' => [
          'graphic_element' => Builder::$prefix . '_bg_image',
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb_cta-image-background' => 'background-size: {{VALUE}}',
        ]
      ]
    );
    $this->end_controls_section();

    $this->start_controls_section( 'icon_style',
      [
        'label' => __('Icon', 'nerd-builder'),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );
    $this->start_controls_tabs( 'tabs_icon_style' );

    $this->start_controls_tab(
      'tab_icon_normal',
      [
        'label' => __( 'Normal', 'nerd-builder' ),
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
    $this->end_controls_tab();

    $this->start_controls_tab(
      'tab_icon_hover',
      [
        'label' => __( 'Hover', 'nerd-builder' ),
      ]
    );

    $this->add_control(
      'icon_hover_primary_color',
      [
        'label' => __( 'Primary Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
          '{{WRAPPER}} .elementor-view-stacked .elementor-icon:hover' => 'background-color: {{VALUE}}',
          '{{WRAPPER}} .elementor-view-framed .elementor-icon:hover, {{WRAPPER}} .elementor-view-default .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}}',
        ],
        'condition' => [
          'graphic_element' => 'icon',
        ],
      ]
    );

    $this->add_control(
      'icon_hover_secondary_color',
      [
        'label' => __( 'Secondary Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'condition' => [
          'graphic_element' => 'icon',
          'icon_view!' => 'default',
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-view-framed .elementor-icon:hover' => 'background-color: {{VALUE}};',
          '{{WRAPPER}} .elementor-view-stacked .elementor-icon:hover' => 'color: {{VALUE}};',
        ],
      ]
    );

    $this->add_control(
      'icon_hover_animation',
      [
        'label' => __( 'Animation', 'nerd-builder' ),
        'type' => Controls_Manager::HOVER_ANIMATION,
      ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section( 'section_content_style',
			[
				'label' => __('Content', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => Builder::$prefix . '_title',
							'operator' => '!==',
							'value' => '',
						],
						[
							'name' => Builder::$prefix .'_description',
							'operator' => '!==',
							'value' => '',
						]
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => Builder::$prefix . '_title_typography',
				'label' => __('Title Typography', 'tenweb-bilder'),
                'global' => [
                  'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
				'selector' => '{{WRAPPER}} .twbb_cta-title',
				'condition' => [
					Builder::$prefix . '_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_title_spacing',
			[
				'label' => __('Spacing', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-title:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					Builder::$prefix .'_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => Builder::$prefix .'_description_typography',
                'label' => __('Description Typography', 'tenweb-bilder'),
                'global' => [
                  'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
				'selector' => '{{WRAPPER}} .twbb_cta-description',
				'condition' => [
					Builder::$prefix . '_description!' => '',
				],
			]
		);

		$this->add_responsive_control(
			Builder::$prefix . '_description_spacing',
			[
				'label' => __('Spacing', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					Builder::$prefix . '_description!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'color_tabs' );
			$this->start_controls_tab( 'colors_normal', [
					'label' => __('Normal', 'nerd-builder'),
				]
			);
			$this->add_control( Builder::$prefix . '_content_bg_color', [
					'label' => __( 'Background Color', Builder::$prefix ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-content-wrapper' => 'background-color: {{VALUE}}',
					]
				]
			);
			$this->add_control( Builder::$prefix . '_title_color', [
					'label' => __('Title Color', 'nerd-builder'),
					'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_PRIMARY,
                    ],
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-title' => 'color: {{VALUE}}',
					],
					'condition' => [
						Builder::$prefix . '_title!' => '',
					],
				]
			);
			$this->add_control( Builder::$prefix . '_description_color', [
					'label' => __('Description Color', 'nerd-builder'),
					'type' => Controls_Manager::COLOR,
                    'global' => [
                        'default' => Global_Colors::COLOR_TEXT,
                    ],
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-description' => 'color: {{VALUE}}',
					],
					'condition' => [
						Builder::$prefix . '_description!' => '',
					],
				]
			);
		$this->end_controls_tab();

		$this->start_controls_tab( 'colors_hover', [
				'label' => __( 'Hover', Builder::$prefix ),
			]
		);
			$this->add_control( Builder::$prefix . '_content_bg_color_hover', [
					'label' => __('Background Color', 'nerd-builder'),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-wrapper:hover .twbb_cta-content-wrapper' => 'background-color: {{VALUE}}',
					]
				]
			);
			$this->add_control( Builder::$prefix .'_title_color_hover', [
					'label' => __('Title Color', 'nerd-builder'),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-wrapper:hover .twbb_cta-title' => 'color: {{VALUE}}',
					]
				]
			);

			$this->add_control( Builder::$prefix .'_description_color_hover', [
					'label' => __('Description Color', 'nerd-builder'),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-wrapper:hover .twbb_cta-description' => 'color: {{VALUE}}',
					],
					'condition' => [
						Builder::$prefix .'_description!' => ''
					]
				]
			);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section( 'button_style_one', [
				'label' => __('First Button', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					Builder::$prefix .'_button_one!' => ''
				]
			]
		);

		$this->add_responsive_control( Builder::$prefix . '_button_width_one', [
				'label' => __('Width', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'desktop_default' => [
				  'unit' => 'px',
				  'size' => "",
				],
				'tablet_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'mobile_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button-item__one' => 'width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$this->add_control( Builder::$prefix . '_button_size_one', [
				'label' => __('Size', 'nerd-builder'),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __('Extra Small', 'nerd-builder'),
					'sm' => __('Small', 'nerd-builder'),
					'md' => __('Medium', 'nerd-builder'),
					'lg' => __('Large', 'nerd-builder'),
					'xl' => __('Extra Large', 'nerd-builder'),
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => Builder::$prefix . '_button_typography_one',
				'label' => __('Typography', 'nerd-builder'),
                'global' => [
                  'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
				'selector' => '{{WRAPPER}} .twbb_cta-button__one'
			]
		);
		$this->add_control( Builder::$prefix . '_button_border_width_one', [
				'label' => __('Border Width', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__one' => 'border-width: {{SIZE}}{{UNIT}};',
				]
			]
		);
		$this->add_control( Builder::$prefix . '_button_border_radius_one', [
				'label' => __('Border Radius', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__one' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control( Builder::$prefix .'_button_margin_one', [
				'label' => __('Margin', 'nerd-builder'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__one' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control( Builder::$prefix .'_button_padding_one', [
				'label' => __('Padding', 'nerd-builder'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__one' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs( 'button_tabs_one' );
			$this->start_controls_tab( 'button_normal_one', [
					'label' => __('Normal', 'nerd-builder'),
				]
			);
				$this->add_control(
					Builder::$prefix . '_button_text_color_one', [
						'label' => __('Text Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'default' => '#ffffff',
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__one' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					Builder::$prefix . '_button_border_color_one', [
						'label' => __('Border Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__one' => 'border-color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					Builder::$prefix . '_button_background_color_one',
					[
						'label' => __('Background Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
                        'global' => [
                            'default' => Global_Colors::COLOR_ACCENT,
                        ],
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__one' => 'background-color: {{VALUE}};',
						],
					]
				);
				$this->end_controls_tab();

				$this->start_controls_tab( 'button-hover_one', [
							'label' => __('Hover', 'nerd-builder'),
						]
					);
					$this->add_control(
						Builder::$prefix .'_button_hover_text_color_one',
						[
							'label' => __('Text Color', 'nerd-builder'),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .twbb_cta-button__one:hover' => 'color: {{VALUE}};',
							],
						]
					);
					$this->add_control(
						Builder::$prefix .'_button_hover_border_color_one',
						[
							'label' => __('Border Color', 'nerd-builder'),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .twbb_cta-button__one:hover' => 'border-color: {{VALUE}};',
							],
						]
					);
					$this->add_control(
						Builder::$prefix .'_button_hover_background_color_one',
						[
							'label' => __('Background Color', 'nerd-builder'),
							'type' => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .twbb_cta-button__one:hover' => 'background-color: {{VALUE}};',
							],
						]
					);
				$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();
		
		$this->start_controls_section( 'button_style_two', [
				'label' => __('Second Button', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					Builder::$prefix .'_button_two!' => '',
				]
			]
		);

		$this->add_responsive_control( Builder::$prefix . '_button_width_two', [
				'label' => __('Width', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'desktop_default' => [
				  'unit' => 'px',
				  'size' => "",
				],
				'tablet_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'mobile_default' => [
				  'unit' => 'px',
				  'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button-item__two' => 'width: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$this->add_control( Builder::$prefix . '_button_size_two', [
				'label' => __('Size', 'nerd-builder'),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => [
					'xs' => __('Extra Small', 'nerd-builder'),
					'sm' => __('Small', 'nerd-builder'),
					'md' => __('Medium', 'nerd-builder'),
					'lg' => __('Large', 'nerd-builder'),
					'xl' => __('Extra Large', 'nerd-builder'),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => Builder::$prefix . '_button_typography_two',
				'label' => __('Typography', 'nerd-builder'),
				'selector' => '{{WRAPPER}} .twbb_cta-button__two',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_ACCENT,
        ],
			]
		);
		$this->add_control(
			Builder::$prefix . '_button_border_width_two',
			[
				'label' => __('Border Width', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__two' => 'border-width: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			Builder::$prefix . '_button_border_radius_two',
			[
				'label' => __('Border Radius', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__two' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control( Builder::$prefix .'_button_margin_two', [
				'label' => __('Margin', 'nerd-builder'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__two' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control( Builder::$prefix .'_button_padding_two', [
				'label' => __('Padding', 'nerd-builder'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-button__two' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->start_controls_tabs( 'button_tabs_tow' );
			$this->start_controls_tab( Builder::$prefix . '_button_normal_two', [
					'label' => __('Normal', 'nerd-builder'),
				]
			);
				$this->add_control(
					Builder::$prefix . '_button_text_color_two', [
						'label' => __('Text Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					Builder::$prefix . '_button_border_color_two', [
						'label' => __('Border Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two' => 'border-color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					Builder::$prefix . '_button_background_color_two', [
						'label' => __('Background Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two' => 'background-color: {{VALUE}};',
						],
					]
				);
			$this->end_controls_tab();

			$this->start_controls_tab(Builder::$prefix . '_button-hover_two',[
					'label' => __('Hover', 'nerd-builder'),
				]
			);
				$this->add_control( Builder::$prefix .'_button_hover_text_color_two', [
						'label' => __('Text Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two:hover' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_control( Builder::$prefix .'_button_hover_border_color_two', [
						'label' => __('Border Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two:hover' => 'border-color: {{VALUE}};',
						],
					]
				);
				$this->add_control(
					Builder::$prefix .'_button_hover_background_color_two', [
						'label' => __('Background Color', 'nerd-builder'),
						'type' => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .twbb_cta-button__two:hover' => 'background-color: {{VALUE}};',
						],
					]
				);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

    $this->start_controls_section(
      'section_ribbon_style',
      [
        'label' => __( 'Ribbon', 'elementor-pro' ),
        'tab' => Controls_Manager::TAB_STYLE,
        'show_label' => false,
        'condition' => [
          'ribbon_title!' => '',
        ],
      ]
    );

    $this->add_control(
      'ribbon_bg_color',
      [
        'label' => __( 'Background Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'global' => [
          'default' => Global_Colors::COLOR_ACCENT,
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-ribbon-inner' => 'background-color: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'ribbon_text_color',
      [
        'label' => __( 'Text Color', 'elementor-pro' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
          '{{WRAPPER}} .elementor-ribbon-inner' => 'color: {{VALUE}}',
        ],
      ]
    );

    $ribbon_distance_transform = is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg)';

    $this->add_responsive_control(
      'ribbon_distance',
      [
        'label' => __( 'Distance', 'elementor-pro' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 0,
            'max' => 50,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .elementor-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . $ribbon_distance_transform,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'ribbon_typography',
        'selector' => '{{WRAPPER}} .elementor-ribbon-inner',
        'global' => [
          'default' => Global_Typography::TYPOGRAPHY_ACCENT,
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Box_Shadow::get_type(),
      [
        'name' => 'box_shadow',
        'selector' => '{{WRAPPER}} .elementor-ribbon-inner',
      ]
    );

    $this->end_controls_section();
		
		$this->start_controls_section( 'hover_effects',
			[
				'label' => __('Hover Effects', 'nerd-builder'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			$this->add_control( Builder::$prefix . '_image_background_animation', [
					'label' => __('Image Hover Animation', 'nerd-builder'),
					'label_block' => true,
					'type' => Controls_Manager::SELECT,
					'options' => [
						'' => 'None',
						'zoom-in' => 'Zoom In',
						'zoom-out' => 'Zoom Out',
						'move-up' => 'Move Up',
						'move-down' => 'Move Down',
						'move-left' => 'Move Left',
						'move-right' => 'Move Right',
					],
					'default' => 'zoom-in',
					'prefix_class' => 'twbb_cta-image-background-animation-',
				]
			);
			$this->add_control( Builder::$prefix . '_content_animation', [
					'label' => __('Content Hover Animation', 'nerd-builder'),
					'label_block' => true,
					'type' => Controls_Manager::SELECT,
					'groups' => [
						[
							'label' => __('None', 'nerd-builder'),
							'options' => [
								'' => __('None', 'nerd-builder'),
							],
						],
						[
							'label' => __('Entrance', 'nerd-builder'),
							'options' => [
								'fade-in' => 'Fade In',
								'enter-zoom-in' => 'Zoom In',
								'enter-zoom-out' => 'Zoom Out',
								'enter-from-top' => 'Slide In Up',
								'enter-from-bottom' => 'Slide In Down',
								'enter-from-right' => 'Slide In Right',
								'enter-from-left' => 'Slide In Left',
							],
						],
						[
							'label' => __('Exit', 'nerd-builder'),
							'options' => [
								'fade-out' => 'Fade Out',
								'exit-zoom-in' => 'Zoom In',
								'exit-zoom-out' => 'Zoom Out',
								'exit-to-top' => 'Slide Out Up',
								'exit-to-bottom' => 'Slide Out Down',
								'exit-to-right' => 'Slide Out Right',
								'exit-to-left' => 'Slide Out Left',
							],
						],
						[
							'label' => __('Reaction', 'nerd-builder'),
							'options' => [
								'move-up' => 'Move Up',
								'move-down' => 'Move Down',
								'move-right' => 'Move Right',
								'move-left' => 'Move Left',
								'shrink' => 'Shrink',
								'grow' => 'Grow',
							],
						],
					],
					'default' => '',
					'condition' => [
						Builder::$prefix . '_position' => 'top'
					]
				]
			);
			$this->add_control( Builder::$prefix . '_animation_class', [
					'label' => 'Animation',
					'type' => Controls_Manager::HIDDEN,
					'default' => 'animated-content',
					'prefix_class' => 'twbb_cta-',
					'condition' => [
						Builder::$prefix . '_content_animation!' => '',
					]
				]
			);
			$this->add_control(
				Builder::$prefix . '_content_animation_duration',
				[
					'label' => __('Animation Duration', 'nerd-builder'),
					'type' => Controls_Manager::SLIDER,
					'render_type' => 'template',
					'default' => [
						'size' => 1000,
					],
					'range' => [
						'px' => [
							'min' => 0,
							'max' => 3000,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .twbb_cta-content-wrapper' => 'transition-duration: {{SIZE}}ms',
						'{{WRAPPER}} .twbb_cta-content-wrapper .twbb_cta-button-wrapper ' => 'transition-delay: calc( {{SIZE}}ms / 3 )',
					]
				]
			);

		$this->start_controls_tabs( 'bg_effects_tabs' );

		$this->start_controls_tab( 'normal',
			[
				'label' => __('Normal', 'nerd-builder'),
			]
		);

		$this->add_control(
			Builder::$prefix . '_overlay_color',
			[
				'label' => __('Overlay Color', 'nerd-builder'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-wrapper:not(:hover) .twbb_cta-image-background-overlay' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => Builder::$prefix . '_bg_filters',
				'selector' => '{{WRAPPER}} .twbb_cta-image-background',
			]
		);

		$this->add_control(
			Builder::$prefix . '_overlay_blend_mode',
			[
				'label' => __('Blend Mode', 'nerd-builder'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Normal', 'nerd-builder'),
					'color' => 'Color',
					'overlay' => 'Overlay',
					'screen' => 'Screen',
					'darken' => 'Darken',
					'multiply' => 'Multiply',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'luminosity' => 'Luminosity',
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-image-background-overlay' => 'mix-blend-mode: {{VALUE}}',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __('Hover', 'nerd-builder'),
			]
		);

		$this->add_control(
			Builder::$prefix .'_overlay_color_hover',
			[
				'label' => __('Overlay Color', 'nerd-builder'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-wrapper:hover .twbb_cta-image-background-overlay' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => Builder::$prefix . '_bg_filters_hover',
				'selector' => '{{WRAPPER}} .twbb_cta-wrapper:hover .twbb_cta-image-background',
			]
		);

		$this->add_control(
			Builder::$prefix . '_effect_duration',
			[
				'label' => __('Transition Duration', 'nerd-builder'),
				'type' => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'default' => [
					'size' => 1500,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .twbb_cta-wrapper .twbb_cta-image-background-wrapper, {{WRAPPER}} .twbb_cta-wrapper .twbb_cta-image-background-overlay' => 'transition-duration: {{SIZE}}ms',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$prefix = Builder::$prefix;
		$main_tag 	= 'div';
		$button_tag_one = 'a';
		$button_tag_two = 'a';
		$title_tag = $settings[Builder::$prefix .'_title_tag'];
		$link_whole_box = !empty($settings[Builder::$prefix . '_link_whole_box']['url']) ? $settings[Builder::$prefix . '_link_whole_box']['url'] : '';

		if ( !empty($link_whole_box) && $settings[Builder::$prefix .'_whole_box'] === 'box' ) {
			$main_tag = 'a';
			$button_tag_one  = 'button';
			$button_tag_two  = 'button';
			$this->add_render_attribute( Builder::$prefix .'_wrapper', 'href', $link_whole_box );
			if ( $settings[Builder::$prefix . '_link_whole_box']['is_external'] ) {
				$this->add_render_attribute( Builder::$prefix .'_wrapper', 'target', '_blank' );
			}
			if ( !empty($settings[Builder::$prefix . '_link_whole_box']['nofollow']) && $settings[Builder::$prefix . '_link_whole_box']['nofollow'] == 'on') {
				$this->add_render_attribute( Builder::$prefix . '_wrapper', 'rel', 'nofollow' );
			}
		}
		else {
			$link_url_one = !empty($settings[Builder::$prefix . '_link_one']['url']) ? $settings[Builder::$prefix . '_link_one']['url'] : '';
			if ( !empty($link_url_one) ) {
				$this->add_render_attribute( Builder::$prefix . '_button_one', 'href', $link_url_one );
				if ( $settings[Builder::$prefix . '_link_one']['is_external'] ) {
					$this->add_render_attribute( Builder::$prefix . '_button_one', 'target', '_blank' );
				}
				if ( !empty($settings[Builder::$prefix . '_link_one']['nofollow']) && $settings[Builder::$prefix . '_link_one']['nofollow'] == 'on') {
					$this->add_render_attribute( Builder::$prefix . '_button_one', 'rel', 'nofollow' );
				}
			}

			$link_url_two = !empty($settings[Builder::$prefix . '_link_two']['url']) ? $settings[Builder::$prefix . '_link_two']['url'] : '';
			if ( !empty($link_url_two) ) {
				$this->add_render_attribute( Builder::$prefix . '_button_two', 'href', $link_url_two );
				if ( $settings[Builder::$prefix . '_link_two']['is_external'] ) {
					$this->add_render_attribute( Builder::$prefix . '_button_two', 'target', '_blank' );
				}
				if ( !empty($settings[Builder::$prefix . '_link_two']['nofollow']) && $settings[Builder::$prefix . '_link_two']['nofollow'] == 'on') {
					$this->add_render_attribute( Builder::$prefix . '_button_two', 'rel', 'nofollow' );
				}
			}
		}
		$animation_content_class = !empty( $settings[Builder::$prefix .'_content_animation'] ) ? Builder::$prefix . '_cta-content-animation-' . $settings[Builder::$prefix .'_content_animation'] : '';
		$bg_image = '';
		if ( !empty( $settings[Builder::$prefix . '_bg_image']['id'] ) ) {
			$bg_image = Group_Control_Image_Size::get_attachment_image_src( $settings[Builder::$prefix .'_bg_image']['id'], Builder::$prefix .'_bg_image', $settings );
		} elseif ( !empty( $settings[Builder::$prefix .'_bg_image']['url'] ) ) {
			$bg_image = $settings[Builder::$prefix . '_bg_image']['url'];
		}
		$this->add_render_attribute( Builder::$prefix . '_image-background', 'style', [
			'background-image: url("' . $bg_image . '");',
		] );

		$content = false;
		if ( !empty($settings[Builder::$prefix .'_title']) || !empty($settings[Builder::$prefix .'_description']) ) {
			$content = true;
		}

		$this->add_render_attribute( Builder::$prefix . '_title', 'class', [
				Builder::$prefix . '_cta-title'
			]
		);

		$this->add_render_attribute( Builder::$prefix . '_description', 'class', [
				Builder::$prefix . '_cta-description'
			]
		);

		$enable_button_one_class = (!empty( $settings[Builder::$prefix .'_enable_button_one'] ) && $settings[Builder::$prefix .'_enable_button_one'] == 'yes') ? Builder::$prefix . '_cta-button-enable-yes' : Builder::$prefix . '_cta-button-enable-no';
		$this->add_render_attribute( Builder::$prefix . '_button_one', 'class', [
				'elementor-button',
        Builder::$prefix . '_cta-button',
				'elementor-size-' . $settings[ Builder::$prefix . '_button_size_one'],
				Builder::$prefix . '_cta-button__one'
			]
		);
		$enable_button_two_class = (!empty( $settings[Builder::$prefix .'_enable_button_two'] ) && $settings[Builder::$prefix .'_enable_button_two'] == 'yes') ? Builder::$prefix . '_cta-button-enable-yes' : Builder::$prefix . '_cta-button-enable-no';
		$this->add_render_attribute( Builder::$prefix . '_button_two', 'class', [
		    'elementor-button',
		    Builder::$prefix . '_cta-button',
				'elementor-size-' . $settings[ Builder::$prefix . '_button_size_two'],
				Builder::$prefix . '_cta-button__two'
			]
		);

		$this->add_inline_editing_attributes( Builder::$prefix . '_title' );
		$this->add_inline_editing_attributes( Builder::$prefix . '_description' );
		$this->add_inline_editing_attributes( Builder::$prefix . '_button_one' );
		$this->add_inline_editing_attributes( Builder::$prefix . '_button_two' );

    if ( 'icon' === $settings['graphic_element'] ) {
      $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-icon-wrapper' );
      $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-view-' . $settings['icon_view'] );
      if ( 'default' != $settings['icon_view'] ) {
        $this->add_render_attribute( 'icon-wrapper', 'class', 'elementor-shape-' . $settings['icon_shape'] );
      }
      if ( !empty( $settings['icon'] ) ) {
        $this->add_render_attribute( 'icon', 'class', $settings['icon'] );
      }
    }
    if ( $settings['icon_hover_animation'] ) {
      $this->add_render_attribute( 'icon', 'class', 'elementor-animation-' . $settings['icon_hover_animation'] );
    }

    $migrated = isset($settings['__fa4_migrated']['selected_icon']);
    $is_new = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

    ?>
    <<?php echo $main_tag .' '. $this->get_render_attribute_string( $prefix . '_wrapper' ); ?> class="twbb_cta-wrapper <?php echo $settings['graphic_element'] == 'icon' ? 'twbb_cta-with-icon' : ''; ?>">
    <?php if ($settings['graphic_element'] != 'none') { ?>
      <?php if ($settings['graphic_element'] == $prefix . '_bg_image') { ?>
        <div class="twbb_cta-image-background-wrapper">
          <span class="twbb_cta-image-background-overlay"></span>
          <div class="twbb_cta-image-background" <?php echo $this->get_render_attribute_string( $prefix . '_image-background' ); ?>></div>
        </div>
      <?php } elseif ('icon' === $settings['graphic_element'] && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) )) { ?>
        <div class="twbb_cta-icon-wrapper">
          <div <?php echo $this->get_render_attribute_string('icon-wrapper'); ?>>
            <div class="elementor-icon tenweb-icon">
              <?php if ( $is_new || $migrated ) :
                Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
              else : ?>
                <i <?php echo $this->get_render_attribute_string( 'icon' ); ?>></i>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php }
    } ?>
  <div class="twbb_cta-content-wrapper <?php echo $animation_content_class; ?>">
    <div class="twbb_cta-content-inner">
      <?php
      if ( $content ) {
        if ( !empty($settings[$prefix . '_title']) ) {
          ?>
          <<?php echo $title_tag . ' ' . $this->get_render_attribute_string( $prefix . '_title' ); ?>><?php echo $settings[$prefix .'_title']; ?></<?php echo $title_tag; ?>>
          <?php
        }
        if ( !empty($settings[$prefix . '_description']) ) {
          ?>
          <div <?php echo $this->get_render_attribute_string( $prefix . '_description' ); ?>><?php echo $settings[$prefix . '_description']; ?></div>
          <?php
        }
      }
      ?>
    </div>
    <div class="twbb_cta-button-wrapper">
      <?php if ( !empty($settings[$prefix . '_button_one']) ) { ?>
        <div class="twbb_cta-button-item twbb_cta-button-item__one <?php echo $enable_button_one_class; ?>">
          <<?php echo $button_tag_one . ' ' . $this->get_render_attribute_string( $prefix . '_button_one' ); ?>><?php echo $settings[$prefix . '_button_one']; ?></<?php echo $button_tag_one; ?>>
        </div>
      <?php } ?>
      <?php if ( !empty($settings[$prefix . '_button_two']) ) { ?>
        <div class="twbb_cta-button-item twbb_cta-button-item__two <?php echo $enable_button_two_class; ?>">
          <<?php echo $button_tag_two . ' ' . $this->get_render_attribute_string( $prefix . '_button_two' ); ?>><?php echo $settings[$prefix . '_button_two']; ?></<?php echo $button_tag_two; ?>>
        </div>
      <?php } ?>
    </div>
  </div>
    <?php
    if ( ! empty( $settings['ribbon_title'] ) ) :
      $this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon' );

      if ( ! empty( $settings['ribbon_horizontal_position'] ) ) {
        $this->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_horizontal_position'] );
      }
      ?>
      <div <?php echo $this->get_render_attribute_string( 'ribbon-wrapper' ); ?>>
        <div class="elementor-ribbon-inner"><?php echo $settings['ribbon_title']; ?></div>
      </div>
    <?php endif; ?>
    </<?php echo  $main_tag; ?>>
<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register(new Call_To_Action());
