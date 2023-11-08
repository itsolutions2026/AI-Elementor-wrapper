<?php
namespace Tenweb_Builder;

use Tenweb_Authorization\Helper as k;

/**
 * Class Twbb_RestApi
 *
 * @package Tenweb_Builder\Twbb_RestApi
 */

class Twbb_RestApi {
    private $helper;

    public function __construct() {
        $this->helper = k::get_instance();
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        $this->register_templates_routes();
        $this->register_route_wp_get_pages_templates();
        $this->register_route_wp_delete_pages();
        $this->register_route_wp_publish_pages();
        $this->register_route_wp_unpublish_pages();
        $this->register_route_wp_add_blank_page();
        $this->register_route_check_domain();
        $this->register_route_set_page_as_homepage();

        if ( TW_HOSTED_ON_10WEB ) {
          include_once "ai/RestApi.php";
          RestApi::get_instance();
        }
    }

    private function register_route_set_page_as_homepage() {
        register_rest_route('nerd-builder/v1', 'set-as-homepage',
            array(
                'methods'  => \WP_REST_Server::EDITABLE, // post method,
                'permission_callback' => array($this, 'check_authorization'),
                'callback' => array($this, 'set_as_homepage'),
                'args' => array(
                    'page_id' => array(
                        'type' => 'string',
                        'required' => true,
                        'validate_callback' => function( $param ) {
                            if( empty( $param ) ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    ),
                ),
            )
        );
    }

    private function register_route_check_domain() {
        register_rest_route('nerd-builder/v1', 'check_domain',
            array(
                'methods'  => 'POST',
                'permission_callback' => '__return_true',
                'callback' => array($this, 'check_domain'),
            )
        );
    }

    private function register_route_wp_get_pages_templates() {

        register_rest_route( 'nerd-builder/v1', '/get-all-pages', array(
                'methods' => \WP_REST_Server::READABLE, // get method
                'callback' => array( $this,'get_all_wp_pages' ),
                'permission_callback' => array($this, 'check_authorization'),
            )
        );
    }

    private function register_route_wp_delete_pages() {

        register_rest_route( 'nerd-builder/v1', '/delete-pages', array(
                'methods' => \WP_REST_Server::EDITABLE, // post method
                'callback' => array( $this, 'delete_pages' ),
                'permission_callback' => array($this, 'check_authorization'),
                'args' => array(
                    'page_ids' => array(
                        'type' => 'json',
                        'required' => true,
                        'validate_callback' => function( $param ) {
                            if( empty( $param ) ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    ),
                ),
            )
        );
    }

    private function register_route_wp_publish_pages() {

        register_rest_route( 'nerd-builder/v1', '/publish-pages', array(
                'methods' => \WP_REST_Server::EDITABLE, // post method
                'callback' => array( $this, 'publish_pages' ),
                'permission_callback' => array($this, 'check_authorization'),
                'args' => array(
                    'page_ids' => array(
                        'type' => 'json',
                        'required' => true,
                        'validate_callback' => function( $param ) {
                            if( empty( $param ) ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    ),
                ),
            )
        );
    }

    private function register_route_wp_unpublish_pages() {

        register_rest_route( 'nerd-builder/v1', '/unpublish-pages', array(
                'methods' => \WP_REST_Server::EDITABLE, // post method
                'callback' => array( $this, 'unpublish_pages' ),
                'permission_callback' => array($this, 'check_authorization'),
                'args' => array(
                    'page_ids' => array(
                        'type' => 'json',
                        'required' => true,
                        'validate_callback' => function( $param ) {
                            if( empty( $param ) ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    ),
                ),
            )
        );
    }

    private function register_route_wp_add_blank_page() {
        register_rest_route( 'nerd-builder/v1', '/add-blank-page', array(
                'methods' => \WP_REST_Server::CREATABLE, // post method
                'callback' => array( $this, 'add_blank_page' ),
                'permission_callback' => array($this, 'check_authorization'),
                'args' => array(
                    'page_title' => array(
                        'type' => 'string',
                        'required' => true,
                        'sanitize_callback' => function( $page_title ) {
                            if( empty( $page_title ) ) {
                                return false;
                            } else {
                                return sanitize_text_field($page_title);
                            }
                        }
                    ),
                    'post_status' => array(
                        'type' => 'string',
                        'required' => true,
                        'sanitize_callback' => function( $post_status ) {
                            if( empty( $post_status ) ) {
                                return false;
                            } else {
                                return sanitize_text_field($post_status);
                            }
                        }
                    ),
                )
            )
        );
    }

    private function register_templates_routes(){

        register_rest_route('10webBuilder/templates', '/list',
            [
                'methods' => 'GET',
                'callback' => array($this, 'get_templates'),
                'permission_callback' => function ( ) {
                    return current_user_can( 'publish_posts' );
                },
            ]
        );
    }

    public function check_authorization(\WP_REST_Request $request){
        if (!\Tenweb_Authorization\Login::get_instance()->check_logged_in()){
            $data_for_response = array(
                "code"    => "unauthorized",
                "message" => "unauthorized",
                "data"    => array(
                    "status" => 401
                )
            );
            return new \WP_Error('rest_forbidden', $data_for_response, 401);
        }
        $authorize = \Tenweb_Authorization\Login::get_instance()->authorize($request);
        if (is_array($authorize)) {
            return new \WP_Error('rest_forbidden', $authorize, 401);
        }
        return true;
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function check_domain(\WP_REST_Request $request){
        if (get_site_option(TENWEB_PREFIX . '_is_available') !== '1') {
            update_site_option(TENWEB_PREFIX . '_is_available', '1');
        }
        $parameters = self::wp_unslash_conditional($request->get_body_params());

        if (isset($parameters['confirm_token'])) {
            $confirm_token_saved = get_site_transient(TENWEB_PREFIX . '_confirm_token');
            if ($parameters['confirm_token'] === $confirm_token_saved) {
                $data_for_response = array(
                    "code" => "ok",
                    "data" => "it_was_me"  // do not change
                );
                $headers_for_response = array('tenweb_check_domain' => "it_was_me");
            } else {
                $data_for_response = array(
                    "code" => "ok",
                    "data" => "it_was_not_me" // do not change
                );
                $headers_for_response = array('tenweb_check_domain' => "it_was_not_me");
            }
        } else {
            $data_for_response = array(
                "code" => "ok",
                "data" => "alive"  // do not change
            );
            $headers_for_response = array('tenweb_check_domain' => "alive");
        }

        $tenweb_hash = $request->get_header('tenweb-check-hash');
        if (!empty($tenweb_hash)) {
            $encoded = '__' . $tenweb_hash . '.';
            $encoded .= base64_encode(json_encode($data_for_response));
            $encoded .= '.' . $tenweb_hash . '__';

            $data_for_response['encoded'] = $encoded;
            k::set_error_log('tenweb-check-hash', $encoded);
        }

        return new \WP_REST_Response($data_for_response, 200, $headers_for_response);
    }

    private function get_active_kit() {
        $active_kit_id = get_option('elementor_active_kit');
        /*
         * check if generated by AI
         */
        $is_ai_kit = false;
        $kit_post = get_post($active_kit_id);
        if ( in_array($kit_post->post_title, ['AI recreated Kit', '10Web AI kit'])) {
            $is_ai_kit = true;
        }

        $kit_settings = get_post_meta( $active_kit_id, '_elementor_page_settings', true );

        // If there is a custom color or custom typography, don't generate new kit
        if($is_ai_kit === false && (!empty($kit_settings['custom_colors']) || !empty($kit_settings['custom_typography']))){
          $is_ai_kit = true;
        }

        $active_kit = array(
            'kit_settings' => $kit_settings,
            'is_ai_kit' => $is_ai_kit
        );
        return $active_kit;
    }

    public function set_as_homepage(\WP_REST_Request $request) {
        $data_for_response = array(
            'builder_status'=> 0,
            'message'=> 'Failed to set as frontpage.',
        );
        try {
            $page_id = $request->get_body_params()['page_id'];

            if ($page_id && get_post_status($page_id) == 'publish' && get_post_type($page_id) == 'page') {
                update_option('page_on_front', intval($page_id));
                update_option('show_on_front', 'page');
            }
            $is_updated = get_option('page_on_front') == intval($page_id) ? true : false;
            if ($is_updated) {
                $data_for_response['builder_status'] = 1;
                $data_for_response['message'] = "Page is set as frontpage.";

                return new \WP_REST_Response($data_for_response, 200);
            }
        } catch(\Exception $exception) {
            return new \WP_REST_Response($data_for_response, 500);
        }
    }

    public function get_all_wp_pages() {
        $all_pages =  get_posts(
            array(
                "post_type" => array( "page", 'elementor_library' ),
                "posts_per_page" => -1,
                "post_status" => array('publish', 'pending', 'draft'),
            )
        );
        $all_pages_info = array();
        foreach ( $all_pages as $key => $page) {
            if ( get_post_meta( $page->ID, '_elementor_template_type', true ) != 'kit' ) {
                $all_pages_info[$key]['ID'] = $page->ID;
                $all_pages_info[$key]['title'] = $page->post_title;
                $all_pages_info[$key]['url'] = get_page_link($page->ID);
                $all_pages_info[$key]['post_modified'] = $page->post_modified;
                $all_pages_info[$key]['post_date'] = $page->post_date;
                $all_pages_info[$key]['twbb_created_with'] = get_post_meta($page->ID, 'twbb_created_with', true);
                $all_pages_info[$key]['post_type'] = $page->post_type;
                $all_pages_info[$key]['template_type'] = get_post_meta($page->ID, '_elementor_template_type', true);
                $all_pages_info[$key]['is_edited'] = self::is_elementor_content_edited($page->ID);
                if ($all_pages_info[$key]['post_type'] == 'elementor_library') {
                    $all_pages_info[$key]['template_condition_count'] = count(Condition::get_instance()->get_template_condition($page->ID));
                }
                $all_pages_info[$key]['post_status'] = $page->post_status;
                if (get_option('page_on_front') == $page->ID) {
                    $all_pages_info[$key]['page_on_front'] = true;
                }
            }
        }

        $all_pages_info[] = $this->get_active_kit();
        return $all_pages_info;
    }

    /**
     * @param $request \WP_REST_Request
     * */
    public static function get_templates(){

        $templates_slugs = Templates::get_instance()->get_templates_slugs();

        $args = [
            'numberposts' => -1,
            'post_type' => 'elementor_library',
            'post_status' => 'publish',
            'meta_key' => '_elementor_template_type'
        ];

        $templates_list = [];

        foreach($templates_slugs as $slug) {
            $args['meta_value'] = $slug;
            $posts = get_posts($args);

            $templates_list[$slug] = $posts;
        }
        return $templates_list;
    }

    public function delete_pages(\WP_REST_Request $request) {
        $data_for_response = array(
            'status'=> 0,
            'message'=> 'Failed to delete a page.',
        );
        try {
            $pages = json_decode($request->get_body_params()['page_ids']);
            $is_deleted = wp_delete_post(intval($pages[0]));
            if ($is_deleted) {
                $data_for_response['status'] = 1;
                $data_for_response['message'] = "Page is deleted.";

                return new \WP_REST_Response($data_for_response, 200);
            }
        } catch(\Exception $exception) {
            return new \WP_REST_Response($data_for_response, 500);
        }
    }

    public function publish_pages(\WP_REST_Request $request) {
        $data_for_response = array(
            'builder_status'=> 0,
            'message'=> 'Failed to publish a page.',
        );
        try {
            $pages = json_decode($request->get_body_params()['page_ids']);
            $is_published = false;
            $the_post = array(
                'ID' => intval($pages[0]),
                'post_status' => 'publish',
            );
            $post_updated = wp_update_post($the_post);
            if (!is_wp_error($post_updated) && $post_updated > 0) {
                $is_published = true;
            }
            if ($is_published) {
                $data_for_response['builder_status'] = 1;
                $data_for_response['message'] = "Page is published.";

                return new \WP_REST_Response($data_for_response, 200);
            }
        } catch(\Exception $exception) {
            return new \WP_REST_Response($data_for_response, 500);
        }
    }

    public function unpublish_pages(\WP_REST_Request $request) {
        $data_for_response = array(
            'builder_status'=> 0,
            'message'=> 'Failed to unpublish a page.',
        );
        try {
            $pages = json_decode($request->get_body_params()['page_ids']);
            $is_unpublished = false;
            $the_post = array(
                'ID' => intval($pages[0]),
                'post_status' => 'draft',
            );
            $post_updated = wp_update_post($the_post);
            if (!is_wp_error($post_updated) && $post_updated > 0) {
                $is_unpublished = true;
            }
            if ($is_unpublished) {
                $data_for_response['builder_status'] = 1;
                $data_for_response['message'] = "Page is unpublished.";

                return new \WP_REST_Response($data_for_response, 200);
            }
        } catch(\Exception $exception) {
            return new \WP_REST_Response($data_for_response, 500);
        }
    }

    public function add_blank_page(\WP_REST_Request $request) {
        $data_for_response = array(
            'status'=> 0,
            'message'=> 'Failed to create a page.',
        );
        $page_title = $request->get_body_params()['page_title'];
        $post_status = $request->get_body_params()['post_status'];
        $menu_term_id =
            isset($request->get_body_params()['menu_term_id']) ? $request->get_body_params()['menu_term_id'] : false;
        $menu_item_id =
            isset($request->get_body_params()['menu_item_id']) ? $request->get_body_params()['menu_item_id'] : false;
        $menu_item_position =
            isset($request->get_body_params()['menu_item_position']) ? $request->get_body_params()['menu_item_position'] : false;
        $page_id  = wp_insert_post(
            array(
            'post_type' => 'page',
            'post_title' => $page_title,
            'post_status' => $post_status,
            )
        );
        try {
            if ( $page_id > 0 ) {
                update_post_meta($page_id, 'twbb_created_with', 'twbb_blank');
                if ($menu_term_id && $menu_item_id && $menu_item_position) {
                    $change_in_menus[] = array(
                        'menu_term_id' => $menu_term_id,
                        'menu_item_id' => $menu_item_id,
                        'menu_item_position' => $menu_item_position,
                    );
                }
                else if ( $post_status == 'publish' ) {
                    /* check if there is header and footer menus(setted from theme) and add page in next available position */
                    $menu_ids = [];
                    if ( isset( get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'] ) ) {
                        $header_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'];
                        $menu_ids['header_menu_id'] = $header_menu_id;
                    }
                    if ( isset( get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'] ) ) {
                        $footer_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'];
                        $menu_ids['footer_menu_id'] = $footer_menu_id;
                    }
                    foreach ($menu_ids as $menu_id) {
                        if ( $menu_id != 0 ) {
                            $menu = wp_get_nav_menu_items($menu_id);
                            $menu_item_id = 0;
                            $menu_item_position = 0;
                            foreach ($menu as $menu_item) {
                                foreach ($menu_item->classes as $menu_classes) {
                                    if(str_contains($menu_classes, 'ai-recreated-menu-item') ) {
                                        $menu_item_id = $menu_item->ID;
                                        $menu_item_position = $menu_item->menu_order;
                                        break 2;
                                    }
                                }
                            }
                            $change_in_menus[] = array(
                                'menu_term_id' => $menu_id,
                                'menu_item_id' => $menu_item_id,
                                'menu_item_position' => $menu_item_position,
                            );
                        }
                    }
                }
                foreach ($change_in_menus as $menu) {
                    wp_update_nav_menu_item($menu['menu_term_id'], $menu['menu_item_id'], array(
                        'menu-item-title' => $page_title,
                        'menu-item-object-id' => $page_id,
                        'menu-item-object' => 'page',
                        'menu-item-status' => 'publish',
                        'menu-item-type' => 'post_type',
                        'menu-item-classes' => '',
                        'menu-item-position'=> $menu['menu_item_position']
                    ));
                }
                $data_for_response['status'] = 1;
                $data_for_response['message'] = "The page has been created.";
                $data_for_response['page_url'] = admin_url( 'post.php?post=' . $page_id . '&action=elementor' );

                return new \WP_REST_Response($data_for_response, 200);
            }
        } catch(\Exception $exception) {
            return new \WP_REST_Response($data_for_response, 500);
        }
    }

    /*
        * wp 4.4 adds slashes, removes them
        *
        * https://core.trac.wordpress.org/ticket/36419
        **/
    private static function wp_unslash_conditional($data)
    {

        global $wp_version;
        if ($wp_version < 4.5) {
            $data = wp_unslash($data);
        }

        return $data;
    }

  private static function is_elementor_content_edited($post_id){
    /**
     *
     * The function checks elementor content was edited or no.
     * If revisions are disabled returns null
     *
     * @param $post_id integer
     * @returns boolean|null
     */
    if((int)WP_POST_REVISIONS == 0) {
      return null;
    }

    // get first version of page
    $revs = wp_get_post_revisions($post_id, ["order" => "ASC", "numberposts" => 1]);

    if(empty($revs)) {
      return false;
    }

    $revision = $revs[key($revs)];
    $first_elementor_data = get_post_meta($revision->ID, "_elementor_data", true);
    $current_elementor_data = get_post_meta($post_id, "_elementor_data", true);

    return $first_elementor_data != $current_elementor_data;
  }
}
