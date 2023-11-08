<?php

namespace Tenweb_Builder;

class ImportSite {

  private $template_dir;
  private $template_url;

  private $options = [];
  private $files = [];
  private $version;
  private $attachments_folder;
  private $attachments_folder_url;
  private $replace_attachments = [];
  private $replace_terms = [];
  private $replace_posts = [];
  public $new_shortcodes = [];
  private $imported_data = [
    "posts" => [],
    "attachments" => [],
    "terms" => [],
    "menus" => [],
    "options" => [],
    "kit_id" => null,
    "home_page_id" => null
  ];
  private $errors = [];
  private $image_sizes = [];
  private $imported_nav_menus = [];
  private $default_value_for_post_id = null;
  protected $import_type;
  protected $import_obj;
  protected $uid = "";
  protected $twbb_created_with = "";
  protected $home_page_id = null;
  public $twbb_imported_attachments = "twbb_imported_attachments";
  public $twbb_imported_site_data = "twbb_imported_site_data";

  public function __construct($import_type, $import_obj = null){
    $this->import_type = $import_type;
    $this->import_obj = $import_obj;
    $this->imported_data['import_type'] = $this->import_type;

    if($this->import_type === "ai_recreate") {
      $this->twbb_created_with = 'twbb_recreated';
    } else if($this->import_type === "ai_regenerate") {
      $this->twbb_created_with = 'twbb_generated';
    } else {
      $this->twbb_created_with = 'twbb_imported';
    }


    if($this->import_obj) {
      $this->twbb_imported_attachments = $this->import_obj->option_name("twbb_imported_attachments");
      $this->twbb_imported_site_data = $this->import_obj->option_name("twbb_imported_site_data");
    }

  }


  public function import($args_form_dash){
    if($this->parse_export_json() === false) {
      return new \WP_Error('import_data_failed', 'Failed to parse json.');
    }

    if($this->import_attachment() === false) {
      return new \WP_Error('import_data_failed', 'Failed to import attachments.');
    }

    if($this->import_terms() === false) {
      return new \WP_Error('import_data_failed', 'Failed to import terms.');
    }

    $this->import_posts($args_form_dash);
    if($this->import_type === "ai_recreate") {
      $this->save_wp_option_imported_menus();
    }

    $this->import_options();

    if($this->import_type === "ai_recreate") {
      $this->change_menu_items($args_form_dash);
    }
    $this->save_imported_data();

    return true;
  }

  private function import_posts($args_form_dash){
    $nav_menu = null;
    $elementor_library = null;
    $page_file = null;
    foreach($this->files->post_types as $post_type => $pt_file) {
      if($post_type === "post" && $this->import_type === "ai_recreate") {
        $published_posts = wp_count_posts()->publish;
        if($published_posts > 2) {
          continue;
        }
      } else if($post_type === "nav_menu_item") {
        $nav_menu = $pt_file;
      } else if($post_type === "page") {
        $page_file = $pt_file;
      } else {
        $this->import_post_type_posts($post_type, $pt_file, $args_form_dash);
      }
    }

    if($page_file) {
      $this->import_post_type_posts("page", $page_file, $args_form_dash);
    }

    $this->default_value_for_post_id = "";

    if(isset($nav_menu)) {
      $this->import_nav_menu($nav_menu);
    }

    $this->import_templates();
    $this->import_elementor_kit($args_form_dash["theme_id"]);

    foreach($this->imported_data['posts'] as $post_id) {
      $post = get_post($post_id);
      $post->post_content = $this->replace_post_urls($post->post_content);
      $post->post_excerpt = $this->replace_post_urls($post->post_excerpt);
      wp_update_post($post);
      $elementor_data = get_post_meta($post_id, '_elementor_data', true);
      $elementor_data = $this->replace_post_ids_in_widgets($elementor_data, $post_id);
      $elementor_data = wp_slash($this->replace_post_urls($elementor_data));
      update_post_meta($post_id, '_elementor_data', $elementor_data);
    }
  }

  private function save_wp_option_imported_menus(){
    foreach($this->imported_nav_menus as $menu_id) {
      update_option('imported_nav_menu_' . $menu_id, wp_get_nav_menu_items($menu_id));
    }
  }

  /**
   * @param $post_type string
   * @param $pt_file string json file name
   * @return boolean
   * */
  private function import_post_type_posts($post_type, $pt_file, $args_form_dash){
    /*
     * post_exists() function is used and it isincluded only in wp-admin, so we are checking and including it when needed
     */
    if(!function_exists('post_exists')) {
      require_once(ABSPATH . 'wp-admin/includes/post.php');
    }
    $file_content = (string)file_get_contents($this->template_dir . $pt_file);

    if($post_type === "page") {
      $file_content = preg_replace_callback('/{10WEB_REPLACE_POST_ID_([0-9]+)}/', [$this, 'preg_replace_temp_callback'], $file_content);
    }

    $file_content = $this->replace_placeholders($file_content);
    $posts = json_decode($file_content);
    if($posts === null) {
      $this->add_error(new \WP_Error('failed_to_parse_json', 'Failed to parse ' . $pt_file . ' file.'));
      return false;
    }

    $set_home_page_as_parent = $post_type === "page" && !empty($args_form_dash["parent_home_page_for_pages"]);

    if($set_home_page_as_parent){
      $home_page_index = null;
      foreach($posts as $i=>$post) {
        if($post->post_id == $args_form_dash["parent_home_page_for_pages"]){
          $home_page_index = $i;
          break;
        }
      }

      if($home_page_index === null) {
        $set_home_page_as_parent = false;
      } else {
        $first = $posts[0];
        $posts[0] = $posts[$home_page_index];
        $posts[$home_page_index] = $first;
      }


    }
    $home_page_id = null;
    foreach($posts as $post) {

      $meta_input = [];
      foreach($post->metas as $meta_key => $meta_value) {
        // unserialize data to avoid double serialization by wordpress.
        // wp_slash in order to avoid the unslashing during the `add_post_meta`
        $meta_value = maybe_unserialize((is_array($meta_value) && isset($meta_value[0])) ? $meta_value[0] : $meta_value);

        if($post_type == "elementor_font" && $meta_key == "elementor_font_files") {
          $meta_value = json_decode(json_encode($meta_value), true);
          if(post_exists($post->post_title, '', '', $post_type, 'publish')) {
            return false;
          }
        } else {
          $meta_value = wp_slash($meta_value);
        }

        $meta_input[$meta_key] = $meta_value;
      }

      $categories = $tax_input = [];
      if($post_type !== "nav_menu_item") {

        $tax_input = !empty($post->terms) ? (array)$post->terms : [];

        /*
         * Hierarchical taxonomies must always pass IDs rather than names so that
         * children with the same names but different parents aren't confused.
         */
        $categories = [];

        if(!empty($tax_input['category'])) {
          foreach($tax_input['category'] as $slug) {
            $term = get_term_by('slug', $slug, 'category');
            $categories[] = $term->term_id;
          }
        }

        unset($tax_input['category']);

        if(!empty($tax_input['product_cat'])) {
          foreach($tax_input['product_cat'] as $index => $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            $tax_input['product_cat'][$index] = $term->term_id;
          }
        }
      }
      $post_title = $post->post_title;
      $post_status = 'publish';

      if($post_type === "page") {
        $post_status = $args_form_dash['post_status'];
        /*
       * check if new title is send change it.
       */
        if($args_form_dash['page_title'] !== FALSE && $this->twbb_created_with != 'twbb_imported') {
          $post_title = $args_form_dash['page_title'];
        }

        if($this->import_type === "ai_regenerate") {
          // twbb_ai_created is used also for recreation. The meta value is in template file
          $meta_input['twbb_ai_created'] = 1;
        }
      }

      /*
       * logic to not have dublicated slide titles
       */
      if($post_type == "elementor_library" && strpos(json_encode($post), 'twbb_slide') !== false) {
        $slide_id = post_exists($post->post_title);
        if(isset($num)) {
          $setted_num = $num;
        }
        if(0 != $slide_id) {
          do {
            if(!isset($setted_num)) {
              $num = substr($post_title, 5);
            } else {
              $num = $setted_num;
            }
            $num = intval($num) + 1;
            $slide_id = post_exists('Slide ' . $num);
            $post_title = get_the_title($slide_id);
          } while(0 != post_exists($post_title));
          $post_title = 'Slide ' . $num;
        }
      }

      $args = array(
        'post_title' => $post_title,
        'post_content' => $post->post_content,
        'post_status' => $post_status,
        'post_type' => $post_type,
        'ping_status' => $post_status,
        'post_excerpt' => $post->post_excerpt,
        'menu_order' => intval($post->menu_order),
        'meta_input' => $meta_input,
        'post_category' => $categories,
        'tax_input' => $tax_input,
      );

      if($set_home_page_as_parent){
        if($home_page_id){
          $args["post_parent"] = $home_page_id;
        }else if(isset($post->post_name)) {
          $args["post_name"] = $post->post_name;
        }
      }

      $post_id = wp_insert_post($args, true);

      if($set_home_page_as_parent && $home_page_id === null && $post->post_id == $args_form_dash["parent_home_page_for_pages"]){
        $home_page_id = $post_id;
      }

      if($post_type === "page") {
        update_option('twbb_last_imported_pageID', $post_id);
      }
      if($post_type == "elementor_library" && strpos(json_encode($post), 'twbb_slide') !== false) {
        wp_set_object_terms($post_id, 'twbb_slide', 'elementor_library_type');
      }
      update_post_meta($post_id, 'twbb_created_with', $this->twbb_created_with);

      if(is_wp_error($post_id)) {
        $this->add_error($post_id);
      } else {
        if($post_type == "elementor_font") {
          $term = get_term_by('slug', "custom", 'elementor_font_type');
          if($term === false) {
            $term_id = wp_insert_term('custom', "elementor_font_type");
          } else {
            $term_id = $term->term_id;
          }

          if($term_id && !is_wp_error($term_id)) {
            wp_set_post_terms($post_id, [$term_id], "elementor_font_type");
          }

        }

        $this->imported_data['posts'][] = $post_id;
        $this->replace_posts[$post->post_id] = $post_id;
        if(isset($this->replace_attachments[$post->thumbnail_id])) {
          set_post_thumbnail($post_id, $this->replace_attachments[$post->thumbnail_id]['current']['post_id']);
        }
      }
    }

    return true;
  }

  /**
   * Temporary to fix menu import before user log-in issue is fixed.
   *
   * @param $allcaps
   * @return void
   */
  public function grant_nav_menu_cap($allcaps){
    $taxonomy_obj = get_taxonomy('nav_menu');
    if($taxonomy_obj) {
      $allcaps[$taxonomy_obj->cap->assign_terms] = true;
    }

    return $allcaps;
  }

  /**
   * @param $pt_file string file name
   * @return boolean
   * */
  private function import_nav_menu($pt_file){
    $file_content = (string)file_get_contents($this->template_dir . $pt_file);
    $posts = json_decode($file_content);

    if($posts === null) {
      $this->add_error(new \WP_Error('failed_to_parse_json', 'Failed to parse ' . $pt_file . ' file.'));
      return false;
    }

    add_filter('user_has_cap', [$this, 'grant_nav_menu_cap'], 1);

    foreach($posts as $post) {

      $tax_input = !empty($post->terms->nav_menu) ? $post->terms->nav_menu : [];
      $tax_input = ['nav_menu' => $this->get_nav_menu_terms($tax_input)];

      $args = array(
        'post_title' => $post->post_title,
        'post_content' => $post->post_content,
        'post_status' => $post->post_status,
        'post_type' => 'nav_menu_item',
        'menu_order' => intval($post->menu_order),
        'ping_status' => $post->post_status,
        'post_excerpt' => $post->post_excerpt,
        'tax_input' => $tax_input,
      );

      $post_id = wp_insert_post($args, true);

      if(is_wp_error($post_id)) {
        $this->add_error($post_id);
      } else {
        $this->replace_posts[$post->post_id] = $post_id;
      }
    }
    remove_filter('user_has_cap', [$this, 'grant_nav_menu_cap']);

    foreach($posts as $post) {
      if(!empty($post->metas)) {
        $metas = [];
        if($post->metas->_menu_item_type[0] === "taxonomy") {
          $old_id = $post->metas->_menu_item_object_id[0];
          if(isset($this->replace_terms[$old_id])) {
            $new_id = $this->replace_terms[$old_id]['current']['term_id'];
          } else {
            $new_id = 0;
          }

          $metas['_menu_item_object_id'] = $new_id;

        } else if($post->metas->_menu_item_type[0] === "post_type" || $post->metas->_menu_item_type[0] === "custom") {

          $old_id = $post->metas->_menu_item_object_id[0];
          if(isset($this->replace_posts[$old_id])) {
            $new_id = $this->replace_posts[$old_id];
          } else {
            $new_id = 0;
          }

          $metas['_menu_item_object_id'] = $new_id;
        }

        $old_parent = $post->metas->_menu_item_menu_item_parent[0];
        if(isset($this->replace_posts[$old_parent])) {
          $new_parent = $this->replace_posts[$old_parent];
        } else {
          $new_parent = 0;
        }

        if($this->import_type === "ai_recreate") {

          /*
           * for replacing new created page in same place in menu
           */
          //TODO find better solution merge two foreach
          $tax_input = !empty($post->terms->nav_menu) ? $post->terms->nav_menu : [];
          $tax_input = ['nav_menu' => $this->get_nav_menu_terms($tax_input)];
          $nav_menu_term_id = $tax_input['nav_menu'][0];
          if(array_search($nav_menu_term_id, get_option('menus_to_be_changed')) !== false) {
            $menu_order = get_post($new_id)->menu_order;
            update_option('twbb_menu_order_' . $menu_order, $nav_menu_term_id);
            $metas['_menu_item_classes'] = 'ai-recreated-menu-item twbb_menu_order_' . $menu_order;
          }
        }

        $metas['_menu_item_menu_item_parent'] = $new_parent;


        foreach($post->metas as $meta_key => $meta_value) {

          if(isset($metas[$meta_key])) {
            $meta_value = $metas[$meta_key];
          } else {
            $meta_value = (isset($meta_value[0])) ? $meta_value[0] : $meta_value;
          }

          update_post_meta($this->replace_posts[$post->post_id], $meta_key, $this->replace_post_urls($meta_value));
        }

      }
    }
    return true;
  }

  /**
   * @return boolean
   * */
  private function import_terms(){
    $terms = json_decode(file_get_contents($this->template_dir . $this->files->terms));

    if($terms === null) {
      $this->add_error(new \WP_Error('failed_to_parse_json', 'Failed to parse ' . $this->files->terms . ' file.'));
      return false;
    }
    $menus_to_be_changed = [];
    foreach($terms as $tax => $tax_term) {
      foreach($tax_term as $term) {
        $new_term = get_term_by('slug', $term->slug, $term->taxonomy);
        if($new_term !== false && $tax !== "nav_menu") {
          $this->replace_terms[$term->term_id] = [
            'current' => ['term_id' => $new_term->term_id, 'slug' => $new_term->slug],
            'template' => ['term_id' => $term->term_id, 'slug' => $term->slug]
          ];
        } else {
          if($new_term !== false && $tax === "nav_menu") {
            // Backup old menus or delete them.
            $menu_increment = 0;
            do {
              $menu_item = wp_get_nav_menu_object($term->name);
              if(false != $menu_item) {
                $menu_item = wp_get_nav_menu_object($menu_item->name . ' ' . ++$menu_increment);
              }
            } while(false != $menu_item);
          }
          $name = $term->name . ' ' . $menu_increment;
          $slug = $term->slug;

          $result = wp_insert_term($name, $term->taxonomy, array(
            'description' => $term->description,
            'slug' => $slug,
          ));

          if($tax === "nav_menu") {
            $this->imported_data['menus'][] = $result['term_id'];
            $this->imported_nav_menus[] = $result['term_id'];
          } else {
            $this->imported_data['terms'][] = [
              'term_id' => $result['term_id'],
              'taxonomy' => $tax
            ];
          }

          $this->replace_terms[$term->term_id] = [
            'current' => ['term_id' => $result['term_id'], 'slug' => $slug],
            'template' => ['term_id' => $term->term_id, 'slug' => $term->slug]
          ];

          /* check which menus should be changed to '+Add page' */
          if($term->change_menu_items == 1) {
            $menus_to_be_changed[] = $result['term_id'];
          }
        }
      }
    }
    update_option('menus_to_be_changed', $menus_to_be_changed);
    return true;
  }

  /**
   * @return boolean|array
   * */
  public function import_attachment($generate_metadata_mode = 'bulk'){
    $this->image_sizes = get_intermediate_image_sizes();
    $this->image_sizes[] = 'full';

    if($this->parse_export_json() === false) {
      $this->add_error(new \WP_Error('import_data_failed', 'Failed to parse json.'));
      return false;
    }

    $attachments = json_decode(file_get_contents($this->template_dir . $this->files->attachments));

    if($attachments === null) {
      $this->add_error(new \WP_Error('failed_to_parse_json', 'Failed to parse ' . $this->files->attachments . ' file.'));
      return false;
    }

    /*
     * Disable image resize for gif files as Wordpress does not know how to safely handle them.
     * */
    add_filter('big_image_size_threshold', function($threshold, $imagesize, $file, $attachment_id){
      if('image/gif' == $imagesize['mime']) {
        return false;
      }
      return $threshold;
    }, 10, 4);

    $attachment_paths = array();
    foreach($attachments as $attachment) {
      $insert_result = $this->insert_attachment($attachment, $generate_metadata_mode);
      $attachment_paths[$insert_result['post_id']] = $insert_result['path'];

      $this->replace_attachments[$attachment->post_id] = [
        'current' => $insert_result,
        'template' => $attachment
      ];
    }

    update_option($this->twbb_imported_attachments, $attachment_paths);
    return $this->imported_data['attachments'];
  }

  /**
   * Generate attachments metadata async to speed up import process.
   */
  public function generate_attachment_meta(){
    $attachment_paths = get_option($this->twbb_imported_attachments, []);
    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    foreach($attachment_paths as $id => $path) {
      // Generate the metadata for the attachment, and update the database record.
      $attachment = get_post($id);
      wp_maybe_generate_attachment_metadata($attachment);
    }
    delete_option($this->twbb_imported_attachments);
  }

  /**
   * @param $attachment_obj Object
   * @return array ['post_id'=>'', 'url'=>'']
   * */
  private function insert_attachment($attachment_obj, $generate_metadata_mode){
    if($attachment_obj->file_id) {
      $saved_image = $this->get_image_by_hash($attachment_obj->file_id);
    } else {
      $saved_image = null;
    }

    if($saved_image !== null) {
      $this->imported_data['attachments'][] = $saved_image['post_id'];
      update_post_meta($saved_image['post_id'], '_elementor_source_image_hash', sha1($saved_image['urls']['default']));
      return $saved_image;
    }

    // $filename should be the path to a file in the upload directory.
    copy($this->template_dir . "attachments/". $attachment_obj->file_name, $this->attachments_folder . $attachment_obj->file_name);
    $filename = $this->attachments_folder . $attachment_obj->file_name;

    // Check the type of file. We'll use this as the 'post_mime_type'.
    $filetype = wp_check_filetype(basename($filename), null);

    // Get the path to the upload directory.
    $wp_upload_dir = wp_upload_dir();

    // Prepare an array of post data for the attachment.
    $attachment = array(
      'guid' => $wp_upload_dir['url'] . '/' . basename($filename),
      'post_mime_type' => $filetype['type'],
      'post_title' => $attachment_obj->post_title,
      'post_content' => $attachment_obj->post_content,
      'post_status' => $attachment_obj->post_status
    );

    if(!empty($attachment_obj->metas)) {
      $attachment["meta_input"] = (array)$attachment_obj->metas;
    }

    // Insert the attachment.
    $attach_id = wp_insert_attachment($attachment, $filename);
    if('bulk' != $generate_metadata_mode) {
      // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      // Generate the metadata for the attachment, and update the database record.
      $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
      wp_update_attachment_metadata($attach_id, $attach_data);
    }

    $url = $this->attachments_folder_url . $attachment_obj->file_name;

    update_post_meta($attach_id, '_twbb_image_hash', $attachment_obj->file_id);
    update_post_meta($attach_id, '_elementor_source_image_hash', sha1($url));

    $this->imported_data['attachments'][] = $attach_id;
    $urls = [];
    $urls['default'] = wp_get_attachment_url($attach_id);
    foreach($this->image_sizes as $image_size) {
      $get_image_src = wp_get_attachment_image_src($attach_id, $image_size);
      $urls[$image_size] = $get_image_src ? $get_image_src[0] : false;
    }
    return ['post_id' => $attach_id, 'urls' => $urls, 'path' => $filename];
  }

  public function import_templates(){
    include_once 'import-template.php';

    $templates_dir = $this->template_dir . 'templates/';
    if(isset($this->files->templates)) {
      $templates_files = $this->files->templates;
    } else {
      $templates_files = array();
    }

    foreach($templates_files as $old_id => $template_file) {

      if($this->replace_template_placeholders($templates_dir . $template_file) === false) {
        return false;
      }

      $import_template = new ImportTemplate();
      $result = $import_template->import_single_template($templates_dir . $template_file);

      if(is_wp_error($result)) {
        $this->add_error($result);
      } else {
        $this->imported_data['posts'][] = $result;
        $this->replace_posts[$old_id] = $result;
      }
    }

    return true;
  }


  public function import_elementor_kit($theme_id){
    if(!file_exists($this->template_dir . 'elementor_kit.json')) {
      return;
    }

    $kit_data = json_decode(file_get_contents($this->template_dir . 'elementor_kit.json'), true);

    $all_kits_path = $this->template_dir . 'all_kits.json';
    if($theme_id && file_exists($all_kits_path)){
      $kit_data = $this->merge_theme_and_kit($theme_id, $all_kits_path,  $kit_data);
    }

    add_filter("user_has_cap", [$this, "add_caps_to_allow_adding_new_kit"], 10, 4);
    $kit_params = ['system_colors', 'system_typography','custom_colors','custom_typography'];
    $kit_data_to_import = [];
    foreach($kit_params as $param) {
      $kit_data_to_import[$param] = $kit_data[$param];
    }
    $kit_id = \Elementor\Plugin::instance()->kits_manager->create_new_kit("10Web AI kit", $kit_data_to_import);
    remove_filter('user_has_cap', [$this, 'add_caps_to_allow_adding_new_kit']);

    $this->imported_data['kit_id'] = $kit_id;

    return $kit_id;
  }

  /**
   * @return boolean
   * */
  private function parse_export_json(){
    if(!file_exists($this->template_dir . 'export.json')) {
      $this->add_error(new \WP_Error('file_not_exists', 'export json file not exists.'));
      return false;
    }

    $export_json = json_decode(file_get_contents($this->template_dir . 'export.json'));

    if($export_json === null) {
      $this->add_error(new \WP_Error('failed_to_parse_json', 'Failed to parse export json.'));
      return false;
    }

    $this->version = $export_json->version;
    if(isset($export_json->options)) {
      $this->options = $export_json->options;
    } else {
      $this->options = array();
    }

    foreach($export_json->files as $key => $file) {

      if($key === 'post_types') {

        foreach($file as $f) {
          if(!file_exists($this->template_dir . $f)) {
            $this->add_error(new \WP_Error('file_not_exists', $f . ' file not exists.'));
            return false;
          }
        }

      } else if($key === 'templates') {

        foreach($file as $f) {
          if(!file_exists($this->template_dir . 'templates/' . $f)) {
            $this->add_error(new \WP_Error('file_not_exists', $f . ' file not exists.'));
            return false;
          }
        }


      } else {
        if(!file_exists($this->template_dir . $file)) {
          $this->add_error(new \WP_Error('file_not_exists', $file . ' file not exists.'));
          return false;
        }
      }

    }

    $this->files = $export_json->files;

    $wp_upload_dir = wp_upload_dir();

    $this->attachments_folder = $wp_upload_dir['path'] . '/';
    $this->attachments_folder_url = $wp_upload_dir['url'] . '/';

    if(!is_dir($this->attachments_folder)) {
      $this->add_error(new \WP_Error('dir_not_exists', 'Attachments dir not exists.'));
      return false;
    }

    return true;
  }

  private function import_options(){
    foreach($this->options as $opt_name => $opt_val) {
      $value = null;
      switch($opt_name) {
        case 'twbb_archive_conditions':
        case 'twbb_singular_conditions':
        case 'twbb_general_conditions':

          $value = get_option($opt_name, []);
          foreach($opt_val as $template_id => $conditions) {
            if(!isset($this->replace_posts[$template_id]) || empty($conditions)) {
              continue;
            }
            $value[$this->replace_posts[$template_id]] = $this->replace_conditions($conditions);
          }

          break;

        case 'page_on_front':
        case 'page_for_posts':
        case 'woocommerce_cart_page_id':
        case 'woocommerce_checkout_page_id':
        case 'woocommerce_myaccount_page_id':
        case 'woocommerce_terms_page_id':
        case 'woocommerce_shop_page_id':

          if(isset($this->replace_posts[$opt_val])) {
            $value = $this->replace_posts[$opt_val];
          } else {
            $value = 0;
          }

          break;
        case 'nav_menu_options':

          $option = get_option('nav_menu_options', []);

          if(!empty($opt_val->auto_add) && is_array($option)) {

            if(!isset($option['auto_add'])) {
              $option['auto_add'] = [];
            }

            foreach($opt_val->auto_add as $term_id) {
              if(isset($this->replace_terms[$term_id])) {
                $option['auto_add'][] = $this->replace_terms[$term_id]['current']['term_id'];
              }
            }
          }

          update_option('nav_menu_options', $option);
          break;
        case 'nav_menu_locations':

          if(!empty($opt_val)) {
            $locations = [];
            foreach($opt_val as $loc => $term_id) {
              if(isset($this->replace_terms[$term_id])) {
                $locations[$loc] = $this->replace_terms[$term_id]['current']['term_id'];
              }
            }

            if(!empty($locations)) {
              $this->imported_data['options']['nav_menu_locations'] = get_theme_mod('nav_menu_locations');
              set_theme_mod('nav_menu_locations', $locations);
            }

          }

          break;
        case 'custom_logo':

          if(!empty($opt_val)) {
            $attachment_id = $this->replace_attachments[$opt_val]['current']['post_id'];

            if(!empty($attachment_id)) {
              $this->imported_data['options']['custom_logo'] = get_theme_mod('custom_logo');
              set_theme_mod('custom_logo', $attachment_id);
              update_option('custom_logo', $attachment_id);
            }
          }

          break;
        case 'site_icon':

          if(!empty($opt_val)) {
            $attachment_id = $this->replace_attachments[$opt_val]['current']['post_id'];

            if(!empty($attachment_id)) {
              $this->imported_data['options']['site_icon'] = $attachment_id;
              set_theme_mod('site_icon', $attachment_id);
              update_option('site_icon', $attachment_id);
            }
          }

          break;
        case '_elementor_page_settings':
          if(!empty($opt_val)) {
            add_filter("user_has_cap", [$this, "add_caps_to_allow_adding_new_kit"], 10, 4);
            $settings = (is_object($opt_val)) ? json_decode(json_encode($opt_val), true) : stripslashes($opt_val);
            \Elementor\Plugin::instance()->kits_manager->create_new_kit('AI recreated Kit', $settings, true);
            remove_filter('user_has_cap', [$this, 'add_caps_to_allow_adding_new_kit']);
          }
          break;
        default:
          // (array) conversion is done by json_decode(json_encode($opt_val), true) to get proper conversion for multilevel arrays.
          $value = (is_object($opt_val)) ? json_decode(json_encode($opt_val), true) : stripslashes($opt_val);
      }

      if(isset($value)) {

        // Do not import blogname and blogdescription
        if('blogname' == $opt_name || 'blogdescription' == $opt_name) {
          continue;
        }

        if($opt_name === 'page_on_front'){
          $this->home_page_id = $value;
          $this->imported_data["home_page_id"] = $this->home_page_id;
        }

        $opt = get_option($opt_name);
        if($opt !== false) {
          $this->imported_data['options'][$opt_name] = $opt;
        }

        update_option($opt_name, $value);
      }
    }
  }

  private function replace_conditions($conditions){
    $new_conditions = [];
    foreach($conditions as $key => $condition) {
      $condition = (array)$condition;
      if(empty($condition['specific_pages'])) {
        $new_conditions[$key] = $condition;
        continue;
      }

      $specific_pages = [];
      foreach($condition['specific_pages'] as $item_id) {

        if($condition['filter_type'] === 'specific_posts') {

          if(isset($this->replace_posts[$item_id])) {
            $specific_pages[] = $this->replace_posts[$item_id];
          }

        } else {

          if(isset($this->replace_terms[$item_id])) {
            $specific_pages[] = $this->replace_terms[$item_id]['current']['term_id'];
          }
        }
      }

      $condition['specific_pages'] = $specific_pages;
      $new_conditions[$key] = $condition;
    }

    return $new_conditions;
  }

  private function get_nav_menu_terms($terms){
    $new_terms = [];
    foreach($terms as $term) {
      foreach($this->replace_terms as $replace_term) {
        if($replace_term['template']['slug'] === $term) {
          $new_terms[] = $replace_term['current']['slug'];
        } else if($replace_term['template']['term_id'] === $term) {
          $new_terms[] = $replace_term['current']['term_id'];
        }
      }
    }

    return $new_terms;
  }

  private function save_imported_data(){
    update_option($this->twbb_imported_site_data, $this->imported_data, false);
    if($this->import_type === "ai_regenerate"){
      update_option('twbb_imported_site_data_generated', $this->imported_data, false);
    }
  }

  /*
   *
   */
  private function change_menu_items($args_form_dash){
    $page_status = $args_form_dash['post_status'];
    $page_title = $args_form_dash['page_title'];
    $menu_term_id = $args_form_dash['menu_term_id'];
    $menu_item_id = $args_form_dash['menu_item_id'];
    $menu_item_position = $args_form_dash['menu_item_position'];
    $page_id = get_option('twbb_last_imported_pageID');
    $change_in_menus = [];

    if($menu_term_id != FALSE && $menu_item_id != FALSE && $menu_item_position != FALSE) {
      $change_in_menus[] = array(
        'menu_term_id' => $menu_term_id,
        'menu_item_id' => $menu_item_id,
        'menu_item_position' => $menu_item_position,
      );
    } else if($page_status == 'publish') {
      /* check if there is header and footer menus(setted from theme) and add page in next available position */
      $menu_ids = [];
      if(isset(get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'])) {
        $header_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['header_menu'];
        $menu_ids['header_menu_id'] = $header_menu_id;
      }
      if(isset(get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'])) {
        $footer_menu_id = get_option('theme_mods_tenweb-website-builder-theme')['nav_menu_locations']['footer_menu'];
        $menu_ids['footer_menu_id'] = $footer_menu_id;
      }
      foreach($menu_ids as $menu_id) {
        if($menu_id != 0) {
          $menu = wp_get_nav_menu_items($menu_id);
          $menu_item_id = 0;
          $menu_item_position = 0;
          foreach($menu as $menu_item) {
            foreach($menu_item->classes as $menu_classes) {
              if(str_contains($menu_classes, 'ai-recreated-menu-item')) {
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
    foreach($change_in_menus as $menu) {
      wp_update_nav_menu_item($menu['menu_term_id'], $menu['menu_item_id'], array(
        'menu-item-title' => $page_title,
        'menu-item-object-id' => $page_id,
        'menu-item-object' => 'page',
        'menu-item-status' => 'publish',
        'menu-item-type' => 'post_type',
        'menu-item-classes' => '',
        'menu-item-position' => $menu['menu_item_position']
      ));
    }
  }

  /**
   * @param $hash string
   * @return null|array
   * */
  private function get_image_by_hash($hash){
    $args = array(
      'post_type' => 'attachment',
      'meta_query' => array(
        array(
          'key' => '_twbb_image_hash',
          'value' => $hash,
          'compare' => '=',
        )
      )
    );

    $post = get_posts($args);
    if(empty($post)) {
      return null;
    }

    $urls = [];
    $urls['default'] = wp_get_attachment_url($post[0]->ID);
    foreach($this->image_sizes as $image_size) {
      $get_image_post_src = wp_get_attachment_image_src($post[0]->ID, $image_size);
      $urls[$image_size] = $get_image_post_src ? $get_image_post_src[0] : false;
    }

    $path = get_attached_file($post[0]->ID);

    $attachment_data = [
      'post_id' => $post[0]->ID,
      'urls' => $urls,
      'path' => $path
    ];

    return $attachment_data;
  }

  public function replace_shortcode_ids($repl_arr, $content, $reg, $return_name, $addslashes){
    $content = preg_replace_callback($reg,
      function($matches) use ($repl_arr, $return_name, $addslashes, $reg){
        return ($addslashes ? ('\"' . $return_name . '\":\"' . $repl_arr[$matches[1]] . '\"') : ('"' . $return_name . '":"' . $repl_arr[$matches[1]] . '"'));
      }, $content);

    return $content;
  }

  private $current_size;
  private $addslashes;

  private function replace_placeholders($content, $addslashes = true){
    $content = preg_replace_callback('/{10WEB_REPLACE_MENU_SLUG_([0-9]+)}/', [$this, 'preg_replace_menu_slug_callback'], $content);
    $this->addslashes = $addslashes;
    foreach($this->image_sizes as $image_size) {
      $this->current_size = $image_size;
      $placeholder = '/{10WEB_REPLACE_ATTACH_' . strtoupper($image_size) . '_URL_([0-9]+)}/';
      $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback'], $content);
      $placeholder = '/{10WEB_REPLACE_ATTACH_' . strtoupper($image_size) . '_URL_SLASHED_([0-9]+)}/';
      $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_slashed'], $content);
      $placeholder = '/{10WEB_REPLACE_ATTACH_' . strtoupper($image_size) . '_ID_([0-9]+)}/';
      $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_id'], $content);
      $placeholder = '{10WEB_REPLACE_ATTACH_' . strtoupper($image_size) . '_ID_URL_([0-9]+)}';
      $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_id_url'], $content);
      $placeholder = '{10WEB_REPLACE_ATTACH_' . strtoupper($image_size) . '_URL_ID_([0-9]+)}';
      $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_url_id'], $content);
    }
    // for video attachments
    $this->current_size = 'default';
    $placeholder = '/{10WEB_REPLACE_ATTACH_URL_([0-9]+)}/';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback'], $content);
    $placeholder = '/{10WEB_REPLACE_ATTACH_ID_([0-9]+)}/';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_id'], $content);
    $placeholder = '/{10WEB_REPLACE_ATTACH_URL_SLASHED_([0-9]+)}/';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_slashed'], $content);
    $placeholder = '{10WEB_REPLACE_ATTACH_ID_URL_([0-9]+)}';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_id_url'], $content);
    $placeholder = '{10WEB_REPLACE_ATTACH_URL_ID_([0-9]+)}';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_url_id'], $content);
    return $content;
  }

  public function preg_replace_pt_callback($matches){
    if(isset($this->replace_attachments[$matches[1]])) {
      return $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size];
    } else {
      return "";
    }
  }

  public function preg_replace_pt_callback_id($matches){
    if(isset($this->replace_attachments[$matches[1]])) {
      return $this->replace_attachments[$matches[1]]['current']['post_id'];
    } else {
      return "";
    }
  }

  public function preg_replace_pt_callback_slashed($matches){
    if(isset($this->replace_attachments[$matches[1]])) {
      $permalink = $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size];
      $url_slashed = str_replace('/', '\\/', $permalink);
      return $this->addslashes ? addslashes($url_slashed) : $url_slashed;
    } else {
      return "";
    }
  }

  public function preg_replace_pt_callback_id_url($matches){
    if(isset($this->replace_attachments[$matches[1]])) {
      $url_slashed = $this->addslashes ? str_replace('/', '\\/', $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size]) : $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size];
      $str = '"id":' . $this->replace_attachments[$matches[1]]['current']['post_id'] . ',"url":"' . $url_slashed . '"';
      return $this->addslashes ? addslashes($str) : $str;
    } else {
      return "";
    }
  }

  public function preg_replace_pt_callback_url_id($matches){
    if(isset($this->replace_attachments[$matches[1]])) {
      $url_slashed = $this->addslashes ? str_replace('/', '\\/', $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size]) : $this->replace_attachments[$matches[1]]['current']['urls'][$this->current_size];
      $str = '"url":"' . $url_slashed . '","id":' . $this->replace_attachments[$matches[1]]['current']['post_id'] . '';
      return $this->addslashes ? addslashes($str) : $str;
    } else {
      return "";
    }
  }


  private function replace_template_placeholders($file){
    $content = file_get_contents($file);
    if($content === false) {
      $this->add_error(new \WP_Error("failed_to_open_file", "Failed to open " . $file . " file."));
      return false;
    }
    $content = $this->replace_placeholders($content, false);
    $content = preg_replace_callback('/{10WEB_REPLACE_POST_ID_([0-9]+)}/', [$this, 'preg_replace_temp_callback'], $content);
    $content = preg_replace_callback('/{10WEB_REPLACE_TERM_ID_([0-9]+)}/', [$this, 'preg_replace_temp_term_callback'], $content);
    $content = ImportTemplate::replace_attachment_placeholder($content, $this->template_url);
    $content = $this->replace_post_urls($content);

    if(file_put_contents($file, $content) === false) {
      $this->add_error(new \WP_Error(
        "failed_to_write_in_file",
        "Failed to write in " . $file . " file."));
      return false;
    }

    return true;
  }

  public function preg_replace_temp_callback($matches){
    if(isset($this->replace_posts[$matches[1]])) {
      return $this->replace_posts[$matches[1]];
    } else {

      if(is_null($this->default_value_for_post_id)) {
        return $matches[0];
      } else {
        return $this->default_value_for_post_id;
      }

    }
  }

  public function preg_replace_menu_slug_callback($matches){
    if(isset($this->replace_terms[$matches[1]])) {
      $term_data = $this->replace_terms[$matches[1]];
      $term = get_term($term_data['current']['term_id'], "nav_menu");
      if($term) {
        return $term->slug;
      }
    }

    return "";
  }

  public function preg_replace_temp_term_callback($matches){
    if(isset($this->replace_terms[$matches[1]])) {
      $term_data = $this->replace_terms[$matches[1]];
      return $term_data['current']['term_id'];
    } else {
      return "";
    }
  }

  private function replace_post_urls($content){
    $placeholder = '/{10WEB_REPLACE_POST_URL_([0-9]+)}/';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_post_url'], $content);
    $this->addslashes = true;
    $placeholder = '/{10WEB_REPLACE_POST_URL_SLASHED_([0-9]+)}/';
    $content = preg_replace_callback($placeholder, [$this, 'preg_replace_pt_callback_post_url'], $content);

    $site_url = site_url();
    $placeholder = '{10WEB_REPLACE_SITE_URL}';
    $content = str_replace($placeholder, $site_url, $content);
    $placeholder = '{10WEB_REPLACE_SITE_URL_SLASHED}';
    $content = str_replace($placeholder, str_replace('/', '\\/', $site_url), $content);

    return $content;
  }

  private function replace_post_ids_in_widgets($elementor_content, $post_id){
    $elementor_content = preg_replace_callback('/{10WEB_REPLACE_POST_ID_([0-9]+)}/', [$this, 'preg_replace_temp_callback'], $elementor_content);

    return $elementor_content;
  }

  public function preg_replace_pt_callback_post_url($matches){
    if(isset($this->replace_posts[$matches[1]])) {
      $permalink = get_permalink($this->replace_posts[$matches[1]]);
      $url_slashed = str_replace('/', '\\/', $permalink);
      return $this->addslashes ? $url_slashed : $permalink;
    } else {
      return "";
    }
  }


  /**
   *  Change products names which has default name from AI
   */
  public function change_products_default_names(){
    $all_posts = get_posts([
      'post_type' => 'product',
      'posts_per_page' => -1,
    ]);
    if(!empty($all_posts)) {
      foreach($all_posts as $post) {
        if($post->post_title == 'recreated_product') {
          $new_title = 'product_no' . $post->ID;
          $this->_change_product_name($post->ID, $new_title);
        }
      }
    }
  }

  /**
   *
   * @param int $product_id THis is product id of the product.
   * @param string $new_titile New title of the product.
   */
  private function _change_product_name($product_id, $new_title){
    $post = get_post($product_id);
    if(empty ($new_title)) {
      return;
    }
    $new_title = mb_convert_case($new_title, MB_CASE_TITLE, "UTF-8");

    if($post->post_title === $new_title) {
      return;
    }

    $post_update = array(
      'ID' => $post->ID,
      'post_title' => $new_title,
      'post_name' => $new_title,
    );

    wp_update_post($post_update);
  }

  private function merge_theme_and_kit($theme_id, $all_kits_path, $kit_data){
    $all_kits_data = json_decode(file_get_contents($all_kits_path), true);

    $kit_data_to_merge = null;
    foreach($all_kits_data as $kit_id => $tmp_kit_data) {

      if(str_starts_with($kit_id, $theme_id)) {
        $kit_data_to_merge = $tmp_kit_data;
        break;
      }
    }

    if($kit_data_to_merge === null) {
      return $kit_data;
    }

    $items_to_merge = ['system_colors', 'system_typography', 'custom_colors', 'custom_typography'];

    foreach($items_to_merge as $item) {
      if(!isset($kit_data_to_merge[$item]) || !isset($kit_data[$item])) {
        continue;
      }

      foreach($kit_data[$item] as $i => $setting) {

        foreach($kit_data_to_merge[$item] as $tmp_setting) {
          if($tmp_setting['title'] == $setting['title']) {

            if(in_array($item, ["system_colors", "custom_colors"])) {
              $tmp_setting["_id"] = $setting["_id"];
              $kit_data[$item][$i] = $tmp_setting;
            } else {
              $kit_data[$item][$i]["typography_font_family"] = $tmp_setting["typography_font_family"];
            }

          }
        }
      }
    }


    return $kit_data;
  }

  public function add_caps_to_allow_adding_new_kit($allcaps, $caps, $args, $this_){
    $allcaps["edit_posts"] = true;
    $allcaps["edit_post"] = true;
    $allcaps["edit_published_posts"] = true;
    $allcaps["edit_others_posts"] = true;
    return $allcaps;
  }

  public function set_template_dir($dir, $url){
    $this->template_dir = $dir;
    $this->template_url = $url;
  }

  private function add_error($error){
    $this->errors[] = $error;
  }

  public function get_errors(){
    return $this->errors;
  }

  public function get_replace_posts(){
    return $this->replace_posts;
  }

  public function get_imported_data(){
    return $this->imported_data;
  }
}
