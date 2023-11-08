<?php

namespace Tenweb_Builder;
class Builder {
	public static $prefix = '';
	public static $version = '';
	protected static $instance = NULL;
	private $widgets_list = array();
	private $group_widgets_list = array();
	private $custom_options = array();
	private $tags_list = array();

	private function __construct() {
		self::$prefix             = TWBB_PREFIX;
		self::$version            = TWBB_VERSION;
		$this->widgets_list       = twbb_get_widgets();
		$this->group_widgets_list = twbb_get_group_widgets();
		$this->tags_list          = twbb_get_tags();
		if ( get_option( 'twbb_version' ) !== TWBB_VERSION ) {
			self::install();
		}

		add_post_type_support( 'page', 'excerpt' );
		load_plugin_textdomain( 'nerd-builder', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        //add_action('admin_notices', array($this,'generate_admin_notices'));

		if ( ! $this->check_elementor_compatibility() ) {
			add_action( 'admin_notices', array( $this, 'elementor_compatibility_notice' ) );
		} else {
			if ( defined( 'ELEMENTOR_PATH' ) ) {
				include_once 'templates/templates.php';
				Templates::get_instance();
				$this->register_hooks();
				if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) ) {
					$this->remove_elementor_upsell();
					$this->custom_options     = get_custom_options();
				}
				include_once TWBB_DIR . '/classes/woocommerce.php';
				\Tenweb_Builder\Classes\Woocommerce\Woocommerce::get_instance();
			}
		}
        $this->connect_with_rest();
    }


  public static function install() {
		$version = get_option( 'twbb_version' );
		if ( $version === FALSE ) {
			$version = '0.0.0';
		}
		if ( version_compare( $version, TWBB_VERSION, '<=' ) ) {
			//todo
		}
		update_option( 'twbb_version', TWBB_VERSION );
	}

    public function generate_admin_notices() {
        $error_logs = \Tenweb_Authorization\Helper::get_error_logs();
        if(!empty($error_logs)){
            foreach($error_logs as $error_key => $error) {
                echo '<div class="notice notice-error"><p>'.$error["msg"].'</p></div>';
            }
        }

    }

    public function connect_with_rest() {
        include_once 'rest_api.php';
        $restApi = new Twbb_RestApi;
    }

    private function check_elementor_compatibility() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, TWBB_ELEMENTOR_MIN_VERSION, '<' ) || ! did_action( 'elementor/loaded' ) ) {
			return FALSE;
		}

		return TRUE;
	}

	// @TODO Temporary solution.
	private function register_hooks() {
		$this->register_controls();
        add_action( 'wp_ajax_twbb_remove_sidebar', array( $this, 'remove_sidebar' ) );
		add_action( 'wp_ajax_twbb_widgets', array( $this, 'widgets_ajax' ) );
		add_action( 'wp_ajax_nopriv_twbb_widgets', array( $this, 'widgets_ajax' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_category' ), 9 );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 10 );
		add_action( 'elementor/controls/controls_registered', array( $this, 'register_custom_options' ), 10 );
		/* @TODO SCRIPTS AND STYLES */
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
		/* wp_footer action's third parameter need to be elementor's 'wp_footer' actions third parameter +1 */
		add_action( 'wp_footer', [ $this, 'enqueue_frontend_scripts' ], 12 );
		/* @TODO FIRES AFTER ELEMENTOR EDITOR STYLES AND SCRIPTS ARE ENQUEUED. */
		//fires after elementor editor styles and scripts are enqueued.
		add_filter( 'tw_get_elementor_assets', array( $this, 'register_elementor_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'admin_bar_menu', [ $this, 'add_toolbar_items' ], 500 );
		add_action( 'wp_ajax_popup_template_ajax', array( $this, 'popup_template_ajax_action' ) );
		add_action( 'wp_ajax_remove_template_ajax', array( $this, 'remove_template_ajax_action' ) );
		add_action( 'wp_ajax_trigger_conditions', array( $this, 'trigger_conditions_admin_ajax' ) );
		add_action( 'elementor/init', array( $this, 'elementor_init' ) );
		add_action( 'admin_init', array( $this, 'hide_elementor_upgrade_notice' ) );
		add_filter( 'elementor/widget/render_content', [ $this, 'remove_powered_by' ], 10, 3 );

        /* if menu item is created by AI or imported, remove class after editing it */
        add_action( 'wp_update_nav_menu_item',function($menu_id,$menu_item_id){
            $saved_menu = get_option('imported_nav_menu_' . $menu_id);
            $updated_menu = wp_get_nav_menu_items($menu_id);
            foreach ( $updated_menu as $key => $new_item ) {
                if ( $new_item->ID == $menu_item_id ) {
                    foreach ( $saved_menu as $old_item ) {
                        if ( $old_item->ID == $menu_item_id ) {
                            if ( $old_item->post_title != $new_item->post_title ||
                                $old_item->title != $new_item->title ||
                                $old_item->url != $new_item->url ) {
                                $item_classes = get_post_meta($menu_item_id,'_menu_item_classes')[0];
                                if (($key = array_search('ai-recreated-menu-item', $item_classes)) !== false) {
                                    unset($item_classes[$key]);
                                }
                                update_post_meta($menu_item_id,'_menu_item_classes',$item_classes);
                            }
                        }
                    }
                }
            }
        }, 10, 2 );

        include_once 'includes/cli.php';
        CLI::get_instance();

        if ( TW_HOSTED_ON_10WEB ) {
          add_action('elementor/editor/footer', array( $this, 'twbb_ai' ));
        }
	}

  function twbb_ai() {
    require_once 'ai/ai.php';
    new AITenwebBuilder();
  }



  public function hide_elementor_upgrade_notice() {
    update_option( '_elementor_editor_upgrade_notice_dismissed', 1 );
  }

	public function register_controls() {
		include_once 'controls/select-ajax/controller.php';
		SelectAjaxController::get_instance();
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Group_Control_Base' ) ) {
			include_once 'controls/query-control/controller.php';
			include_once 'controls/query-control/controls/group-control-posts.php';
			\Tenweb_Builder\Controls\QueryControl\QueryController::get_instance();
		}
	}

	private function remove_elementor_upsell() {
		include_once 'includes/remove-upsell.php';
		RemoveUpsell::get_instance();
	}

	/* Remove last imported template*/
	public static function get_instance() {
		if ( self::$instance === NULL ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function trigger_conditions_admin_ajax() {
		$post_id = Helper::get( 'post_id' );
		include_once 'templates/condition/admin-condition.php';
		$admincondition = new AdminCondition();
		$admincondition->admin_condition_popup( $post_id );
	}

	public function remove_powered_by( $content ) {
		$re = '/(Powered by 10Web)|(<a ?.*>Powered by 10Web<\/a>)/mi';
		preg_match( $re, $content, $matches, PREG_OFFSET_CAPTURE, 0 );
		if ( ! empty( $matches ) ) {
			$content = '';
		}

		return $content;
	}

	public function elementor_init() {
		require_once TWBB_DIR . '/dynamic-tags/module.php';
		new DynamicTags\Module();
		require_once TWBB_DIR . '/pro-features/ElementorPro.php';
		ElementorPro\ElementorPro::get_instance();
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( ! empty( $this->widgets_list ) ) {
				$widgets = array_merge( $this->widgets_list, $this->custom_options );
				foreach ( $widgets as $widget_name => $widget_data ) {
					if ( isset( $widget_data[ 'oninit' ] ) && $widget_data[ 'oninit' ] ) {
						$file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
						if ( is_file( $file ) ) {
							require_once $file;
						}
					}
				}
			}
			if ( ! empty( $this->group_widgets_list ) ) {
				foreach ( $this->group_widgets_list as $module_name => $widget_data ) {
					$file = TWBB_DIR . '/widgets/' . $module_name . '/module.php';
					if ( is_file( $file ) ) {
						require_once $file;
					}
				}
			}
		}
	}


	public function remove_template_ajax_action() {
		if ( ! isset( $_GET[ 'twbb_nonce' ] ) || ( isset( $_GET[ 'twbb_nonce' ] ) && ! wp_verify_nonce( $_GET[ 'twbb_nonce' ], 'twbb_remove_template_ajax' ) ) ) {
			$wp_error[ "message" ] = __( "You have no permission for the action", 'nerd-builder' );
			$wp_error[ "status" ]  = "error";
			echo json_encode( $wp_error );
			die();
		}
		include_once 'templates/import/import.php';
		$args = array(
			'posts'       => 'delete',
			'attachments' => '1',
			'terms'       => '1',
			'menus'       => '1',
			'options'     => '1',
		);
		Import::delete_last_imported_site_data( $args , 'twbb_imported_site_data_generated');
	}

	public function popup_template_ajax_action() {
		include_once 'templates/popupTemplates.php';
		$task = Helper::get( 'task' );
		switch ( $task ) {
			case 'save_lacaly':
				PopupTemplates::get_instance()->twbb_dublicate_teplate_post();
				break;
			case 'save_popup':
				if ( Helper::get( 'header_template' ) === '0' || Helper::get( 'footer_template' ) === '0' || Helper::get( 'single_template' ) === '0' || Helper::get( 'archive_template' ) === '0' ) {
					$param = "exclude";
				} else {
					$param = "include";
				}
				PopupTemplates::get_instance()->twbb_save_templates( $param );
		}
	}

	public function add_toolbar_items( \WP_Admin_Bar $admin_bar ) {
		if ( ! is_admin() ) {
			$edit_url       = $this->get_edit_url();
			if ( ! TENWEB_WHITE_LABEL ) {
				$admin_bar->remove_menu( 'elementor_edit_page' );
				if ( is_singular() ) {
					$document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( get_the_ID() );
					if ( $document && $document->is_editable_by_current_user() ) {
						$admin_bar->add_menu( array(
							                      'id'    => 'twbb_builder',
							                      'class' => 'admin_bar-twbb_builder',
							                      'title' => __( 'Edit with 10Web Builder', 'nerd-builder' ),
							                      'href'  => $edit_url,
						                      ) );
					}
					if ( is_singular( array( 'product' ) ) ) {
						$loaded_templates = Templates::get_instance()->get_loaded_templates();
						if ( array_key_exists( 'twbb_single', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_single' ] ) ) {
							$template_id = $loaded_templates[ 'twbb_single' ];
							$document    = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $template_id );
							if ( $document && $document->is_editable_by_current_user() ) {
								$admin_bar->add_menu( array(
									                      'id'    => 'twbb_builder',
									                      'class' => 'admin_bar-twbb_builder',
									                      'title' => __( 'Edit Product template with 10Web Builder', 'nerd-builder' ),
									                      'href'  => $edit_url,
									                      'meta'  => array( 'target' => '_blank' ),
								                      ) );
							}
						}
					}
				} else {
					$loaded_templates = Templates::get_instance()->get_loaded_templates();
					if ( array_key_exists( 'twbb_archive', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_archive' ] ) ) {
						$archive_id = $loaded_templates[ 'twbb_archive' ];
						$document   = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $archive_id );
						if ( $document && $document->is_editable_by_current_user() ) {
							$admin_bar->add_menu( array(
								                      'id'    => 'twbb_builder',
								                      'class' => 'admin_bar-twbb_builder',
								                      'title' => __( 'Edit Archive template with 10Web Builder', 'nerd-builder' ),
								                      'href'  => $edit_url,
								                      'meta'  => array( 'target' => '_blank' ),
							                      ) );
						}
					}
				}
			}

          if ($edit_url && 1 == get_post_meta( get_the_ID(), 'twbb_ai_created', TRUE )
            && (isset( $_GET['preview_sidebar'] ) || get_option('twbb_sidebar')) ) {
            ?>
        <script type="text/javascript">
            let url, new_url;
            url = window.location.href;
            new_url = url.split('?')[0];
            window.history.pushState({}, document.title, new_url);
        </script>
        <style>
            html {
                margin-top: 0 !important;
            }

            #wpadminbar {
                display: none;
            }
        </style>
        <?php

                include_once "includes/PreviewUpgrade/PreviewUpgrade.php";
                new PreviewUpgrade();

                update_option( 'twbb_sidebar', true );
			}
		}

  }

	public static function get_edit_url() {
		if ( ! is_admin() ) {
			$edit_url = FALSE;
			if ( is_singular() ) {
				$document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( get_the_ID() );
				if ( $document && $document->is_editable_by_current_user() ) {
					$edit_url = $document->get_edit_url();
				}
				if ( is_singular( array( 'product' ) ) ) {
					$loaded_templates = Templates::get_instance()->get_loaded_templates();
					if ( array_key_exists( 'twbb_single', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_single' ] ) ) {
						$template_id = $loaded_templates[ 'twbb_single' ];
						$document    = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $template_id );
						if ( $document && $document->is_editable_by_current_user() ) {
							$edit_url = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
						}
					}
				}
			} else {
				$loaded_templates = Templates::get_instance()->get_loaded_templates();
				if ( array_key_exists( 'twbb_archive', $loaded_templates ) && ! empty( $loaded_templates[ 'twbb_archive' ] ) ) {
					$archive_id = $loaded_templates[ 'twbb_archive' ];
					$document   = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $archive_id );
					if ( $document && $document->is_editable_by_current_user() ) {
						$edit_url = admin_url( 'post.php?post=' . $archive_id . '&action=elementor' );
					}
				}
			}

			return $edit_url;
		}
	}

	public function register_widgets() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( ! empty( $this->widgets_list ) ) {
				$isExternal = FALSE;
				foreach ( $this->widgets_list as $widget_name => $widget_data ) {
					if ( ! isset( $widget_data[ 'oninit' ] ) || ! $widget_data[ 'oninit' ] ) {
						if ( isset( $widget_data[ 'external' ] ) && $widget_data[ 'external' ] ) {
							if ( isset( $widget_data[ 'class_name' ] ) && ! class_exists( $widget_data[ 'class_name' ] ) ) {
								$isExternal = TRUE;
								require_once TWBB_DIR . '/widgets/external/external.php';
								$external_widget = new External();
								$external_widget->set( $widget_data );
								\Elementor\Plugin::instance()->widgets_manager->register( $external_widget );
							}
						} else {
							$file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
							if ( is_file( $file ) ) {
								require_once $file;
							}
						}
					}
				}
				if ( $isExternal && class_exists( '\Tenweb_Manager\Manager' ) && is_admin() ) {
					wp_enqueue_script( 'twbb-control-external-ajax', TWBB_URL . '/assets/editor/js/external-ajax.js', [ 'jquery' ], TWBB_VERSION );
					$rest_route = add_query_arg( array(
						                             'rest_route' => '/' . TENWEB_REST_NAMESPACE . '/action',
					                             ), get_home_url() . "/" );
					wp_localize_script( 'twbb-control-external-ajax', 'twbb', array(
						'ajaxurl'          => admin_url( 'admin-ajax.php' ),
						'ajaxnonce'        => wp_create_nonce( 'wp_rest' ),
						'plugin_url'       => TENWEB_URL,
						'action_endpoint'  => $rest_route,
						'install_success'  => __( 'The plugin was successfully installed and activated. Please save your changes and reload the page for using the widget', 'nerd-builder' ),
						'activate_success' => __( 'The plugin was successfully activated. Please save your changes and reload the page for using the widget', 'nerd-builder' ),
						'update_success'   => __( 'The plugin was successfully updated. Please save your changes and reload the page for using the widget', 'nerd-builder' ),
						'reload_msg'       => __( 'Please save your changes and reload the page for using the widget', 'nerd-builder' ),
						'inprogress_msg'   => __( 'Some plugin is in the process of being activated or installed.', 'nerd-builder' ),
					) );
				}
			}
		}

  }

	public function register_custom_options() {
		if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
			if ( ! empty( $this->custom_options ) ) {
				foreach ( $this->custom_options as $widget_name => $widget_data ) {
					if ( ! isset( $widget_data[ 'oninit' ] ) || ! $widget_data[ 'oninit' ] ) {
						$file = TWBB_DIR . '/widgets/' . $widget_name . '/' . $widget_name . '.php';
						if ( is_file( $file ) ) {
							require_once $file;
						}
					}
				}
			}

    }
	}

	public function widgets_ajax() {
		if ( ! check_ajax_referer( 'twbb', 'nonce' ) || ! isset( $_REQUEST[ 'widget_name' ] ) ) {
			wp_send_json_error();
		}
		$widget = twbb_get_widgets( $_REQUEST[ 'widget_name' ] );
		if ( ! isset( $widget[ 'ajax' ] ) || $widget[ 'ajax' ] !== TRUE ) {
			wp_send_json_error();
		}
		$file = TWBB_DIR . '/widgets/' . $_REQUEST[ 'widget_name' ] . '/' . $_REQUEST[ 'widget_name' ] . '.php';
		if ( is_file( $file ) ) {
			require_once $file;
			$class_name = "\Tenweb_Builder\\" . ucfirst( $_REQUEST[ 'widget_name' ] );
			$method     = 'twbb_ajax';
			if ( method_exists( $class_name, $method ) ) {
				$class_name::$method();
			}
		}
	}

	/**
	 * @param $elements_manager \Elementor\Elements_Manager
	 * */
	public function register_widget_category( $elements_manager ) {
		$company_name = ' by 10Web';
		if ( TENWEB_WHITE_LABEL ) {
			$company_name = '';
		}
		$elements_manager->add_category(
			'tenweb-widgets',
			[
				'title' => __( 'Premium Widgets' . $company_name, 'nerd-builder' ),
				'icon'  => 'fa fa-plug',
			]
		);
		$elements_manager->add_category(
			'tenweb-plugins-widgets',
			[
				'title' => __( '10WEB Plugins', 'nerd-builder' ),
				'icon'  => 'fa fa-plug',
			]
		);
		/* show sections only on template page! */
        if (Templates::get_instance()->is_elementor_template_type()) {
            $elements_manager->add_category(
                'nerd-builder-widgets',
                [
                    'title' => __('Site Builder Widgets' . $company_name, 'nerd-builder'),
                    'icon' => 'fa fa-plug',
                ]
            );
            $elements_manager->add_category(
                'tenweb-woocommerce-builder-widgets',
                [
                    'title' => __('Woocommerce Builder Widgets' . $company_name, 'nerd-builder'),
                    'icon' => 'fa fa-plug',
                ]
            );
        }
		$elements_manager->add_category(
			'tenweb-woocommerce-widgets',
			[
				'title' => __( 'Woocommerce Widgets' . $company_name, 'nerd-builder' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	public function enqueue_frontend_scripts() {
		// For post archive widget.
		wp_enqueue_script( 'underscore' );
		// Do not include admin scripts to front.
		if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			$handle_editor = 'twbb-common-js';
			wp_enqueue_script( 'jquery-elementor-select2' );
			wp_enqueue_script( 'twbb-common-js', TWBB_URL . '/assets/common/js/common.js', [ 'jquery' ], TWBB_VERSION, TRUE );
			wp_enqueue_script( 'twbb-condition-js', TWBB_URL . '/assets/editor/js/condition.js', [ 'jquery' ], TWBB_VERSION, TRUE );
			wp_enqueue_style( 'twbb-common', TWBB_URL . '/assets/common/css/common.css', array(), TWBB_VERSION );
			wp_enqueue_style( 'twbb-condition', TWBB_URL . '/assets/editor/css/condition.css', array(), TWBB_VERSION );
			$rest_route         = add_query_arg( array( 'rest_route' => '/' ), get_home_url() . "/" );
			$twbb_template_type = Templates::get_instance()->is_twbb_template()[ 'template_type' ];
			$header_button      = Templates::get_instance()->is_twbb_template()[ 'header_button_show' ];
			wp_localize_script( $handle_editor, 'twbb_options', array(
				'loaded_templates'    => Templates::get_instance()->get_loaded_templates(),
				'post_id'             => get_the_ID(),
				'current_page'        => __( 'Current Page', 'nerd-builder' ),
				'entire_site'         => __( 'Entire Site', 'nerd-builder' ),
				'singular'            => __( 'Singular', 'nerd-builder' ),
				'archive'             => __( 'Archive', 'nerd-builder' ),
				'choose'              => __( 'Choose', 'nerd-builder' ),
				'template'            => __( 'template', 'nerd-builder' ),
				'twbb_page_type'      => Condition::get_instance()->get_page_type(),
				'edit'                => __( 'Edit', 'nerd-builder' ),
				'edit_localy'         => __( 'Edit Localy', 'nerd-builder' ),
				'edit_url'            => admin_url( 'post.php?post={post_id}&action=elementor' ),
				'popup_template_ajax' => add_query_arg( array( 'action' => 'popup_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
				'is_post_template'    => ( get_post_type( get_the_ID() ) == 'elementor_library' ? 1 : 0 ),
				'header_button'       => $header_button,
				'twbb_header'         => __( 'Edit Header Template', 'nerd-builder' ),
				'twbb_footer'         => __( 'Edit Footer Template', 'nerd-builder' ),
				'twbb_single'         => __( 'Edit Single Template', 'nerd-builder' ),
				'twbb_archive'        => __( 'Edit Archive Template', 'nerd-builder' ),
				'twbb_template_type'  => $twbb_template_type,
				'plugin_url'          => plugin_dir_url( __FILE__ ),
			) );
			wp_localize_script( $handle_editor, 'twbb_editor', array(
				'texts'              => array(
					'include'           => __( 'Include', 'nerd-builder' ),
					'exclude'           => __( 'Exclude', 'nerd-builder' ),
					'general'           => __( 'Entire Site', 'nerd-builder' ),
					'archive'           => __( 'Archive', 'nerd-builder' ),
					'singular'          => __( 'Singular', 'nerd-builder' ),
					'are_your_sure'     => __( 'Are you sure?', 'nerd-builder' ),
					'condition_removed' => __( 'A condition has been removed.', 'nerd-builder' ),
					'content_missing'   => __( 'Warning: There are no content widgets in this Single template. Please make sure to add some.', 'nerd-builder' ),
					'publish'           => __( 'Publish', 'nerd-builder' ),
					'continue'          => __( 'Continue', 'nerd-builder' ),
				),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'rest_route'         => $rest_route,
				'rest_nonce'         => wp_create_nonce( 'wp_rest' ),
				'post_id'            => get_the_ID(),
				'conditions'         => Condition::get_instance()->get_template_condition( get_the_ID(), 'all', TRUE ),
				'twbb_template_type' => $twbb_template_type,
			) );
		}
		$handle_frontend = 'twbb-frontend-scripts';
		if ( TWBB_DEV === FALSE ) {
			if ( TWBB_DEBUG === TRUE ) {
				wp_enqueue_script( 'twbb-frontend-scripts', TWBB_URL . '/assets/frontend/js/frontend.js', array(
					'elementor-frontend-modules',
					'imagesloaded',
					'masonry',
				),                 TWBB_VERSION );
			} else {
				wp_enqueue_script( 'twbb-frontend-scripts', TWBB_URL . '/assets/frontend/js/frontend.min.js', array(
					'elementor-frontend-modules',
					'imagesloaded',
					'masonry',
				),                 TWBB_VERSION );
			}
		} else {
			$handle_frontend = 'twbb-posts-scripts';
			$widgets         = array_merge( $this->widgets_list, $this->custom_options );
			foreach ( $widgets as $widget_data ) {
				if ( empty( $widget_data[ 'scripts' ] ) ) {
					continue;
				}
				foreach ( $widget_data[ 'scripts' ] as $handle => $script_data ) {
					wp_enqueue_script( 'twbb-' . $handle . '-scripts', $script_data[ 'src' ], $script_data[ 'deps' ], TWBB_VERSION, TRUE );
				}
			}
		}
		wp_localize_script( $handle_frontend, 'twbb', array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'home_url' => home_url(),
			'nonce'    => wp_create_nonce( 'twbb' ),
            'tenweb_dashboard' => TENWEB_DASHBOARD,
            'dashboard_website_id' => get_option('tenweb_domain_id'),
		) );

		do_action( 'twbb_after_enqueue_scripts', $handle_frontend );
		if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			include_once 'includes/quick-navigation.php';
			$structure = new Quick_Navigation();
			$structure->twbb_custom_header();
			$structure->twbb_template_popup();
		}
		if ( ! TENWEB_WHITE_LABEL ) {
			/* remove 'Edit with Elementor' from admin bar */
			wp_dequeue_script( 'elementor-admin-bar' );
		}
	}

	public function enqueue_frontend_styles() {
		if ( TWBB_DEV === FALSE ) {
			wp_enqueue_style( 'twbb-frontend-styles', TWBB_URL . '/assets/frontend/css/frontend.css', array(
				'elementor-icons'
			),                TWBB_VERSION );
			// Ensure the images remain visible while meta information is being generated after import.
			if ( get_option( 'tenweb_import_in_progress' ) ) {
				wp_add_inline_style( 'twbb-frontend-styles', 'img {width: initial !important; height: initial !important;}' );
			}
		}
        else {
			wp_enqueue_style( 'twbb-fonts', TWBB_URL . '/assets/frontend/css/fonts.css', array(), TWBB_VERSION );
			$widgets = array_merge( $this->widgets_list, $this->custom_options );
			foreach ( $widgets as $widget_data ) {
				if ( empty( $widget_data[ 'styles' ] ) ) {
					continue;
				}
				foreach ( $widget_data[ 'styles' ] as $handle => $style_data ) {
					if ( is_array( $style_data ) ) {
						wp_enqueue_style( 'twbb-' . $handle . '-style', $style_data[ 'src' ], $style_data[ 'deps' ], TWBB_VERSION );
					} else {
						wp_enqueue_style( $handle );
					}
				}
			}
		}
        wp_enqueue_style( 'twbb-frontend-global-styles', TWBB_URL . '/assets/frontend/css/global_frontend.css', array(),TWBB_VERSION );
		do_action( 'twbb_after_enqueue_styles' );
	}

	public function register_elementor_assets( $assets ) {
		$version = '2.0.2';
		if ( ! isset( $assets[ 'version' ] ) || version_compare( $assets[ 'version' ], $version ) === - 1 ) {
			$assets[ 'version' ]  = $version;
			$assets[ 'css_path' ] = TWBB_URL . '/assets/frontend/css/fonts.css';
		}

		return $assets;
	}

	public function enqueue_editor_styles() {
	    $handle_for_old_version = "";
		if ( TWBB_DEV === FALSE ) {
			wp_enqueue_style( 'twbb-admin-styles', TWBB_URL . '/assets/editor/css/editor.min.css', array(), TWBB_VERSION );
      self::hide_elementor_AI( 'twbb-admin-styles' );
            $handle_for_old_version = "twbb-admin-styles";
        } else {
            $handle_for_old_version = "twbb-common";
            $key = 'twbb-editor-styles';
			wp_deregister_style( $key );
			$assets = apply_filters( 'tw_get_elementor_assets', array() );
			wp_enqueue_style( $key, $assets[ 'css_path' ], array(), $assets[ 'version' ] );
			wp_enqueue_style( 'twbb-el-editor-styles', TWBB_URL . '/assets/editor/css/editor.css', array(), TWBB_VERSION );
      self::hide_elementor_AI( 'twbb-el-editor-styles' );
      wp_enqueue_style( 'twbb-condition', TWBB_URL . '/assets/editor/css/condition.css', array(), TWBB_VERSION );
			wp_enqueue_style( 'twbb-common', TWBB_URL . '/assets/common/css/common.css', array(), TWBB_VERSION );
            wp_enqueue_style( 'twbb-frontend-global-styles', TWBB_URL . '/assets/frontend/css/global_frontend.css', array(),TWBB_VERSION );
		}
		// Compatibility with Font awesome 5, remove once Elementor deprecates fa4.
		wp_enqueue_style( 'font-awesome-5-all', self::get_fa_asset_url( 'all' ), array(), ELEMENTOR_VERSION );

		if(defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.12.0') == -1) {
		    wp_add_inline_style($handle_for_old_version, "#elementor-panel #elementor-panel-content-wrapper {top: 60px !important;}");
		}

		if ( TENWEB_WHITE_LABEL ) {
			wp_enqueue_style( 'twbb-white-label', TWBB_URL . '/assets/common/css/white_label.css', array(), TWBB_VERSION );
		}

	}

  /**
   * Hide Elementor AI suggestion buttons if Pro version is not active.
   *
   * @param $style_hook string
   */
  public static function hide_elementor_AI( $style_hook ) {
    $custom_css = "#e-announcements-root, .e-ai-button:not(.twb-ai-button) { display: none!important; }";
    wp_add_inline_style( $style_hook, $custom_css );
  }

	public static function get_fa_asset_url( $filename, $ext_type = 'css', $add_suffix = TRUE ) {
		static $is_test_mode = NULL;
		if ( NULL === $is_test_mode ) {
			$is_test_mode = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS;
		}
		$url = ELEMENTOR_ASSETS_URL . 'lib/font-awesome/' . $ext_type . '/' . $filename;
		if ( ! $is_test_mode && $add_suffix ) {
			$url .= '.min';
		}

		return $url . '.' . $ext_type;
	}

	public function enqueue_editor_scripts() {
		if ( TWBB_DEV === FALSE ) {
			$requirements = array(
				'jquery',
				'backbone-marionette',
				'elementor-common-modules',
				'elementor-common',
				'elementor-editor-modules',
				'elementor-editor-document',
			);
			if ( TWBB_DEBUG === TRUE ) {
				wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor-tenweb.js', $requirements, TWBB_VERSION, TRUE );
			} else {
				wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor-tenweb.min.js', $requirements, TWBB_VERSION, TRUE );
			}
		} else {
			foreach ( $this->widgets_list as $widget_data ) {
				if ( empty( $widget_data[ 'admin-scripts' ] ) ) {
					continue;
				}
				foreach ( $widget_data[ 'admin-scripts' ] as $handle => $script_data ) {
					wp_enqueue_script( 'twbb-' . $handle . '-admin-scripts', $script_data[ 'src' ], $script_data[ 'deps' ], TWBB_VERSION, TRUE );
				}
			}
			wp_enqueue_script( 'twbb-editor-scripts', TWBB_URL . '/assets/editor/js/editor.js', [ 'jquery' ], TWBB_VERSION, TRUE );
			wp_enqueue_script( 'twbb-condition-js', TWBB_URL . '/assets/editor/js/condition.js', [ 'jquery' ], TWBB_VERSION, TRUE );
			wp_enqueue_script( 'twbb-common-js', TWBB_URL . '/assets/common/js/common.js', [ 'jquery' ], TWBB_VERSION, TRUE );
		}
		$rest_route         = add_query_arg( array( 'rest_route' => '/' ), get_home_url() . "/" );
		$twbb_template_type = Templates::get_instance()->is_twbb_template()[ 'template_type' ];
		wp_localize_script( 'twbb-editor-scripts', 'twbb_editor', array(
			'texts'              => array(
				'include'           => __( 'Include', 'nerd-builder' ),
				'exclude'           => __( 'Exclude', 'nerd-builder' ),
				'general'           => __( 'Entire Site', 'nerd-builder' ),
				'archive'           => __( 'Archive', 'nerd-builder' ),
				'singular'          => __( 'Singular', 'nerd-builder' ),
				'are_your_sure'     => __( 'Are you sure?', 'nerd-builder' ),
				'condition_removed' => __( 'A condition has been removed.', 'nerd-builder' ),
				'content_missing'   => __( 'Warning: There are no content widgets in this Single template. Please make sure to add some.', 'nerd-builder' ),
				'publish'           => __( 'Publish', 'nerd-builder' ),
				'continue'          => __( 'Continue', 'nerd-builder' ),
			),
			'ajax_url'           => admin_url( 'admin-ajax.php' ),
			'rest_route'         => $rest_route,
			'rest_nonce'         => wp_create_nonce( 'wp_rest' ),
			'post_id'            => get_the_ID(),
			'conditions'         => Condition::get_instance()->get_template_condition( get_the_ID(), 'all', TRUE ),
			'twbb_template_type' => $twbb_template_type,
		) );
		$edit_url         = admin_url( 'post.php?post={post_id}&action=elementor' );
		$is_post_template = ( get_post_type( get_the_ID() ) == 'elementor_library' ? 1 : 0 );
		$header_button    = Templates::get_instance()->is_twbb_template()[ 'header_button_show' ];
		wp_localize_script( 'twbb-editor-scripts', 'twbb_options', array(
			'ajaxurl'              => admin_url( 'admin-ajax.php' ),
			'nonce'                => wp_create_nonce( 'twbb' ),
			'loaded_templates'     => Templates::get_instance()->get_loaded_templates(),
			'rest_route'           => $rest_route,
			'rest_nonce'           => wp_create_nonce( 'wp_rest' ),
			'post_id'              => get_the_ID(),
			'edit_button_title'    => __( 'Edit Template', 'nerd-builder' ),
			'teplate_popup_title'  => __( 'Choose templates for your web site', 'nerd-builder' ),
			'current_page'         => __( 'Current Page', 'nerd-builder' ),
			'entire_site'          => __( 'Entire Site', 'nerd-builder' ),
			'singular'             => __( 'Singular', 'nerd-builder' ),
			'archive'              => __( 'Archive', 'nerd-builder' ),
			'choose'               => __( 'Choose', 'nerd-builder' ),
			'template'             => __( 'template', 'nerd-builder' ),
			'twbb_page_type'       => Condition::get_instance()->get_page_type(),
			'edit'                 => __( 'Edit', 'nerd-builder' ),
			'edit_localy'          => __( 'Edit Locally', 'nerd-builder' ),
			'edit_url'             => $edit_url,
			'edit_local_url'       => add_query_arg( array( 'action' => 'popup_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
			'popup_template_draw'  => add_query_arg( array( 'action' => 'draw_popup' ), admin_url( 'admin-ajax.php' ) ),
			'page_title'           => get_the_title(),
			'is_post_template'     => $is_post_template,
			'header_button'        => $header_button,
			'plugin_url'           => plugin_dir_url( __FILE__ ),
			'remove_template_ajax' => add_query_arg( array( 'action' => 'remove_template_ajax' ), admin_url( 'admin-ajax.php' ) ),
			'twbb_header'          => __( 'Edit Header Template', 'nerd-builder' ),
			'twbb_footer'          => __( 'Edit Footer Template', 'nerd-builder' ),
			'twbb_single'          => __( 'Edit Single Template', 'nerd-builder' ),
			'twbb_archive'         => __( 'Edit Archive Template', 'nerd-builder' ),
			'twbb_template_type'   => $twbb_template_type,
		) );
		do_action( 'twbb_after_enqueue_scripts' );
		do_action( 'twbb_before_enqueue_editor_scripts' );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_script( TWBB_PREFIX . '-admin-script', TWBB_URL . '/assets/admin/js/admin.js', [ 'jquery' ], TWBB_VERSION, TRUE );
		wp_localize_script( TWBB_PREFIX . '-admin-script', 'twbb_admin',
		                    array(
			                    'ajax_url' => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'twbb_remove_template_ajax', 'twbb_nonce' ),
		                    ) );
		wp_enqueue_style( TWBB_PREFIX . '-admin-style', TWBB_URL . '/assets/admin/css/admin.css', [], TWBB_VERSION );
	}

	public function elementor_compatibility_notice() {
		$elementor_notice = NULL;
		add_thickbox();
		$thickbox          = add_query_arg(
			array( 'tab' => 'plugin-information', 'plugin' => 'elementor', 'TB_iframe' => 'true' ),
			admin_url( 'plugin-install.php' )
		);
		$link              = "";
		$installed_plugins = get_plugins();
		if ( ! isset( $installed_plugins[ 'elementor/elementor.php' ] ) ) {
			$elementor_notice = __( '10Web Builder requires Elementor plugin. Please install and activate the latest version of %s plugin.', 'nerd-builder' );
			if ( isset( $_GET[ 'from' ] ) && '10web' == $_GET[ 'from' ] ) {
				$link   = 'thickbox';
				$script = '<script>jQuery(window).load(function() {jQuery("#twbb_install_elementor").trigger("click")});</script>';
			} else {
				$link = add_query_arg(
					array( 's' => 'elementor', 'tab' => 'search', 'type' => 'term', 'from' => '10web' ),
					admin_url( 'plugin-install.php' )
				);
			}
		} else if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			$elementor_notice = __( '10Web Builder requires Elementor plugin. Please activate %s plugin.', 'nerd-builder' );
			$link             = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=elementor/elementor.php&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_elementor/elementor.php' );
		} else if ( version_compare( ELEMENTOR_VERSION, TWBB_ELEMENTOR_MIN_VERSION, '<' ) ) {
			$elementor_notice = __( '10Web Builder requires latest version of Elementor plugin. Please update %s plugin.', 'nerd-builder' );
			$link             = 'thickbox';
		}
		if ( $elementor_notice !== NULL ) {

			if ( current_user_can( 'activate_plugins' ) ) {

				if ( $link == 'thickbox' ) {
					$link = '<a id="twbb_install_elementor" class="thickbox" href="' . $thickbox . '">Elementor</a>';
				} else {
					$link = '<a href="' . $link . '">Elementor</a>';
				}
			} else {
				$link = 'Elementor';
			}
			echo '<div class="error twbb_notice">' . sprintf( $elementor_notice, $link ) . "</div>";
			if ( isset( $script ) ) {
				echo $script;
			}
		}
	}

      public function remove_sidebar() {
        if ( ! check_ajax_referer( 'twb_pu_nonce', 'nonce' ) ) {
          wp_send_json_error();
        }

        delete_option( 'twbb_sidebar' );
      }

}

