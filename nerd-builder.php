<?php
/**
 * Plugin Name: Nerd Builder
 * Description: Nerd Builder is an ultimate premium tool, based on Elementor,  to create websites with stunning design.
 * Plugin URI:  https://Nerd.io/wordpress-website-builder/
 * Author: Nerd
 * Version: 1.3.319
 * Author: Nerd
 * Author URI: https://Nerd.io/plugins/
 * Text Domain: nerd-builder
 * License: GNU /GPLv3.0 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!defined('ABSPATH')) {
    exit;
}
include_once 'config.php';
require_once 'vendor/autoload.php';

include_once 'widgets-list.php';
include_once 'includes/helper.php';
if (twbb_check_plugin_requirements()) {
    add_action('plugins_loaded', 'twbb_plugins_loaded', 1);
    function twbb_plugins_loaded()
    {
        include_once 'builder.php';
        \Tenweb_Builder\Builder::get_instance();
    }
}
register_activation_hook(__FILE__, 'twbb_activate');
function twbb_activate()
{

    if (!twbb_check_plugin_requirements()) {
        die("PHP or Wordpress version is not compatible with plugin.");
    }
    include_once 'builder.php';
    Tenweb_Builder\Builder::install();
}

function twbb_check_plugin_requirements()
{
    global $wp_version;
    $php_version = explode("-", PHP_VERSION);
    $php_version = $php_version[0];
    $result = (
        version_compare($wp_version, '4.7', ">=") &&
        version_compare($php_version, '5.4', ">=")
    );

    return $result;
}

/**
 * Adding submenu Tenweb Templates to elementor menu.
 */
function admin_elementor_submenu()
{
    add_submenu_page('edit.php?post_type=elementor_library', '', __('Nerd Templates', 'nerd-builder'), 'edit_posts', 'edit.php?post_type=elementor_library&tabs_group=twbb_templates&elementor_library_type=twbb_header');
}

if (!TENWEB_WHITE_LABEL) {
//    add_action("admin_menu", 'admin_elementor_submenu');
}

if (isset($_GET["tabs_group"]) && $_GET["tabs_group"] == "twbb_templates") {
    add_action("admin_menu", "admin_menu_reorder", 900);
}
add_action("views_edit-elementor_library", "admin_print_tabs", 25);
/**
 * Remove Add New item from admin menu.
 * Fired by `admin_menu` action.
 *
 * @since  2.4.0
 * @access public
 */
function admin_menu_reorder()
{
    global $submenu;
    $library_submenu = &$submenu['edit.php?post_type=elementor_library'];
    // Remove 'All Templates' menu.
    unset($library_submenu[5]);
    // If current use can 'Add New' - move the menu to end, and add the '#add_new' anchor.
    if (isset($library_submenu[10][2])) {
        $library_submenu[700] = $library_submenu[10];
        unset($library_submenu[10]);
        $library_submenu[700][2] = admin_url('edit.php?post_type=elementor_library' . '#add_new');
    }
    // Move the 'Categories' menu to end.
    if (isset($library_submenu[15])) {
        $library_submenu[800] = $library_submenu[15];
        unset($library_submenu[15]);
    }
    if (is_current_screen()) {
        $library_title = get_library_title();
        foreach ($library_submenu as &$item) {
            if ($library_title === $item[0]) {
                if (!isset($item[4])) {
                    $item[4] = '';
                }
                $item[4] .= ' current';
            } else {
                if (isset($item[4])) {
                    $item[4] = '';
                }
            }
        }
    }
}

function admin_print_tabs($views)
{
    $current_type = '';
    $active_class = ' nav-tab-active';
    $current_tabs_group = get_current_tab_group();
    if (!empty($_REQUEST['elementor_library_type'])) {
        $current_type = $_REQUEST['elementor_library_type'];
        $active_class = '';
    }
    $url_args = [
        'post_type' => 'elementor_library',
        'tabs_group' => (isset($_GET['tabs_group']) && $_GET['tabs_group'] == 'twbb_templates') ? 'twbb_templates' : $current_tabs_group,
    ];
    $baseurl = add_query_arg($url_args, admin_url('edit.php'));
    $filter = [
        'admin_tab_group' => $current_tabs_group,
    ];
    $operator = 'and';
    if (empty($current_tabs_group)) {
        // Don't include 'not-supported' or other templates that don't set their `admin_tab_group`.
        $operator = 'NOT';
    }
    /* hide elementor tabs */
    if (isset($_GET["tabs_group"]) && $_GET['tabs_group'] == 'twbb_templates') {
        ?>
        <style>
            #elementor-template-library-tabs-wrapper:not(.twbb-builder), .subsubsub, .search-box, .alignleft.actions:not(.bulkactions) {
                display: none;
            }
        </style>
    <?php } elseif (isset($_GET["post_type"]) && $_GET["post_type"] == 'elementor_library') { ?>
        <style>
            #elementor-template-library-tabs-wrapper {
                display: none;
            }
        </style>

        <script>
            jQuery(document).ready(function () {
                jQuery('#elementor-template-library-tabs-wrapper .nav-tab').each(function () {
                    var href = jQuery(this).attr('href')
                    <?php if ( !TENWEB_WHITE_LABEL ) { ?>
                    var twbb = href.search('twbb_')
                    if (twbb != -1) {
                        jQuery(this).css('display', 'none')
                    }
                    <?php } ?>
                })
                jQuery('#elementor-template-library-tabs-wrapper').not('.twbb-builder').show()
            })
        </script>
        <?php
    }
    $doc_types = \Elementor\Plugin::instance()->documents->get_document_types($filter, $operator);
    if (1 >= count($doc_types)) {
        return '';
    }
    ?>
    <div id="elementor-template-library-tabs-wrapper" class="nav-tab-wrapper twbb-builder">
        <?php
        foreach ($doc_types as $type => $class_name) :
            $active_class = '';
            if ($current_type === $type) {
                $active_class = ' nav-tab-active';
            }
            $type_url = add_query_arg('elementor_library_type', $type, $baseurl);
            $type_label = get_template_label_by_type($type);
            if (isset($_GET["tabs_group"]) && $_GET["tabs_group"] == "twbb_templates" && $type != "twbb_header" && $type != "twbb_single" && $type != "twbb_archive" && $type != "twbb_footer" && $type != "twbb_slide") {
                continue;
            }
            echo "<a class='nav-tab{$active_class}' href='{$type_url}'>{$type_label}</a>";
        endforeach;
        ?>
    </div>
    <?php
    return $views;
}

add_filter('post_row_actions', 'template_list_row_actions', 10, 2);
/* Change edit links */
function template_list_row_actions($actions, $post)
{
    // Check for your post type.
    if ($post->post_type == "elementor_library") {
        unset($actions['view']);
    }

    return $actions;
}

function get_template_label_by_type($template_type)
{
    $document_types = \Elementor\Plugin::instance()->documents->get_document_types();
    if (isset($document_types[$template_type])) {
        $template_label = call_user_func([$document_types[$template_type], 'get_title']);
    } else {
        $template_label = ucwords(str_replace(['_', '-'], ' ', $template_type));
    }
    /**
     * Template label by template type.
     * Filters the template label by template type in the template library .
     *
     * @param string $template_label Template label.
     * @param string $template_type Template type.
     *
     * @since 2.0.0
     */
    $template_label = apply_filters('elementor/template-library/get_template_label_by_type', $template_label, $template_type);

    return $template_label;
}

function is_current_screen()
{
    global $pagenow, $typenow;

    return 'edit.php' === $pagenow && 'elementor_library' === $typenow;
}

function get_current_tab_group($default = '')
{
    $current_tabs_group = 'twbb_templates';
    if (!empty($_REQUEST['elementor_library_type'])) {
        $doc_type = \Elementor\Plugin::instance()->documents->get_document_type($_REQUEST['elementor_library_type'], '');
        if ($doc_type) {
            $current_tabs_group = $doc_type::get_property('admin_tab_group');
        }
    } elseif (!empty($_REQUEST['tabs_group'])) {
        $current_tabs_group = $_REQUEST['tabs_group'];
    }

    return $current_tabs_group;
}

function get_library_title()
{
    $title = '';
    if (is_current_screen()) {
        $current_tab_group = get_current_tab_group();
        if ($current_tab_group) {
            $titles = [
                'library' => __('10Web Templates', 'nerd-builder'),
                'twbb_templates' => __('10Web Templates', 'nerd-builder'),
                'twbb_theme' => __('Theme Builder', 'nerd-builder'),
                'popup' => __('Popups', 'nerd-builder'),
            ];
            if (!empty($titles[$current_tab_group])) {
                $title = $titles[$current_tab_group];
            }
        }
    }

    return $title;
}

