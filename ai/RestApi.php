<?php
namespace Tenweb_Builder;


class RestApi {


    const NAMESPACE = 'ai-builder-nerd/ai';
    const REST_NONCE_ACTION = 'wp_rest';

    const NOTIFICATION_OPTION = 'twbb_notification';

    const AI_OUTPUT = 'twbb_ai_output';
    const CORE_ENDPOINTS = [
        'builder/new_prompt',
        'builder/simplify_language',
        'builder/make_it_longer',
        'builder/make_it_shorter',
        'builder/fix_spelling_and_grammar',
        'builder/change_tone',
        'builder/translate_to',
    ];

    const CORE_CHATGPT_SYSTEM = 'You are a skilled and detail oriented marketing expert , content writer who is extremely familiar with Google knowledge and SEO optimization';

    const CORE_ENDPOINTS_PROMPT = array(
        'builder/new_prompt'=>
            array(
                'content' => '%s',
                'params' => ['text']
            ),
        'builder/simplify_language' =>
            array(
                'content' => 'Please simplify the following content. \n %s',
                'params' => ['text']
            ),
        'builder/make_it_longer' =>
            array(
                'content' => 'Please convert the following content into longer content for SEO-optimized and highly relevant website content. \n %s',
                'params' => ['text']
            ),
        'builder/make_it_shorter' =>
            array(
                'content' => 'Please convert the following content into shorter content and more optimized.. \n %s',
                'params' => ['text']
            ),
        'builder/fix_spelling_and_grammar' =>
            array(
                'content' => 'Please correct the spelling and grammar in the following content. \n %s',
                'params' => ['text']
            ),
        'builder/change_tone' =>
            array(
                'content' => 'Please convert the following into a more %s tone. \n %s',
                'params' => ['tone', 'text']
            ),
        'builder/translate_to' =>
            array(
                'content' => 'Please translate the following into %s. \n %s',
                'params' => ['language', 'text']
            )
    );

    protected static $instance = null;
    private $openInstance = null;

    private function __construct(){
        include_once "openai/Url.php";
        include_once "openai/OpenAi.php";
        include_once "Utils.php";
        $this->init_rest_api();
        $this->openInstance = new OpenAi();
    }

    public function init_rest_api(){
        $default_rest_routs_args = [
            'methods' => 'POST',
            'callback' => array($this, 'core_default'),
            'permission_callback' => array($this, 'check_permission'),
        ];

        $default_rest_routs = [];
        foreach(self::CORE_ENDPOINTS as $endpoint) {

            $default_rest_routs[$endpoint] = array_merge($default_rest_routs_args, array('args' => [
                'text' => [
                    'required' => true,
                    'validate_callback' => array($this, 'validate_not_empty')
                ]
            ]));
        }

        foreach($default_rest_routs as $endpoint => $args) {
            $args = array_merge($default_rest_routs_args, $args);
            register_rest_route(self::NAMESPACE, $endpoint, $args);
        }

        register_rest_route(self::NAMESPACE, 'ai_output',
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_ai_output'),
                'permission_callback' => array($this, 'check_permission'),
            )
        );

        register_rest_route(self::NAMESPACE, 'finish',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'store_ai_output'),
                'permission_callback' => true,
            )
        );
    }

    public function core_default(\WP_REST_Request $request){
        $route = trim(explode(self::NAMESPACE, $request->get_route())[1], '/');

        if(get_transient(self::NOTIFICATION_OPTION) === "in_progress") {
            wp_send_json_error("there_is_in_progress_request");
        }

        $params = [];
        foreach($request->get_body_params() as $param_name => $value) {
            $param_name = sanitize_textarea_field($param_name);
            $value = sanitize_textarea_field($value);
            $params[$param_name] = $value;
        }

        $api_response = $this->ai_action($params, $route);

        if($api_response !== "ok") {

            if(in_array($api_response, ['input_is_long', 'plan_limit_exceeded'])) {
                wp_send_json_error($api_response);
            } else {
                wp_send_json_error("api_error");
            }
        }

//        set_transient(self::NOTIFICATION_OPTION, "in_progress", 10 * MINUTE_IN_SECONDS);
        wp_send_json_success();
    }

    public function ai_action($params, $route){
        // prompt matching wrapper
        try{

            if(self::CORE_ENDPOINTS_PROMPT[$route]){

                $userParams = [];
                foreach (self::CORE_ENDPOINTS_PROMPT[$route]['params'] as $value) {
                    array_push($userParams, $params[$value]);
                }
                $userInputFormat = self::CORE_ENDPOINTS_PROMPT[$route]['content'];
                $userInputContent = sprintf($userInputFormat, ...$userParams);

                $complete = $this->openInstance->chat([
                    'messages' => [
                        [
                            "role" => "system",
                            "content" => self::CORE_CHATGPT_SYSTEM
                        ],
                        [
                            "role" => "user",
                            "content" => $userInputContent
                        ]
                    ],
                    'temperature' => 1.0,
                    'max_tokens' => 4000,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                ]);

                $d = json_decode($complete);

                if(isset($d->error->code) && $d->error->code == 'context_length_exceeded'){
                    return 'input_is_long';
                }

                $content = $d->choices[0]->message->content;

                update_option(self::AI_OUTPUT, sanitize_textarea_field($content));
                set_transient(self::NOTIFICATION_OPTION, "done", MINUTE_IN_SECONDS);
                return "ok";
            }
            return "error";
        }
        catch (Exception $exception){
            error_log(print_r($exception->getMessage(), 1));
            return "error";
        }
    }

    public function get_ai_output(){
        $response = [
            "status" => "",
            "output" => ""
        ];

        $transient = get_transient(self::NOTIFICATION_OPTION);

        if($transient === false){
            $response["status"] = "done";
            wp_send_json_success($response);
        }

        $response["status"] = esc_html($transient);

        if($transient !== "done") {
            wp_send_json_success($response);
        }

        $response['output'] = htmlspecialchars_decode(wp_kses_post(get_option(self::AI_OUTPUT, false)));
        delete_option(self::AI_OUTPUT);
        delete_transient(self::NOTIFICATION_OPTION);
        wp_send_json_success($response);
    }

    public static function check_permission(\WP_REST_Request $request){
        $nonce = $request->get_headers()['x_wp_nonce'][0];

        if(wp_verify_nonce($nonce, self::REST_NONCE_ACTION) === false) {
            return wp_send_json_error("invalid_nonce");
        }

        if(!current_user_can("edit_posts")) {
            return wp_send_json_error("permission_error");
        }

        return true;
    }

    public function check_tenweb_token(\WP_REST_Request $request){
        $auth_header = $request->get_header('tenweb_authorization');

        if(!$auth_header) {
            return false;
        }

        return TenWebApi::get_instance()->check_single_token($auth_header) === false;
    }

    public static function validate_not_empty($prompt){
        return !empty($prompt);
    }

    public static function get_instance(){
        if(null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
