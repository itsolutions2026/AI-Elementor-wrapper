<?php

namespace Tenweb_Builder;

class TenWebApi {

  protected static $instance = null;

  private $api_url;
  private $access_token;
  private $domain_id;
  private $workspace_id;


  private function __construct(){
    $this->access_token = Utils::get_access_token();
    $this->domain_id = Utils::get_domain_id();
    $this->workspace_id = Utils::get_workspace_id();
    $this->api_url = TENWEB_AI_ASSISTANT;
  }


  public function ai_action($params, $rest_route){
    $rest_route = explode("/", $rest_route);

    $type = $rest_route[0];
    $action = $rest_route[1];

    $url = $this->api_url . 'actions/workspaces/' . $this->workspace_id . '/domains/' . $this->domain_id;
    $body = ["actionName" => $action, "params" => json_encode($params), "actionType" => $type];

    $res = wp_safe_remote_post($url, [
      'headers' => $this->get_headers(),
      'body' => $body,
    ]);

    $response_code = (int) wp_remote_retrieve_response_code( $res );

    if ( is_wp_error($res) ) {
      return $res->get_error_message();
    }

    if ( $response_code !== 200 ) {
      if ( $response_code === 417 && !empty($res['response']) ) {
        if ( !empty($res['response']['message']) && $res['response']['message'] === 'Expectation Failed' ) {
          return "input_is_long";
        }
      }
      elseif ( $response_code === 400 ) {
        $body = json_decode($res['body'], true);
        if ( !empty($body['message']) && $body['message'] === 'You have exceeded your plan limit' ) {
          return 'plan_limit_exceeded';
        }
      }

      return wp_remote_retrieve_response_message($res);
    }

    return "ok";
  }

  public function get_limitations(){
    $url = $this->api_url . 'actions/workspaces/' . $this->workspace_id . '/limits';
    $res = wp_safe_remote_get($url, [
      'headers' => $this->get_headers(),
    ]);

    if(wp_remote_retrieve_response_code($res) !== 200) {
      return false;
    }

    $data = json_decode($res['body'], true)['data'];
    Utils::update_limitations($data);

    return $data;
  }

  public function check_single_token($token){
    $body = array('one_time_token' => $token);

    $args = array(
      'method' => 'POST',
      'headers' => [],
      'body' => $body
    );

    $url = TENWEB_API_URL . '/domains/' . $this->domain_id . '/check-single';
    $args['headers']["Authorization"] = "Bearer " . $this->access_token;
    if(empty($args['headers']["Accept"])) {
      $args['headers']["Accept"] = "application/x.10webmanager.v1+json";
    }
    $args['timeout'] = 50000;
    $result = wp_remote_request($url, $args);

    if(is_wp_error($result)) {
      return false;
    }

    $body = json_decode($result['body'], true);

    if(isset($body['error'])) {
      return false;
    }

    if(wp_remote_retrieve_response_code($result) !== 200) {
      return false;
    }

    return (!empty($response['status']) && $response['status'] == "ok");
  }

  private function get_headers(){
    return [
      'Authorization' => 'Bearer ' . $this->access_token,
      'Accept' => 'application/x.10webaiassistantapi.v1+json'
    ];
  }

  public static function get_instance(){
    if(null == self::$instance) {
      self::$instance = new self;
    }

    return self::$instance;
  }
}