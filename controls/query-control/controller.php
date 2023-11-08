<?php

namespace Tenweb_Builder\Controls\QueryControl;

use Elementor\Controls_Manager;
use Tenweb_Builder\Controls\QueryControl\Controls\Group_Control_Posts;

if ( !defined('ABSPATH') ) {
  exit; // Exit if accessed directly
}

/**
 * Class QueryController
 *
 * @package Tenweb_Builder\Controls\QueryControl
 */
class QueryController {

  protected static $instance = NULL;

  const QUERY_CONTROL_ID = 'TWBBSelectAjax';

  public static $displayed_ids = [];

  public function __construct() {
    $this->add_actions();
  }

  protected function add_actions() {
    add_action('elementor/controls/controls_registered', [ $this, 'register_controls' ]);
  }

  public static function elementor() {
    return \Elementor\Plugin::instance();
  }

  /**
   * @param Widget_Base $widget
   */
  public static function add_exclude_controls( $args = array() ) {
    if( !$args['widget'] ) {
      return;
    }
    if( !$args['filter_by'] ) {
      $filter_by = 'any_type';
    }
    else {
      $filter_by = $args['filter_by'];
    }
    $widget = $args['widget'];
    $widget->add_control('exclude', [
      'label' => __('Exclude', 'nerd-builder'),
      'type' => Controls_Manager::SELECT2,
      'multiple' => TRUE,
      'options' => [
        'current_post' => __('Current Post', 'nerd-builder'),
        'manual_selection' => __('Manual Selection', 'nerd-builder'),
      ],
      'label_block' => TRUE,
    ]);
    $widget->add_control('exclude_ids', [
      'label' => __('Search & Select', 'nerd-builder'),
      'type' => self::QUERY_CONTROL_ID,
      'post_type' => '',
      'options' => [],
      'label_block' => TRUE,
      'multiple' => TRUE,
      'filter_by' => $filter_by,
      'condition' => [
        'exclude' => 'manual_selection',
      ],
    ]);
    $arr_className = explode( '\\', get_class( $widget ));
    if ( $arr_className[count($arr_className) - 1 ] == 'Sitemap' ) {
      $widget->add_control(
        'avoid_duplicates',
        [
          'label' => __( 'Avoid Duplicates', 'nerd-builder' ),
          'type' => Controls_Manager::SWITCHER,
          'default' => '',
          'description' => __( 'Set to Yes to avoid duplicate posts from showing up on the page. This only affects the frontend.', 'nerd-builder' ),
        ]
      );
    }
  }

  public function register_controls() {
    $controls_manager = self::elementor()->controls_manager;
    $controls_manager->add_group_control(Group_Control_Posts::get_type(), new Group_Control_Posts());
  }

  public static function get_instance() {
    if ( self::$instance === NULL ) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}
