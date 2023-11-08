<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Countdown extends Widget_Base {

	public function get_name() {
		return Builder::$prefix . 'countdown';
	}

	public function get_title() {
		return __( 'Countdown', 'nerd-builder' );
	}

	public function get_icon() {
		return 'twbb-countdown twbb-widget-icon';
	}

  public function get_categories(){
    return ['tenweb-widgets'];
  }

	protected function register_controls() {
		$this->start_controls_section(
			'section_countdown',
			[
				'label' => __( 'Countdown', 'nerd-builder' ),
			]
		);

		$this->add_control(
			'due_date',
			[
				'label' => __( 'Due Date', 'nerd-builder' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date( 'Y-m-d H:i', $this->timestamp('+1 month') ),
				'description' => sprintf( __( 'Date set according to your timezone: %s.', 'nerd-builder' ), Utils::get_timezone_string() ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'nerd-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'squares' => __( 'Squares', 'nerd-builder' ),
					'circles' => __( 'Circles', 'nerd-builder' ),
					'inline' => __( 'Inline', 'nerd-builder' ),
				],
				'default' => 'squares',
				'prefix_class' => 'tenweb-countdown--layout-',
			]
		);

    $this->add_control(
      'show_delimiter',
      [
        'label' => __( 'Delimiter', 'nerd-builder' ) . ' (:) ',
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', 'nerd-builder' ),
        'label_off' => __( 'Hide', 'nerd-builder' ),
        'default' => 'no',
        'condition' => [
          'layout' => 'inline',
        ],
        'prefix_class' => 'tenweb-countdown--delimiters-',
      ]
    );

    $this->add_control(
      'show_months',
      [
        'label' => __( 'Months', 'nerd-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Show', 'nerd-builder' ),
        'label_off' => __( 'Hide', 'nerd-builder' ),
      ]
    );

		$this->add_control(
			'show_days',
			[
				'label' => __( 'Days', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_hours',
			[
				'label' => __( 'Hours', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_minutes',
			[
				'label' => __( 'Minutes', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_seconds',
			[
				'label' => __( 'Seconds', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

    $this->add_control(
      'description',
      [
        'label' => __( 'Description', 'nerd-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'placeholder' => "",
        'default' => "",
        'rows' => 3,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'hide_after_expiry',
      [
        'label' => __( 'Hide counter after expiry', 'nerd-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => __( 'Yes', 'nerd-builder' ),
        'label_off' => __( 'No', 'nerd-builder' ),
        'default' => 'no',
      ]
    );

    $this->add_control(
      'after_expiry_text',
      [
        'label' => __( 'Expired event text', 'nerd-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'placeholder' => __( 'Expired event text', 'nerd-builder' ),
        'default' => "Expired event text",
        'rows' => 3,
        'condition' => [
          'hide_after_expiry' => 'yes',
        ],
      ]
    );

		$this->add_control(
			'show_labels',
			[
				'label' => __( 'Show Label', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'nerd-builder' ),
				'label_off' => __( 'Hide', 'nerd-builder' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'custom_labels',
			[
				'label' => __( 'Custom Label', 'nerd-builder' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'show_labels!' => '',
				],
			]
		);

    $this->add_control(
      'label_months',
      [
        'label' => __( 'Months', 'nerd-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Months', 'nerd-builder' ),
        'placeholder' => __( 'Months', 'nerd-builder' ),
        'condition' => [
          'show_labels!' => '',
          'custom_labels!' => '',
          'show_months' => 'yes',
        ],
      ]
    );

		$this->add_control(
			'label_days',
			[
				'label' => __( 'Days', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Days', 'nerd-builder' ),
				'placeholder' => __( 'Days', 'nerd-builder' ),
				'condition' => [
					'show_labels!' => '',
					'custom_labels!' => '',
					'show_days' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_hours',
			[
				'label' => __( 'Hours', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Hours', 'nerd-builder' ),
				'placeholder' => __( 'Hours', 'nerd-builder' ),
				'condition' => [
					'show_labels!' => '',
					'custom_labels!' => '',
					'show_hours' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_minutes',
			[
				'label' => __( 'Minutes', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Minutes', 'nerd-builder' ),
				'placeholder' => __( 'Minutes', 'nerd-builder' ),
				'condition' => [
					'show_labels!' => '',
					'custom_labels!' => '',
					'show_minutes' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_seconds',
			[
				'label' => __( 'Seconds', 'nerd-builder' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Seconds', 'nerd-builder' ),
				'placeholder' => __( 'Seconds', 'nerd-builder' ),
				'condition' => [
					'show_labels!' => '',
					'custom_labels!' => '',
					'show_seconds' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Boxes', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'container_width',
			[
				'label' => __( 'Container Width', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_background_color',
			[
				'label' => __( 'Background Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}}.tenweb-countdown--layout-squares .tenweb-countdown-item' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.tenweb-countdown--layout-circles .tenweb-countdown-item' => 'background-color: {{VALUE}};',
				],
				'condition' => [
				  'layout!' => 'inline',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector' => '{{WRAPPER}} .tenweb-countdown-item',
				'separator' => 'before',
				'condition' => [
				  'layout!' => 'inline',
				]
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label' => __( 'Border Radius', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
				  'layout' => 'squares',
				]
			]
		);

		$this->add_responsive_control(
			'box_spacing',
			[
				'label' => __( 'Space Between', 'nerd-builder' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .tenweb-countdown-item:not(:first-of-type)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'body:not(.rtl) {{WRAPPER}} .tenweb-countdown-item:not(:last-of-type)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .tenweb-countdown-item:not(:first-of-type)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .tenweb-countdown-item:not(:last-of-type)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __( 'Padding', 'nerd-builder' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'tablet_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Content', 'nerd-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_description',
			[
				'label' => __( 'Description', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .tenweb-countdown-description',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'heading_digits',
			[
				'label' => __( 'Digits', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'digits_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-digits' => 'color: {{VALUE}};',
				],
				'condition' => [
				  'layout!' => 'inline',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'digits_typography',
				'selector' => '{{WRAPPER}} .tenweb-countdown-digits',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
				'condition' => [
				  'layout!' => 'inline',
				],
			]
		);


    $this->add_control(
      'digits_color_inline',
      [
        'label' => __( 'Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
		'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
        'selectors' => [
          '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-digits' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'digits_typography_inline',
        'selector' => '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-digits',
        'scheme' => Scheme_Typography::TYPOGRAPHY_3,
		'desktop_default' => [
					'size' => 53,
					'unit' => 'px',
				],
		'tablet_default' => [
			'size' => 38,
			'unit' => 'px',
		],
		'mobile_default' => [
			'size' => 25,
			'unit' => 'px',
		],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

		$this->add_control(
			'heading_label',
			[
				'label' => __( 'Label', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-label' => 'color: {{VALUE}};',
				],
				'condition' => [
				  'layout!' => 'inline',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .tenweb-countdown-label',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
				'condition' => [
				  'layout!' => 'inline',
				],
			]
		);

    $this->add_control(
      'label_color_inline',
      [
        'label' => __( 'Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
		'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
        'selectors' => [
          '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-label' => 'color: {{VALUE}};',
        ],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'label_typography_inline',
        'selector' => '{{WRAPPER}}.tenweb-countdown--layout-inline .tenweb-countdown-label',
        'scheme' => Scheme_Typography::TYPOGRAPHY_2,
		'desktop_default' => [
			'size' => 15,
			'unit' => 'px',
		],
		'tablet_default' => [
			'size' => 13,
			'unit' => 'px',
		],
		'mobile_default' => [
			'size' => 10,
			'unit' => 'px',
		],
        'condition' => [
          'layout' => 'inline',
        ],
      ]
    );

		$this->add_control(
			'heading_expiry_text',
			[
				'label' => __( 'Expiry Text', 'nerd-builder' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'expiry_text_color',
			[
				'label' => __( 'Color', 'nerd-builder' ),
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tenweb-countdown-expired' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'expiry_text_typography',
				'selector' => '{{WRAPPER}} .tenweb-countdown-wrapper.tenweb-countdown-header .tenweb-countdown-expired .tenweb-countdown-label',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();
	}

	private function timestamp($time) {
    return strtotime( $time ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
  }

	private function get_strftime( $instance ) {
		$string = '';
		$block_count = 0;
		if ( $instance['show_months'] ) {
      $string .= $this->render_countdown_item( $instance, 'label_months', 'tenweb-countdown-months', $block_count );
      $block_count++;
    }
		if ( $instance['show_days'] ) {
      $string .= $this->render_countdown_item( $instance, 'label_days', 'tenweb-countdown-days', $block_count );
      $block_count++;
    }
		if ( $instance['show_hours'] ) {
      $string .= $this->render_countdown_item( $instance, 'label_hours', 'tenweb-countdown-hours', $block_count );
      $block_count++;
    }
		if ( $instance['show_minutes'] ) {
      $string .= $this->render_countdown_item( $instance, 'label_minutes', 'tenweb-countdown-minutes', $block_count );
      $block_count++;
    }
		if ( $instance['show_seconds'] ) {
      $string .= $this->render_countdown_item( $instance, 'label_seconds', 'tenweb-countdown-seconds', $block_count );
    }

		return $string;
	}

	private $_default_countdown_labels;

	private function _init_default_countdown_labels() {
		$this->_default_countdown_labels = [
			'label_months' => __( 'Months', 'nerd-builder' ),
			'label_weeks' => __( 'Weeks', 'nerd-builder' ),
			'label_days' => __( 'Days', 'nerd-builder' ),
			'label_hours' => __( 'Hours', 'nerd-builder' ),
			'label_minutes' => __( 'Minutes', 'nerd-builder' ),
			'label_seconds' => __( 'Seconds', 'nerd-builder' ),
		];
	}

	public function get_default_countdown_labels() {
		if ( ! $this->_default_countdown_labels ) {
			$this->_init_default_countdown_labels();
		}

		return $this->_default_countdown_labels;
	}

	private function render_countdown_description( $instance ) {
		$string = '';

		if ( $instance['description'] ) {
			$string = '<div class="tenweb-countdown-description">' . $instance['description'] . '</div>';
		}

		return $string;
	}

	private function render_countdown_item( $instance, $label, $part_class, $block_count ) {
    $string = $block_count > 0 ? '<div class="tenweb-countdown-item tenweb-countdown-delimiter-container"><div><div><span class="tenweb-countdown-digits">:</span></div></div></div>' : '';
		$string .= '<div class="tenweb-countdown-item"><div><div><span class="tenweb-countdown-digits ' . $part_class . '"></span>';

		if ( $instance['show_labels'] ) {
			$default_labels = $this->get_default_countdown_labels();
			$label = ( $instance['custom_labels'] ) ? $instance[ $label ] : $default_labels[ $label ];
			$string .= ' <span class="tenweb-countdown-label">' . $label . '</span>';
		}

		$string .= '</div></div></div>';

    return $string;
	}

	private function render_expired_mesage( $instance ) {
    $string = '';

    if ( $instance['hide_after_expiry'] ) {
      $string = '<div class="tenweb-countdown-expired tenweb-hidden">';
			$string .= '<span class="tenweb-countdown-label">' . $instance['after_expiry_text'] . '</span>';
      $string .= '</div>';
    }

    return $string;
	}

	protected function render() {
		$instance = $this->get_settings_for_display();
		$due_date = $instance['due_date'];
		$description_string = $this->render_countdown_description( $instance );
		$counter_string = $this->get_strftime( $instance );
		$expiry_string = $this->render_expired_mesage( $instance );

		// Handle timezone ( we need to set GMT time )
		$gmt = get_gmt_from_date( $due_date . ':00' );
		$due_date = strtotime( $gmt );
		?>
    <div class="tenweb-countdown-wrapper tenweb-countdown-header">
      <?php echo $description_string; ?>
      <?php echo $expiry_string; ?>
    </div>
		<div class="tenweb-countdown tenweb-countdown-wrapper" data-date="<?php echo $due_date; ?>" data-hide-after-expiry="<?php echo $instance['hide_after_expiry']; ?>">
			<?php echo $counter_string; ?>
		</div>
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register(new Countdown());
