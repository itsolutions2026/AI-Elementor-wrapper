<?php

namespace Tenweb_Builder\Widgets\Woocommerce\Products;
include_once('widgets/products-base.php');
include_once('classes/products-renderer.php');

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Tenweb_Builder\Classes\Woocommerce\Woocommerce;
use Tenweb_Builder\Widgets\Woocommerce\Products\Widgets\Products_Base;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;
use Tenweb_Builder\Controls\QueryControl\QueryController;
use Tenweb_Builder\Controls\QueryControl\Controls\Group_Control_Posts;

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class Products extends Products_Base {
    public function get_name() {
        return 'twbb_woocommerce-products';
    }

    public function get_title() {
        return __('Products', 'nerd-builder');
    }

    public function get_icon() {
        return 'twbb-products twbb-widget-icon';
    }

    public function get_categories() {
        return [ Woocommerce::WOOCOMMERCE_GROUP ];
    }

    public function get_keywords() {
        return [ 'woocommerce', 'shop', 'store', 'product' ];
    }

    protected function register_controls() {
        $this->start_controls_section('section_content', [
            'label' => __('Content', 'nerd-builder'),
        ]);
        $this->add_responsive_control('columns', [
            'label' => __('Columns', 'nerd-builder'),
            'type' => Controls_Manager::NUMBER,
            'prefix_class' => 'elementor-products-columns%s-',
            'min' => 1,
            'max' => 12,
            'default' => 4,
            'tablet_default' => '2',
            'mobile_default' => '1',
            'render_type' => 'template',
            'required' => TRUE,
            'device_args' => [
                Controls_Stack::RESPONSIVE_TABLET => [
                    'required' => FALSE,
                ],
                Controls_Stack::RESPONSIVE_MOBILE => [
                    'required' => FALSE,
                ],
            ],
            'min_affected_device' => [
                Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
                Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
            ],
        ]);
        $this->add_control('rows', [
            'label' => __('Rows', 'nerd-builder'),
            'type' => Controls_Manager::NUMBER,
            'default' => 4,
            'render_type' => 'template',
            'range' => [
                'px' => [
                    'max' => 20,
                ],
            ],
        ]);
        $this->add_control('paginate', [
            'label' => __('Pagination', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
        ]);
        $this->add_control('ajax_paginate', [
            'label' => __('Ajax Pagination', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'paginate' => 'yes',
            ],
        ]);
        $this->add_control('allow_order', [
            'label' => __('Show Sorting', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'paginate' => 'yes',
            ],
        ]);
        $this->add_control(
            'wc_notice_frontpage',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__( 'Ordering is not available if this widget is placed in your front page. Visible on frontend only.', 'nerd-builder' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'paginate' => 'yes',
                    'allow_order' => 'yes',
                ],
            ]
        );
        $this->add_control('show_result_count', [
            'label' => __('Show Result Count', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'condition' => [
                'paginate' => 'yes',
            ],
        ]);
        $this->add_control('hide_products_images', [
            'label' => __('Hide Products Images', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'separator' => 'before',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products .attachment-woocommerce_thumbnail,
          {{WRAPPER}}.elementor-wc-products .woocommerce-placeholder' => 'display: none',
            ],
        ]);
        $this->add_control('hide_products_titles', [
            'label' => __('Hide Products Titles', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .woocommerce-loop-product__title' => 'display: none',
            ],
        ]);
        $this->add_control('hide_products_description', [
            'label' => __('Hide Poducts Descriptons', 'nerd-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'selectors' => [
                '{{WRAPPER}}.elementor-wc-products ul.products li.product .twbb_woocommerce-loop-product__desc' => 'display: none',
            ],
        ]);
        $this->add_control(
            'description_length',
            [
                'label' => __('Description Length', 'nerd-builder'),
                'type' => Controls_Manager::NUMBER,
                /** This filter is documented in wp-includes/formatting.php */
                'default' => apply_filters('excerpt_length', 25),
                'condition' => [
                    'hide_products_description!' => 'yes',
                ],
            ]
        );
        $this->end_controls_section();
        $this->register_query_controls();
        parent::register_controls();
    }

    protected function register_query_controls() {
        $this->start_controls_section('section_query', [
            'label' => __('Query', 'nerd-builder'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_group_control(Group_Control_Posts::get_type(), [
            'name' => 'query',
            'post_type' => 'product',
            'fields_options' => [
                'post_type' => [
                    'default' => 'product',
                    'options' => [
                        'current_query' => __('Current Query', 'nerd-builder'),
                        'product' => __('Latest Products', 'nerd-builder'),
                        'sale' => __('Sale', 'nerd-builder'),
                        'featured' => __('Featured', 'nerd-builder'),
                        'by_id' => _x('Manual Selection', 'Posts Query Control', 'nerd-builder'),
                    ],
                ],
                'product_cat_ids' => [
                    'condition' => [
                        'query_post_type!' => [
                            'current_query',
                            'by_id',
                        ],
                    ],
                ],
                'product_tag_ids' => [
                    'condition' => [
                        'query_post_type!' => [
                            'current_query',
                            'by_id',
                        ],
                    ],
                ],
            ],
            'exclude' => [
                'authors',
            ],
        ]);
        $this->add_control('advanced', [
            'label' => __('Advanced', 'nerd-builder'),
            'type' => Controls_Manager::HEADING,
        ]);
        $this->add_control('orderby', [
            'label' => __('Order by', 'nerd-builder'),
            'type' => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => __('Date', 'nerd-builder'),
                'title' => __('Title', 'nerd-builder'),
                'price' => __('Price', 'nerd-builder'),
                'popularity' => __('Popularity', 'nerd-builder'),
                'rating' => __('Rating', 'nerd-builder'),
                'rand' => __('Random', 'nerd-builder'),
                'menu_order' => __('Menu Order', 'nerd-builder'),
            ],
            'condition' => [
                'query_post_type!' => 'current_query',
            ],
        ]);
        $this->add_control('order', [
            'label' => __('Order', 'nerd-builder'),
            'type' => Controls_Manager::SELECT,
            'default' => 'desc',
            'options' => [
                'asc' => __('ASC', 'nerd-builder'),
                'desc' => __('DESC', 'nerd-builder'),
            ],
            'condition' => [
                'query_post_type!' => 'current_query',
            ],
        ]);
        QueryController::add_exclude_controls([ 'widget' => $this, 'filter_by' => 'product' ]);
        $this->end_controls_section();
    }

    protected function render() {
        if ( WC()->session ) {
            wc_print_notices();
        }
        // For Products_Renderer.
        if ( !isset($GLOBALS['post']) ) {
            $GLOBALS['post'] = NULL; // WPCS: override ok.
        }

        $this->get_products_min_max_prices();
        $this->set_wc_thumbnail_single_image_width();
        $shortcode = new Products_Renderer($this->get_settings(), 'products');
        $content = $shortcode->get_content();
        if( $this->get_settings()['ajax_paginate'] == '' ) {
            echo $content;
        } else {
            echo '<div class="twbb_woocommerce-products-ajax-paginate">' . $content . '</div>';
        }
    }

    private function set_wc_thumbnail_single_image_width() {
        $settings = $this->get_settings();
        foreach ( [ 'thumbnail_image_width' => 600, 'single_image_width' => 800 ] as $key => $val ) {
            if ( !empty($settings[$key]) ) {
                $separator = '%%';
                $image_width = explode($separator, $settings[$key]);
                $wc_image_width = $val;
                if ( !empty($image_width[1]) ) {
                    $wc_image_width = $image_width[1];
                }
                if ( $settings[$key] == 'custom' && !empty($settings[$key . '_custom']) && !empty($settings[$key . '_custom']) ) {
                    $wc_image_width = $settings[$key . '_custom'];
                }
                update_option( 'twbb_wc_' . $key, intval($wc_image_width) );
            }
        }
    }


    /**
     * Get min and max prices from current products,compare
     * with last values and set actual min and max values
     *
     * @return null
     */
    private function get_products_min_max_prices() {
        $settings = $this->get_settings();
        $is_excludes = in_array('manual_selection', (isset($settings['exclude']) && $settings['exclude'] ? $settings['exclude'] : array()));
        $is_excludes_current = in_array('current_post', (isset($settings['exclude']) && $settings['exclude'] ? $settings['exclude'] : array()));
        $excluded_products_id = $is_excludes ? (!empty($settings['exclude_ids']) ? $settings['exclude_ids'] : array()) : array();
        if ($is_excludes_current) {
            global $product;
            $current_id = (isset($product) && $product ? $product->get_id() : null); // get current product id if these are single product pages
            array_push($excluded_products_id, $current_id);
        }

        if ($settings['orderby'] == 'rand') {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => '-1',
            );
        } else {
            $shortcode_products_count = $settings['paginate'] ? -1 : $settings['rows']*$settings['columns'];
            $args = array(
                'post_type' => 'product',
                'post__not_in' => $excluded_products_id,
                'posts_per_page' => $shortcode_products_count,
                'order' => $settings['order']
            );
        }
        $loop = new \WP_Query($args);
        $available_products_price = array();

        foreach ( $loop->posts as $post ) {
            $product = wc_get_product( $post->ID );
            array_push($available_products_price, $product->get_price());
        }
        if ( count($available_products_price)) {
            if (!isset(self::$products_min_max_prices['allMinPrice'])) {
                self::$products_min_max_prices['allMinPrice'] = min($available_products_price);
            } else if (isset(self::$products_min_max_prices['allMinPrice']) && self::$products_min_max_prices['allMinPrice'] > min($available_products_price)) {
                self::$products_min_max_prices['allMinPrice'] = min($available_products_price);
            }
            if (!isset(self::$products_min_max_prices['allMaxPrice'])) {
                self::$products_min_max_prices['allMaxPrice'] = max($available_products_price);
            } else if (isset(self::$products_min_max_prices['allMaxPrice']) && self::$products_min_max_prices['allMaxPrice'] < max($available_products_price)) {
                self::$products_min_max_prices['allMaxPrice'] = max($available_products_price);
            }
        }
        if (!isset($_POST['min_price']) && !isset(self::$products_min_max_prices['currentMinPrice'])) {
            self::$products_min_max_prices['currentMinPrice'] = (int)self::$products_min_max_prices['allMinPrice'];
        }
        if ( isset($_POST['min_price'])) {
            if ((int)self::$products_min_max_prices['allMinPrice'] <= (int)$_POST['min_price'])
                self::$products_min_max_prices['currentMinPrice'] = (int)$_POST['min_price'];
        }
        if (!isset($_POST['max_price']) && !isset(self::$products_min_max_prices['currentMaxPrice'])) {
            self::$products_min_max_prices['currentMaxPrice'] = (int)self::$products_min_max_prices['allMaxPrice'];
        }
        if ( isset($_POST['max_price'])) {
            if ((int)self::$products_min_max_prices["allMaxPrice"] >= (int)$_POST['max_price'])
                self::$products_min_max_prices['currentMaxPrice'] = (int)$_POST['max_price'];
        }
        return NULL;
    }

    public static $products_min_max_prices = array(
        'currentMinPrice' => NULL,
        'allMinPrice' => NULL,
        'currentMaxPrice' => NULL,
        'allMaxPrice' => NULL
    );
}

\Elementor\Plugin::instance()->widgets_manager->register(new Products());
