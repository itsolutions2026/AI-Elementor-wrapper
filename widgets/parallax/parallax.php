<?php
namespace Tenweb_Builder\Widgets\Parallax;

use Elementor\Controls_Manager;
use Elementor\Element_Section;
use Elementor\Widget_Base;

/**
 * Class Parallax
 *
 * @package Tenweb_Builder\Widgets\Parallax
 */
class Parallax extends Widget_Base {

  public function __construct() {
    $this->add_actions();
  }

  public function get_name() {
    return 'tenweb-parallax';
  }

  public function show_in_panel() {
    return false;
  }

  /**
   * @param Controls_Parallax $element
   */
	public function register_custom_controls( Element_Section $element ) {
        $element->start_injection( [
            'of' => 'background_bg_width_mobile',
        ] );
        $element->add_control(
            'tenweb_enable_parallax_efects',
            [
                'label' => __( 'Parallax Effects', 'nerd-builder' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __( 'Off', 'nerd-builder' ),
                'label_on' => __( 'On', 'nerd-builder' ),
                'render_type' => 'ui',
                'frontend_available' => true,
                'condition' => [
                  'background_background' => 'classic',
                  'tenweb_enable_parallax_efects' => 'yes'
                ],
            ]
        );
        /*
         * Vertical effect
         */
        $element->add_control(
            'tenweb_vertical_scroll_efects',
            [
                'label' => __( 'Vertical Scroll', 'nerd-builder' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );
        $element->add_control(
            'tenweb_vertical_scroll_efects-direction',
            [
                'label' => __( 'Vertical Scroll direction', 'nerd-builder' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'up',
                'options' => [
                    'up' => __( 'Up', 'nerd-builder' ),
                    'down' => __( 'Down', 'nerd-builder' ),
                ],
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'tenweb_vertical_scroll_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'popover' => [
                    'start'=>true
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );
        $element->add_control(
            'tenweb_vertical_scroll_efects-speed',
            [
                'label' => __( 'Vertical Scroll Speed', 'nerd-builder' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 4,
                ],
                'range' => [
                    'px' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'condition' => [
                    'tenweb_enable_parallax_efects' => 'yes',
                    'tenweb_vertical_scroll_efects' => 'yes',
                    'background_background' => 'classic',
                ],
                'popover' => [
                    'end'=>true
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

    /*
     * Horizontal effect
     */
    $element->add_control(
      'tenweb_horizontal_scroll_efects',
      [
        'label' => __( 'Horizontal Scroll', 'nerd-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_horizontal_scroll_efects-direction',
      [
        'label' => __( 'Horizontal Scroll direction', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'left',
        'options' => [
          'left' => __( 'To Left', 'nerd-builder' ),
          'right' => __( 'To Right', 'nerd-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_horizontal_scroll_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_horizontal_scroll_efects-speed',
      [
        'label' => __( 'Horizontal Scroll Speed', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_horizontal_scroll_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     *  Transparency
     */
    $element->add_control(
      'tenweb_transparency_efects',
      [
        'label' => __( 'Transparency', 'nerd-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_transparency_efects-direction',
      [
        'label' => __( 'Transparency direction', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Fade In', 'nerd-builder' ),
          'out' => __( 'Fade Out', 'nerd-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_transparency_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_transparency_efects-speed',
      [
        'label' => __( 'Transparency Speed', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_transparency_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     * Blur
     */
    $element->add_control(
      'tenweb_blur_efects',
      [
        'label' => __( 'Blur', 'nerd-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_blur_efects-direction',
      [
        'label' => __( 'Blur direction', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Fade In', 'nerd-builder' ),
          'out' => __( 'Fade Out', 'nerd-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_blur_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_blur_efects-speed',
      [
        'label' => __( 'Blur Speed', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_blur_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    /*
     * Scale
     */
    $element->add_control(
      'tenweb_scale_efects',
      [
        'label' => __( 'Scale', 'nerd-builder' ),
        'type' => Controls_Manager::POPOVER_TOGGLE,
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_scale_efects-direction',
      [
        'label' => __( 'Scale direction', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'in',
        'options' => [
          'in' => __( 'Scale In', 'nerd-builder' ),
          'out' => __( 'Scale Out', 'nerd-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_scale_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'start' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->add_control(
      'tenweb_scale_efects-speed',
      [
        'label' => __( 'Scale Speed', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'default' => [
          'size' => 4,
        ],
        'range' => [
          'px' => [
            'max' => 10,
            'step' => 0.1,
          ],
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'tenweb_scale_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'popover' => [
          'end' => true
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );

    $element->add_control(
      'tenweb_parallax_on',
      [
        'label' => __( 'Apply Effects On', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => 'true',
        'default' => [ 'desktop', 'tablet', 'mobile' ],
        'options' => [
          'desktop' => __( 'Desktop', 'nerd-builder' ),
          'tablet' => __( 'Tablet', 'nerd-builder' ),
          'mobile' => __( 'Mobile', 'nerd-builder' ),
        ],
        'condition' => [
          'tenweb_enable_parallax_efects' => 'yes',
          'background_background' => 'classic',
        ],
        'render_type' => 'none',
        'frontend_available' => true,
      ]
    );
    $element->end_injection();
  }

  private function add_actions() {
    add_action( 'elementor/element/section/section_advanced/before_section_end', [ $this, 'register_custom_controls' ], 10, 3 );
  }
}
\Elementor\Plugin::instance()->widgets_manager->register( new Parallax() );
