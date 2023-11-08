<?php

namespace Tenweb_Builder;

class RemoveUpsell {

  protected static $instance = null;

  private function __construct() {
    add_action( 'admin_menu', [ $this, 'remove_go_pro_menu' ], 0 );
    add_action( 'admin_menu', [ $this, 'remove_submenus' ], 99999999999 );
    add_filter( 'plugin_action_links_' . ELEMENTOR_PLUGIN_BASE, [ $this, 'plugin_action_links' ], 99999999999 );
    // Using 'wp_print_footer_scripts' to have this code in Kit Library as well.
    add_action( 'wp_print_footer_scripts', [ $this, 'hide_upsell_in_admin' ] );
    add_action( 'admin_footer', [ $this, 'hide_upsell_in_admin' ] );
    add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'hide_upsell_in_front' ] );
    add_filter( 'elementor/editor/localize_settings', array( $this, 'localize_settings' ) );
    add_action( 'elementor/element/before_section_end', array( $this, 'remove_attributes_section' ), 10, 3 );

    update_option( 'elementor_allow_tracking', 'no' );
    update_option( 'elementor_tracker_notice', '1' );
    add_action( 'elementor/admin/menu/register', array($this,'remove_pro_menus'), 99999999999 );
  }

  public function remove_submenus(){
    /* remove Go Pro from Dashboard->Overview Footer */
    add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', function( $additions_actions ) {
      unset( $additions_actions['go-pro'] );
      unset( $additions_actions['find_an_expert'] );

      return $additions_actions;
    }, 550 );
  }

  public function remove_go_pro_menu() {
      remove_action( 'admin_menu', [ \Elementor\Plugin::instance()->settings, 'register_pro_menu' ], \Elementor\Settings::MENU_PRIORITY_GO_PRO );
  }

  /*
   * For Elementor up to 3.7
   */
  public function remove_pro_menus() {
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('go_elementor_pro');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('e-form-submissions');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_custom_code');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_fonts');
    \Elementor\Plugin::instance()->admin_menu_manager->unregister('elementor_custom_icons');
  }

  public function plugin_action_links($links){
    unset($links['go_pro']);

    return $links;
  }

  /**
   * Remove Elementor Pro promotion widgets from list.
   *
   * @param $settings
   * @return mixed
   */
  public function localize_settings( $settings ) {
    unset( $settings[ 'promotionWidgets' ] );

    return $settings;
  }

  public function remove_attributes_section($section, $section_id) {
    if( $section_id == 'section_custom_attributes_pro' ) {
      $section->remove_control('section_custom_attributes_pro');
    }
  }

  public function hide_upsell_in_admin() {
    if ( is_admin() ) {
      ?>
      <style>
        .elementor-role-row .elementor-role-go-pro,
        #menu-posts-elementor_library .elementor-app-link,
        .tenweb-editor .elementor-template-library-template-remote.elementor-template-library-pro-template,
        .elementor-control-type-text.elementor-control-address .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-number .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-gallery .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-control-type-slider .elementor-control-dynamic-switcher.elementor-control-unit-1,
        .elementor-color-picker__header .elementor-control-dynamic-switcher.e-control-tool,
        .elementor-control-background_size_width_height ul li:nth-child(2),
        .elementor-control-background_size_width_height ul li:nth-child(3),
        .elementor-control-background_size_width_height ul li:nth-child(5),
        .elementor-control-background_size_width_height.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .elementor-control-background_size_width_height_tablet ul li:nth-child(2),
        .elementor-control-background_size_width_height_tablet ul li:nth-child(3),
        .elementor-control-background_size_width_height_tablet ul li:nth-child(5),
        .elementor-control-background_size_width_height_tablet.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .elementor-control-background_size_width_height_mobile ul li:nth-child(2),
        .elementor-control-background_size_width_height_mobile ul li:nth-child(3),
        .elementor-control-background_size_width_height_mobile ul li:nth-child(5),
        .elementor-control-background_size_width_height_mobile.elementor-control-type-dimensions label.elementor-control-dimension-label,
        .tenweb-editor #e-notice-bar {
          display: none !important;
        }
      </style>
      <script>
        jQuery( window ).on( 'elementor:init', function () {
          /* Adding class to hide pro templates only if ElementorPro is not active.
           * had to do so as in Elementor version after 2.6.8 js events are not working
           * TODO: find a better solution. */
          jQuery( 'body' ).addClass( 'tenweb-editor' );

          /* Hook into templates show function. */
          var showTemplates = elementor.templates.showTemplates;
          elementor.templates.showTemplates = function () {
            elementor.templates.loadTemplates();
            tenweb_remove_pro_templates();
            showTemplates();
          }
          /* Remove 'Theme Builder' menu form sidebar & 'View Page' open in new tab */
          jQuery( document ).on( 'click', '#elementor-panel-header-menu-button', function () {
            jQuery( '.elementor-panel-menu-item.elementor-panel-menu-item-site-editor' ).remove();
            jQuery( '.elementor-panel-menu-item.elementor-panel-menu-item-view-page a' ).attr('target','_blank');
          } );
        } );

        /* Hide Pro and Expert kits from list. */
        jQuery( 'body' ).on( 'DOMSubtreeModified', '.eps-app__content.e-kit-library__index-layout-main', function() {
          jQuery( '.e-kit-library__kit-item-subscription-plan-badge' ).each( function () {
            jQuery( this ).parents( '.e-kit-library__kit-item' ).hide();
          } );
        } );

        /* Remove pro templates from template library. */
        function tenweb_remove_pro_templates() {
          if ( elementor.templates.getTemplatesCollection() ) {
            var arraha = false;
            elementor.templates.getTemplatesCollection().each( function (model) {
              if (model && model.get('isPro')) {
                elementor.templates.getTemplatesCollection().remove(model);
                arraha = true;
              }
            } );
            if ( arraha ) {
              tenweb_remove_pro_templates();
            }
          }
        }
      </script>
      <?php
    }
  }

  public function hide_upsell_in_front(){
    ?>
    <style>
      /*custom css*/
      .elementor-control.elementor-control-section_custom_css_pro {
          display: none;
      }
    </style>
    <?php
  }

  public static function get_instance(){
    if(self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

}
