<?php
namespace Tenweb_Builder;

class CLI {
  protected static $instance = null;

  public static function get_instance() {
    if ( self::$instance === null ) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function __construct() {
    if ( class_exists('WP_CLI') ) {
      \WP_CLI::add_command('10web-import-template', [ $this, 'import_template' ]);
      \WP_CLI::add_command('10web-finalize-import', [ $this, 'finalize_import' ]);
      \WP_CLI::add_command('10web-generate-attach-meta-data', [ $this, 'generate_attachment_meta_data' ]);
    }
  }


  /**
   * @param $args
   * @param $assoc_args
   *
   * [--template_id=int]
   * : Predefined template id
   *
   * [--template_url=string]
   * : AI recreated template url
   *
   * @return void
   */
  public function import_template( $args, $assoc_args ) {
    require_once TWBB_DIR . "/templates/import/import.php";

    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";

    $import = new Import($import_type);
    $result = $import->import_site_data( $assoc_args );
    if ( !is_wp_error( $result ) ) {
      \WP_CLI::success( get_option('twbb_last_imported_pageID') );
    }
    else {
      update_option("twbb_import_error", $result->get_error_message());
      \WP_CLI::error( $result->get_error_code() );
    }
  }

  public function finalize_import( $args, $assoc_args ) {
    require_once TWBB_DIR . "/templates/import/import.php";
    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";
    $import = new Import($import_type);
    //finalize_import
    $import->finalize_import( $assoc_args[ 'template_id' ] , '');
    \WP_CLI::success( 'Template Import Finalized');
  }

  public function generate_attachment_meta_data($args, $assoc_args){
    require_once TWBB_DIR . "/templates/import/import.php";
    $import_type = isset($assoc_args['import_type']) ? $assoc_args['import_type'] : "default";
    $import = new Import($import_type);
    $import->generate_attachment_meta_data();
    \WP_CLI::success( 'Attachments meta data is generated');
  }
}