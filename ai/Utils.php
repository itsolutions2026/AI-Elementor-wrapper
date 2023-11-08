<?php
namespace Tenweb_Builder;

class Utils {
  public static function update_limitations($limitations){

    $tmp_limits = self::get_limitation();

    if(!empty($tmp_limits)){
      $limitations = array_merge($tmp_limits, $limitations);
    }

    foreach($limitations as $key => $value) {
      $key = sanitize_text_field($key);
      $value = sanitize_text_field($value);
      $limitations[$key] = $value;
    }

    update_option('twbb_limitation', $limitations);
  }

  public static function get_access_token(){
    return get_site_option(TENWEB_PREFIX . '_access_token');
  }

  public static function get_domain_id(){
    return get_site_option('tenweb_domain_id');
  }

  public static function get_workspace_id(){
    return get_option('tenweb_workspace_id');
  }

  public static function get_limitation(){
    return get_option('twbb_limitation');
  }

  public static function is_free( $total_allowed_words ) {
    if ( intval($total_allowed_words) <= 5000 ) {
      return true;
    }
    return false;
  }
}