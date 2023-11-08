<?php
/**
 * Class Woocommerce
 *
 * @package Tenweb_Builder\Classes\Woocommerce
 */

namespace Tenweb_Builder\Classes\Woocommerce;

use \Tenweb_Builder\Templates;
use Tenweb_Builder\Widgets\Woocommerce\Products\Classes\Products_Renderer;

class Woocommerce {

  const WOOCOMMERCE_GROUP = 'tenweb-woocommerce-widgets';
  const WOOCOMMERCE_BUILDER_GROUP = 'tenweb-woocommerce-builder-widgets';
  protected static $instance = NULL;

  /**
   * Woocommerce constructor.
   */
  public function __construct() {
    if ( $this->is_active() ) {
      $this->get_actions();
    }
  }

  /**
   * @return null|Woocommerce
   */
  public static function get_instance() {
    if ( self::$instance === NULL ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public static function is_template_page() {
    if ( \Elementor\Plugin::instance()->editor->is_edit_mode() && Templates::get_instance()->is_elementor_template_type() ) {
      return TRUE;
    }

    return FALSE;
  }

  public static function get_preview_product() {
    $posts = get_posts([
                         'post_type' => 'product',
                         'posts_per_page' => 1,
                         'orderby' => 'id',
                         'order' => 'ASC',
                       ]);
    if ( !empty($posts[0]) ) {
      return $posts[0];
    }

    return FALSE;
  }

  public static function get_upsell_product() {
    $posts = get_posts([
                         'post_type' => 'product',
                         'orderby' => 'id',
                         'order' => 'ASC',
                       ]);
    foreach ( $posts as $post ) {
      if ( isset($post->ID) ) {
        $product = wc_get_product($post->ID);
        $upsell_ids = $product->get_upsell_ids();
        if ( isset($upsell_ids) && is_array($upsell_ids) && !empty($upsell_ids) ) {
          return $product;
        }
      }
    }

    return FALSE;
  }

  public static function add_new_product_link() {
    $new_product_href = add_query_arg(array( 'post_type' => 'product' ), admin_url('post-new.php'));
    $new_product_link = '<a href="' . $new_product_href . '" target="_blank">' . __("New Product", "nerd-builder") . '</a>';
    $pattern = __('Product not found, please add %s', 'nerd-builder');

    return wp_sprintf($pattern, $new_product_link);
  }

  public function get_actions() {
    // On Editor - Register WooCommerce frontend hooks before the Editor init.
    // Priority = 5, in order to allow plugins remove/add their wc hooks on init.
    if ( !empty($_REQUEST['action']) && 'elementor' === $_REQUEST['action'] && is_admin() && $this->is_active() ) {
      add_action('init', [ $this, 'register_wc_hooks' ], 5);
    }
    add_action('elementor/widgets/register', [ $this, 'register_widgets' ]);
    if ( TWBB_DEV === TRUE ) {
      add_action('twbb_after_enqueue_styles', [ $this, 'after_enqueue_styles' ], 10, 2);
      add_action('twbb_after_enqueue_scripts', [ $this, 'after_enqueue_scripts' ], 10, 2);
      add_action('twbb_before_enqueue_editor_scripts', [ $this, 'before_enqueue_editor_scripts' ], 10, 2);
    }
	  add_action('elementor/editor/before_enqueue_scripts', [ $this, 'maybe_init_cart' ]);
	  add_filter('woocommerce_add_to_cart_fragments', [ $this, 'menu_cart_fragments' ]);
	  add_action('wp_enqueue_scripts', [ $this, 'enqueue_woocommerce_scripts' ], 11);
	  add_filter( 'woocommerce_locate_template', [ $this, 'twbb_woocommerce_locate_template' ], 10, 3 );
      add_action( 'woocommerce_after_shop_loop_item_title', array($this,'get_description_of_products'), 7 );
  }

    public function get_description_of_products() {
        echo '<p class="twbb_woocommerce-loop-product__desc">' . $this->get_woccommerce_excerpt() . '</p>';
    }
    protected function get_woccommerce_excerpt(){
        $excerpt = get_the_excerpt();
        $excerpt = strip_shortcodes($excerpt);
        $excerpt = strip_tags($excerpt);
        $excerpt = trim($excerpt);
        $excerpt_length = isset( $this->desc_excerpt_length ) ? $this->desc_excerpt_length : 25;
        $excerpt = substr($excerpt, 0, $excerpt_length);
        return $excerpt;
    }
  /**
   * Is active
   *
   * @return bool
   */
  private function is_active() {
    return class_exists('woocommerce');
  }

  /**
   * Register wc_hooks
   * Include woocommerce php file and run WC Session
   */
  public function register_wc_hooks() {
    WC()->frontend_includes();
    if ( !WC()->session ) {
      $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
      WC()->session = new $session_class();
      WC()->session->init();
    }
  }

  public function menu_cart_fragments( $fragments ) {
    $has_cart = is_a(WC()->cart, 'WC_Cart');
    if ( !$has_cart ) {
      return $fragments;
    }
    ob_start();
    self::render_menu_cart_toggle_button();
    $menu_cart_html = ob_get_clean();
    if ( !empty($menu_cart_html) ) {
      $fragments['body:not(.elementor-editor-active) div.elementor-element.elementor-widget.elementor-widget-twbb_woocommerce-menu-cart div.twbb_menu-cart__toggle.elementor-button-wrapper'] = $menu_cart_html;
    }

    return $fragments;
  }

  public function maybe_init_cart() {
    $has_cart = is_a(WC()->cart, 'WC_Cart');
    if ( !$has_cart ) {
      $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler');
      WC()->session = new $session_class();
      WC()->session->init();
      WC()->cart = new \WC_Cart();
      WC()->customer = new \WC_Customer(get_current_user_id(), TRUE);
    }
  }

  public function enqueue_woocommerce_scripts() {
    // In preview mode it's not a real Product page.
    if ( Templates::get_instance()->is_elementor_template_type() && self::get_preview_product() ) {
      add_filter('body_class', [ $this, 'wc_body_class' ]);
      if ( current_theme_supports('wc-product-gallery-zoom') ) {
        wp_enqueue_script('zoom');
      }
      if ( current_theme_supports('wc-product-gallery-slider') ) {
        wp_enqueue_script('flexslider');
      }
      if ( current_theme_supports('wc-product-gallery-lightbox') ) {
        wp_enqueue_script('photoswipe-ui-default');
        wp_enqueue_style('photoswipe-default-skin');
        add_action('wp_footer', 'woocommerce_photoswipe');
      }
      wp_enqueue_script('wc-single-product');
      wp_enqueue_style('photoswipe');
      wp_enqueue_style('photoswipe-default-skin');
      wp_enqueue_style('woocommerce_prettyPhoto_css');
    }
  }

  function wc_body_class( $classes = [] ) {
    $classes = (array) $classes;
    $classes[] = 'woocommerce';
    $classes[] = 'woocommerce-page';

    return array_unique($classes);
  }

  public static function render_menu_cart_toggle_button() {
		if ( null === WC()->cart ) {
			return;
		}
		$product_count = WC()->cart->get_cart_contents_count();
		$sub_total = WC()->cart->get_cart_subtotal();
		$counter_attr = 'data-counter="' . $product_count . '"';

		?>
        <div class="twbb_menu-cart__wrapper">
        <div class="twbb_menu-cart__toggle elementor-button-wrapper">
            <a id="twbb_menu-cart__toggle_button" href="#" class="elementor-button elementor-size-sm">
                <span class="elementor-button-text"><?php echo $sub_total; ?></span>
                <span class="elementor-button-icon" <?php echo $counter_attr; ?>>
					<i class="eicon twbb-widget-icon" aria-hidden="true"></i>
					<span class="elementor-screen-only"><?php esc_html_e( 'Cart', 'nerd-builder' ); ?></span>
				</span>
            </a>
        </div>
        </div>

		<?php
	}

  public static function render_menu_cart() {
	  if ( null === WC()->cart ) {
		  return;
	  }
    $widget_cart_is_hidden = apply_filters('woocommerce_widget_cart_is_hidden', is_cart() || is_checkout());
    if ( isset(WC()->cart) ) {

	    /**
	     * The `widget_shopping_cart_content` div will be populated by woocommerce js.
	     */
      ?>

        <div class="twbb_menu-cart__wrapper">
		    <?php if (  !$widget_cart_is_hidden ) : ?>

            <div class="twbb_menu-cart__container elementor-lightbox"  data-cart="<?php echo wc_get_cart_url();?>" data-checkout="<?php echo wc_get_checkout_url(); ?>">
                <div class="twbb_menu-cart__main">
                    <div class="twbb_menu-cart__close-button"></div>
                    <div class="widget_shopping_cart_content"></div>
                </div>
            </div>
			    <?php self::render_menu_cart_toggle_button();
		    endif; ?>
        </div>
      <?php
    }
  }

  /*
   * for getting mini-cart.php template
   */
    public function twbb_woocommerce_locate_template( $template, $template_name, $template_path ){

        if ( 'cart/mini-cart.php' !== $template_name ) {
            return $template;
        }

        $plugin_path = plugin_dir_path( __DIR__ ) . 'widgets/woocommerce/menu-cart/template/';
        if ( file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }

        return $template;
    }


  /**
   * Get widgets.
   *
   * @return array
   */
  private function get_widgets() {
    return [
      'products',
      'product-page',
      'product-price',
      'product-title',
      'product-rating',
      'product-images',
      'product-content',
      'product-related',
      'product-data-tabs',
      'product-meta',
      'product-stock',
      'product-short-description',
      'product-additional-information',
      'categories',
      'breadcrumb',
      'add-to-cart',
      'menu-cart',
      'product-add-to-cart',
      'product-upsell',
//      'price-filter',
    ];
  }

  /**
   * Register widgets.
   */
  public function register_widgets() {
    if ( $this->is_active() ) {
      foreach ( $this->get_widgets() as $widget ) {
        $file = TWBB_DIR . '/widgets/woocommerce/' . $widget . '/controller.php';
        if ( is_file($file) ) {
          require_once $file;
        }
      }
    }
  }

  public function after_enqueue_styles() {
    wp_enqueue_style('twbb-woocommerce-products-style', TWBB_URL . '/widgets/woocommerce/products/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-rating-style', TWBB_URL . '/widgets/woocommerce/product-rating/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-menu-cart-style', TWBB_URL . '/widgets/woocommerce/menu-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-add-to-cart-style', TWBB_URL . '/widgets/woocommerce/add-to-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-categories-style', TWBB_URL . '/widgets/woocommerce/categories/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-related-style', TWBB_URL . '/widgets/woocommerce/product-related/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-meta-style', TWBB_URL . '/widgets/woocommerce/product-meta/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-add-to-cart-style', TWBB_URL . '/widgets/woocommerce/product-add-to-cart/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-upsell-style', TWBB_URL . '/widgets/woocommerce/product-upsell/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-product-additional-information', TWBB_URL . '/widgets/woocommerce/product-additional-information/assets/style.css', [], TWBB_VERSION);
    wp_enqueue_style('twbb-woocommerce-pricing-filter-style', TWBB_URL . '/widgets/woocommerce/price-filter/assets/style.css', [], TWBB_VERSION);
  }

  public function after_enqueue_scripts() {
    wp_enqueue_script('twbb-woocommerce-products-script', TWBB_URL . '/widgets/woocommerce/products/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-menu-cart-script', TWBB_URL . '/widgets/woocommerce/menu-cart/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-data-tabs-script', TWBB_URL . '/widgets/woocommerce/product-data-tabs/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-page-script', TWBB_URL . '/widgets/woocommerce/product-page/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-product-images-script', TWBB_URL . '/widgets/woocommerce/product-images/assets/script.js', [], TWBB_VERSION, TRUE);
    wp_enqueue_script('twbb-woocommerce-pricing-filter-script', TWBB_URL . '/widgets/woocommerce/price-filter/assets/script.js', [], TWBB_VERSION);
  }

  public function before_enqueue_editor_scripts() {
    // wp_enqueue_script('twbb-woocommerce-script', TWBB_URL . '/before_woocommerce_editor_scripts.js', [], TWBB_VERSION, true);
  }
}
