<?php
namespace Tenweb_Builder;


class AdminCondition extends Condition {

  public function admin_condition_popup($post_id){
    wp_register_script(
      'jquery-elementor-select2',
      ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full.min.js',
      [
        'jquery',
      ],
      '4.0.6-rc.1',
      true
    );

    wp_register_style(
      'elementor-select2',
      ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.min.css',
      [],
      '4.0.6-rc.1'
    );

    wp_register_script('twbb-condition-js', TWBB_URL . '/assets/editor/js/condition.js', ['jquery'],TWBB_VERSION);
    wp_register_style('twbb-condition', TWBB_URL . '/assets/editor/css/condition.css', array(), TWBB_VERSION);
    $rest_route = add_query_arg(array('rest_route' => '/'), get_home_url() . "/");

    wp_localize_script('twbb-condition-js', 'twbb_editor', array(
      'admin_condition_class' => 'display-conditions-admin',
      'texts' => array(
        'include' => __('Include', 'nerd-builder'),
        'exclude' => __('Exclude', 'nerd-builder'),
        'general' => __('Entire Site', 'nerd-builder'),
        'archive' => __('Archive', 'nerd-builder'),
        'singular' => __('Singular', 'nerd-builder'),
        'are_your_sure' => __('Are you sure?', 'nerd-builder'),
        'condition_removed' => __('A condition has been removed.', 'nerd-builder'),
        'content_missing' => __('<b>Warning:</b> There are no content widgets in this Single template. Please make sure to add some.', 'nerd-builder'),
        'publish' => __('Publish', 'nerd-builder'),
        'continue' => __('Continue', 'nerd-builder'),
      ),
      'ajax_url' => admin_url('admin-ajax.php'),
      'rest_route' => $rest_route,
      'rest_nonce' => wp_create_nonce('wp_rest'),
      'post_id' => $post_id,
      'conditions' => parent::get_template_condition($post_id, 'all', true),
      'twbb_template_type' => Templates::get_instance()->is_twbb_template()
    ));

    wp_print_scripts('jquery-elementor-select2');
    wp_print_styles('elementor-select2');
    wp_print_scripts('twbb-condition-js');
    wp_print_styles('twbb-condition');
    parent::condition_popup();
  }

}
