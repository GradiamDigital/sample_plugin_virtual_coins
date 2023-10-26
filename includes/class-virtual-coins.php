<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://gradiamdigital.com
 * @since      1.0.0
 *
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Virtual_Coins
 * @subpackage Virtual_Coins/includes
 * @author     gradiamdigital <info@gradiamdigital.com>
 */
class Virtual_Coins {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Virtual_Coins_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VIRTUAL_COINS_VERSION' ) ) {
			$this->version = VIRTUAL_COINS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'virtual-coins';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_custom_posttax_hooks();
		$this->define_events_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Virtual_Coins_Loader. Orchestrates the hooks of the plugin.
	 * - Virtual_Coins_i18n. Defines internationalization functionality.
	 * - Virtual_Coins_Admin. Defines all hooks for the admin area.
	 * - Virtual_Coins_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-virtual-coins-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-virtual-coins-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-virtual-coins-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-virtual-coins-public.php';

		/**
		 * The class responsible for defining custom post types and custom taxonomies
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-virtual-coins-custom-posttax.php';

		/**
		 * The class responsible for managing and handling coins events
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-virtual-coins-events.php';

		$this->loader = new Virtual_Coins_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Virtual_Coins_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Virtual_Coins_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Virtual_Coins_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'create_coins_options_page' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'create_coins_options_acf_group' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'create_coin_post_type_acf_group' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'register_coins_wallet' );
		$this->loader->add_filter( 'manage_coin_posts_columns', $plugin_admin, 'filter_coin_columns' );
		$this->loader->add_action( 'manage_coin_posts_custom_column', $plugin_admin, 'coins_admin_columns', 10, 2 );
		$this->loader->add_filter( 'manage_edit-coin_sortable_columns', $plugin_admin, 'coins_admin_sortable_columns' );
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'admin_coin_posts_orderby' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Virtual_Coins_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

	}

	/**
	 * Register the hooks related to the custom post types and custom taxonomies creation
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_custom_posttax_hooks() {

		$custom_posttax = new Virtual_Coins_Custom_Posts_Taxonomies( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $custom_posttax, 'register_custom_post_types' );

	}

	/**
	 * Register the hooks related to the coins events
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_events_hooks() {

		$events = new Virtual_Coins_Events( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'updated_user_meta', $events, 'coins_collection_point', 10, 4 );
		$this->loader->add_action( 'updated_user_meta', $events, 'coins_notification', 10, 4 );
		$this->loader->add_action( 'added_user_meta', $events, 'coins_collection_point', 10, 4 );
		$this->loader->add_action( 'added_user_meta', $events, 'coins_notification', 10, 4 );
		$this->loader->add_action( 'user_register', $events, 'coins_registration', 10, 1 );
		$this->loader->add_action( 'wp_login', $events, 'coins_login', 10, 2 );
		$this->loader->add_action( 'woocommerce_order_status_processing', $events, 'coins_purchase', 10, 1 );
		$this->loader->add_action( 'comment_post', $events, 'coins_comment_approve', 10, 2 );
		$this->loader->add_action( 'wp_ajax_nopriv_addCoinCheckin', $events, 'coins_checkin' );
		$this->loader->add_action( 'wp_ajax_addCoinCheckin', $events, 'coins_checkin' );
		$this->loader->add_action( 'wp_ajax_nopriv_addCoinsChat', $events, 'coins_chat' );
		$this->loader->add_action( 'wp_ajax_addCoinsChat', $events, 'coins_chat' );
		$this->loader->add_action( 'wp_ajax_nopriv_operateCoinsInCart', $events, 'manage_coins_cart_fee' );
		$this->loader->add_action( 'wp_ajax_operateCoinsInCart', $events, 'manage_coins_cart_fee' );
		$this->loader->add_action( 'wp_ajax_nopriv_addRegisterCoins', $events, 'get_register_coins' );
		$this->loader->add_action( 'wp_ajax_addRegisterCoins', $events, 'get_register_coins' );
		$this->loader->add_action( 'woocommerce_update_cart_action_cart_updated', $events, 'change_coins_cart_fee_on_cart_change' );
		$this->loader->add_action( 'woocommerce_cart_calculate_fees', $events, 'add_coins_cart_fee' );
		$this->loader->add_action( 'woocommerce_new_order', $events, 'redeem_cart_coins', 10, 1 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Virtual_Coins_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
