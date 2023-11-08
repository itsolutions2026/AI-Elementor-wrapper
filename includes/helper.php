<?php
/**
 * Created by PhpStorm.
 * User: mher
 * Date: 7/31/18
 * Time: 4:35 PM
 */

namespace Tenweb_Builder;


class Helper {

  public static function get_post_types($args = array()){
    $defaults = array(
      'exclude_from_search' => false,
    );

    $args = wp_parse_args($args, $defaults);

    $post_types = get_post_types($args, 'objects');
    return $post_types;
  }

  /**
   * Get request value.
   *
   * @param string $key
   * @param string $default_value
   * @param bool $esc_html
   *
   * @return string|array
   */
  public static function get($key, $default_value = '', $esc_html = true) {
    if (isset($_GET[$key])) {
      $value = $_GET[$key];
    }
    elseif (isset($_POST[$key])) {
      $value = $_POST[$key];
    }
    elseif (isset($_REQUEST[$key])) {
      $value = $_REQUEST[$key];
    }
    else {
      $value = $default_value;
    }
    if (is_array($value)) {
      array_walk_recursive($value, array('self', 'validate_data'), $esc_html);
    }
    else {
      self::validate_data($value, $esc_html);
    }
    return $value;
  }

  public static function clear_site_cache($clear_elementor_cache=true, $flush_rewrite=true, $regenerate_home_critical=true){
    if($flush_rewrite) {
      flush_rewrite_rules();
    }

    if($clear_elementor_cache) {
      // Regenerate Elementor generated css files.
      \Elementor\Plugin::instance()->files_manager->clear_cache();
    }

    if(class_exists('\TenWebOptimizer\OptimizerAdmin')) {

      \TenWebOptimizer\OptimizerAdmin::get_instance();

      global $TwoSettings;
      $two_critical_pages = $TwoSettings->get_settings("two_critical_pages");

      if($regenerate_home_critical && !empty($two_critical_pages["front_page"])) {
        // after critical regeneration booster cache will be cleared

        $two_critical_pages["front_page"]["wait_until"] = "load";
        $TwoSettings->update_setting("two_critical_pages", $two_critical_pages);
        \TenWebOptimizer\OptimizerUtils::regenerate_critical("front_page");

      } else {

        // booster will clear also hosting cache
        \TenWebOptimizer\OptimizerAdmin::clear_cache(
          false,
          false,
          true,
          true,
          'front_page',
          false,
          true,
          false,
          false);

      }
    } else {
      // if booster is not active clear hosting cache
      do_action('tenweb_purge_all_caches', false);
    }

  }

  /**
   * Validate data.
   *
   * @param $value
   * @param $esc_html
   */
  private static function validate_data(&$value, $esc_html) {
    $value = stripslashes($value);
    if ($esc_html) {
      $value = esc_html($value);
    }
  }
    public static function two_redirect( $url ) {
        while (ob_get_level() !== 0) {
            ob_end_clean();
        }
        wp_redirect( $url );
        exit();
    }
}
