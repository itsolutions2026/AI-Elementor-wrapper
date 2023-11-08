<?php
namespace Tenweb_Builder;

use AITenwebBuilder\Utils;
use Elementor\Core\Settings\Manager as SettingsManager;


class AITenwebBuilder {

  public $prompts = array();
  public $ai_actions = array();
  public $ai_tones = array();
  public $ai_translates = array();
  public $ai_error_data = array();

  const LIMITATION_OPTION = 'twbb_limitation';

  public function __construct() {
    $this->set_defaults();
    $this->enqueue_scripts();
    $this->view();
  }

  public function set_defaults() {
    include_once "Utils.php";
    $this->prompts = array(
      'text_prompts' => array(
        esc_html__('Write a product description for…','nerd-builder'),
        esc_html__('Draft a mission statement for…','nerd-builder'),
        esc_html__('Develop a set of FAQs for…','nerd-builder'),
        esc_html__('Craft compelling blog content for…','nerd-builder'),
        esc_html__('Develop a detailed ‘Services’ section for…','nerd-builder'),
      ),
      'headline_prompts' => array(
        esc_html__('Generate a compelling headline for','nerd-builder'),
        esc_html__('Create an engaging website heading for','nerd-builder'),
        esc_html__('Write a catchy website title for','nerd-builder'),
        esc_html__('Propose a dynamic website headline for','nerd-builder'),
        esc_html__('Invent a unique website heading for','nerd-builder'),
      ),
    );

    $this->ai_actions = array(
      'new_prompt' => array(
        'title' =>  esc_html__('Generate Text','nerd-builder'),
        'endpoint' => 'new_prompt'
      ),
      'simplify_language' => array(
        'title' =>  esc_html__('Simplify Language','nerd-builder'),
        'endpoint' => 'siplify_language'
      ),
      'make_it_longer' => array(
        'title' =>  esc_html__('Make it longer','nerd-builder'),
        'endpoint' => 'make_it_longer'
      ),
      'make_it_shorter' => array(
        'title' =>  esc_html__('Make it shorter','nerd-builder'),
        'endpoint' => 'make_it_shorter'
      ),
      'fix_spelling_and_grammar' => array(
        'title' =>  esc_html__('Fix spelling & grammar','nerd-builder'),
        'endpoint' => 'fix_spelling'
      ),
      'change_tone' => array(
        'title' =>  esc_html__('Change tone','nerd-builder'),
        'endpoint' => 'change_tone'
      ),
      'translate_to' => array(
        'title' =>  esc_html__('Translate to','nerd-builder'),
        'endpoint' => 'translate_to'
      ),
    );

    $this->ai_tones = array("Casual", "Confidence", "Formal", "Friendly", "Inspirational", "Motivational", "Nostalgic", "Playful",
      "Professional", "Scientific", "Straightforward", "Witty");

    $this->ai_translates = array("Arabic", "Chinese", "Czech", "Danish", "Dutch", "English", "Finnish", "French", "German",
      "Greek", "Hebrew", "Hungarian", "Indonesian", "Italian", "Japanese", "Korean", "Persian", "Polish", "Portuguese", "Russian",
      "Spanish", "Swedish", "Thai", "Turkish", "Vietnamese");

    $this->ai_error_data = array(
      'free_limit_reached' => array(
        'text' => __('You have reached your monthly limit of Free Plan. Upgrade to a higher plan to continue using AI Assistant.', 'nerd-builder'),
      ),
      'plan_limit_reached' => array(
        'text' => __('You have reached your monthly limit for the Personal Plan. Upgrade to a higher plan to continue using AI Assistant.', 'nerd-builder'),
      ),
      'permission_error' => array(
        'text' => __('You cannot edit this page because you do not have the necessary permissions. Please log in with an administrator account to proceed.', 'nerd-builder'),
      ),
      'there_is_in_progress_request' => array(
        'text' => __('It seems like another text generation request is in progress. Please retry once its finished.', 'nerd-builder'),
      ),
      'input_is_long' => array(
        'text' => __('Selected text is too long, please select a short text and try again.', 'nerd-builder'),
      ),
      'something_wrong' => array(
        'text' => __('There was an issue while attempting to access 10Web services. Please try again later.', 'nerd-builder'),
      ),
    );

  }

  public function enqueue_scripts() {
    $limitation_data  = $this->get_limitation_data();
    $total_allowed_words = !empty ($limitation['planLimit']) ? intval($limitation['planLimit']) : 0;
    $ui_theme_selected = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );
    wp_enqueue_script( 'twbb-ai-js', TWBB_URL . '/ai/assets/js/script.js', [ 'jquery' ], TWBB_VERSION, TRUE );
    wp_localize_script('twbb-ai-js', 'twbb_admin_vars', array(
      'ajaxurl' => admin_url('admin-ajax.php'),
      'ajaxnonce' => wp_create_nonce('wp_rest'),
      "rest_route" => get_rest_url(null, 'ai-builder-nerd/ai'),
      "notification_status" => get_transient('twbb_notification'),
      'limitation_expired' => $limitation_data['limitation_expired'],
      'plan' => \Tenweb_Builder\Utils::is_free( $total_allowed_words ) ? 'Free' : '',
      'error_data' => $this->ai_error_data,
      'twbb_ui_theme' => $ui_theme_selected,
    ));

    wp_enqueue_script( 'twbb-ai-main-js', TWBB_URL . '/ai/assets/js/request.js', [ 'jquery' ], TWBB_VERSION, TRUE );
    wp_register_style('twbb-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700,800&display=swap');
    wp_enqueue_style( 'twbb-ai-css', TWBB_URL . '/ai/assets/css/style.css', array('twbb-open-sans'), TWBB_VERSION );
    wp_enqueue_style('twbb-ai-css-dark', TWBB_URL . '/ai/assets/css/dark_style.css', array(), TWBB_VERSION);
  }

  /**
   * Get limitation expired or not and plan title
   *
   * @return array
   */
  public function get_limitation_data() {
    $limitation = \Tenweb_Builder\Utils::get_limitation();
    if ( !empty($limitation) && ($limitation['planLimit'] <= $limitation['alreadyUsed']) )  {
      return array(
        'limitation_expired'  => 1,
        'plan' => $limitation['planTitle'],
      );
    }
    return array(
      'limitation_expired'  => 0,
      'plan' => isset($limitation['planTitle']) ? $limitation['planTitle'] : __('Free', 'nerd-builder'),
    );
  }

  public function view() {
    require_once 'view.php';
  }
}