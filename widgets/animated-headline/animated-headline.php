<?php
namespace Tenweb_Builder;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Widget_Base;
use Tenweb_Builder\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class Animated_Headline extends Widget_Base {

  public function get_name() {
    return Builder::$prefix . 'animated-headline';
  }

  public function get_title() {
    return __( 'Animated Headline', 'nerd-builder' );
  }

  public function get_icon() {
    return 'twbb-animated-heading twbb-widget-icon';
  }

  public function get_categories() {
    return ['tenweb-widgets'];
  }

  public function get_keywords() {
    return [ 'headline', 'heading', 'animation', 'title', 'text' ];
  }

  protected function register_controls() {
    $this->start_controls_section(
      'text_elements',
      [
        'label' => __( 'Headline', 'nerd-builder' ),
      ]
    );

    $this->add_control(
      'headline_style',
      [
        'label' => __( 'Style', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'highlight',
        'options' => [
          'highlight' => __( 'Highlighted', 'nerd-builder' ),
          'rotate' => __( 'Rotating', 'nerd-builder' ),
        ],
        'prefix_class' => 'twbb-headline--style-',
        'render_type' => 'template',
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'animation_type',
      [
        'label' => __( 'Animation', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
          'typing' => 'Typing',
          'clip' => 'Clip',
          'flip' => 'Flip',
          'swirl' => 'Swirl',
          'blinds' => 'Blinds',
          'drop-in' => 'Drop-in',
          'wave' => 'Wave',
          'slide' => 'Slide',
          'slide-down' => 'Slide Down',
        ],
        'default' => 'typing',
        'condition' => [
          'headline_style' => 'rotate',
        ],
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'marker',
      [
        'label' => __( 'Shape', 'nerd-builder' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'circle',
        'options' => [
          'circle' => _x( 'Circle', 'Shapes', 'nerd-builder' ),
          'curly' => _x( 'Curly', 'Shapes', 'nerd-builder' ),
          'underline' => _x( 'Underline', 'Shapes', 'nerd-builder' ),
          'double' => _x( 'Double', 'Shapes', 'nerd-builder' ),
          'double_underline' => _x( 'Double Underline', 'Shapes', 'nerd-builder' ),
          'underline_zigzag' => _x( 'Underline Zigzag', 'Shapes', 'nerd-builder' ),
          'diagonal' => _x( 'Diagonal', 'Shapes', 'nerd-builder' ),
          'strikethrough' => _x( 'Strikethrough', 'Shapes', 'nerd-builder' ),
          'x' => 'X',
        ],
        'render_type' => 'template',
        'condition' => [
          'headline_style' => 'highlight',
        ],
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'before_text',
      [
        'label' => __( 'Before Text', 'nerd-builder' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
          'categories' => [
            TagsModule::TEXT_CATEGORY,
          ],
        ],
        'default' => __( 'This page is', 'nerd-builder' ),
        'placeholder' => __( 'Enter your headline', 'nerd-builder' ),
        'label_block' => true,
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'highlighted_text',
      [
        'label' => __( 'Highlighted Text', 'nerd-builder' ),
        'type' => Controls_Manager::TEXT,
        'default' => __( 'Amazing', 'nerd-builder' ),
        'label_block' => true,
        'condition' => [
          'headline_style' => 'highlight',
        ],
        'separator' => 'none',
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'rotating_text',
      [
        'label' => __( 'Rotating Text', 'nerd-builder' ),
        'type' => Controls_Manager::TEXTAREA,
        'placeholder' => __( 'Enter each word in a separate line', 'nerd-builder' ),
        'separator' => 'none',
        'default' => "Better\nBigger\nFaster",
        'rows' => 5,
        'condition' => [
          'headline_style' => 'rotate',
        ],
        'frontend_available' => true,
      ]
    );

    $this->add_control(
      'after_text',
      [
        'label' => __( 'After Text', 'nerd-builder' ),
        'type' => Controls_Manager::TEXT,
        'dynamic' => [
          'active' => true,
          'categories' => [
            TagsModule::TEXT_CATEGORY,
          ],
        ],
        'placeholder' => __( 'Enter your headline', 'nerd-builder' ),
        'label_block' => true,
        'separator' => 'none',
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
        'separator' => 'before',
      ]
    );

    $this->add_responsive_control(
      'alignment',
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
        'selectors' => [
          '{{WRAPPER}} .twbb-headline' => 'text-align: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'tag',
      [
        'label' => __( 'HTML Tag', 'nerd-builder' ),
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
          'p' => 'p',
        ],
        'default' => 'h3',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_style_marker',
      [
        'label' => __( 'Shape', 'nerd-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
        'condition' => [
          'headline_style' => 'highlight',
        ],
      ]
    );

    $this->add_control(
      'marker_color',
      [
        'label' => __( 'Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_4,
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-dynamic-wrapper path' => 'stroke: {{VALUE}}',
        ],
      ]
    );

    $this->add_control(
      'stroke_width',
      [
        'label' => __( 'Width', 'nerd-builder' ),
        'type' => Controls_Manager::SLIDER,
        'range' => [
          'px' => [
            'min' => 1,
            'max' => 20,
          ],
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-dynamic-wrapper path' => 'stroke-width: {{SIZE}}{{UNIT}}',
        ],
      ]
    );

    $this->add_control(
      'above_content',
      [
        'label' => __( 'Bring to Front', 'nerd-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-dynamic-wrapper svg' => 'z-index: 2',
          '{{WRAPPER}} .twbb-headline-dynamic-text' => 'z-index: auto',
        ],
      ]
    );

    $this->add_control(
      'rounded_edges',
      [
        'label' => __( 'Rounded Edges', 'nerd-builder' ),
        'type' => Controls_Manager::SWITCHER,
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-dynamic-wrapper path' => 'stroke-linecap: round; stroke-linejoin: round',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_style_text',
      [
        'label' => __( 'Headline', 'nerd-builder' ),
        'tab' => Controls_Manager::TAB_STYLE,
      ]
    );

    $this->add_control(
      'title_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_2,
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-plain-text' => 'color: {{VALUE}}',

        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'title_typography',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        'selector' => '{{WRAPPER}} .twbb-headline',
      ]
    );

    $this->add_control(
      'heading_words_style',
      [
        'type' => Controls_Manager::HEADING,
        'label' => __( 'Animated Text', 'nerd-builder' ),
        'separator' => 'before',
      ]
    );

    $this->add_control(
      'words_color',
      [
        'label' => __( 'Text Color', 'nerd-builder' ),
        'type' => Controls_Manager::COLOR,
        'scheme' => [
          'type' => Scheme_Color::get_type(),
          'value' => Scheme_Color::COLOR_2,
        ],
        'selectors' => [
          '{{WRAPPER}} .twbb-headline-dynamic-text' => 'color: {{VALUE}}',
        ],
      ]
    );

    $this->add_group_control(
      Group_Control_Typography::get_type(),
      [
        'name' => 'words_typography',
        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
        'selector' => '{{WRAPPER}} .twbb-headline-dynamic-text',
        'exclude' => [ 'font_size' ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render() {
    $settings = $this->get_settings_for_display();

    $tag = $settings['tag'];

    $this->add_render_attribute( 'headline', 'class', 'twbb-headline' );

    if ( 'rotate' === $settings['headline_style'] ) {
      $this->add_render_attribute( 'headline', 'class', 'twbb-headline-animation-type-' . $settings['animation_type'] );

      $is_letter_animation = in_array( $settings['animation_type'], [ 'typing', 'swirl', 'blinds', 'wave' ] );

      if ( $is_letter_animation ) {
        $this->add_render_attribute( 'headline', 'class', 'twbb-headline-letters' );
      }
    }

    if ( ! empty( $settings['link']['url'] ) ) {
      $this->add_render_attribute( 'url', 'href', $settings['link']['url'] );

      if ( $settings['link']['is_external'] ) {
        $this->add_render_attribute( 'url', 'target', '_blank' );
      }

      if ( ! empty( $settings['link']['nofollow'] ) ) {
        $this->add_render_attribute( 'url', 'rel', 'nofollow' );
      }

      echo '<a ' . $this->get_render_attribute_string( 'url' ).' >';
    }

    ?>
    <<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( 'headline' ); ?>>
    <?php if ( ! empty( $settings['before_text'] ) ) : ?>
      <span class="twbb-headline-plain-text twbb-headline-text-wrapper"><?php echo $settings['before_text']; ?></span>
    <?php endif; ?>
    <span class="twbb-headline-dynamic-wrapper twbb-headline-text-wrapper"></span>
    <?php if ( ! empty( $settings['after_text'] ) ) : ?>
      <span class="twbb-headline-plain-text twbb-headline-text-wrapper"><?php echo $settings['after_text']; ?></span>
    <?php endif; ?>
    </<?php echo $tag; ?>>
    <?php

    if ( ! empty( $settings['link']['url'] ) ) {
      echo '</a>';
    }
  }

  protected function content_template() {
    ?>
    <#
    var headlineClasses = 'twbb-headline',
    tag = settings.tag;

    if ( 'rotate' === settings.headline_style ) {
    headlineClasses += ' twbb-headline-animation-type-' + settings.animation_type;

    var isLetterAnimation = -1 !== [ 'typing', 'swirl', 'blinds', 'wave' ].indexOf( settings.animation_type );

    if ( isLetterAnimation ) {
    headlineClasses += ' twbb-headline-letters';
    }
    }

    if ( settings.link.url ) { #>
    <a htef="#">
      <# } #>
      <{{{ tag }}} class="{{{ headlineClasses }}}">
      <# if ( settings.before_text ) { #>
      <span class="twbb-headline-plain-text twbb-headline-text-wrapper">{{{ settings.before_text }}}</span>
      <# } #>

      <# if ( settings.rotating_text ) { #>
      <span class="twbb-headline-dynamic-wrapper twbb-headline-text-wrapper"></span>
      <# } #>

      <# if ( settings.after_text ) { #>
      <span class="twbb-headline-plain-text twbb-headline-text-wrapper">{{{ settings.after_text }}}</span>
      <# } #>
    </{{{ tag }}}>
    <# if ( settings.link.url ) { #>
    <a htef="#">
      <# } #>
    <?php
  }
}

\Elementor\Plugin::instance()->widgets_manager->register(new Animated_Headline());

