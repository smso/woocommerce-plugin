<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.smso.ro/
 * @since      1.0.0
 *
 * @package    smso
 * @subpackage smso/includes
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
 * @package    smso
 * @subpackage smso/includes
 * @author     smso <support@smso.ro>
 */
class Smso {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() 
	{
		if ( defined( 'SMSO_VERSION' ) ) {
			$this->version = SMSO_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'smso';
		$this->load_dependencies();
		$this->set_locale();

		$this->define_admin_hooks();
		$this->define_admin_filter();

		$this->define_public_hooks();		
		
		$this->set_default_values();		 
	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/smso-class.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/smso-loader.php';
		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/smso-i18n.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/smso-admin-callback.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/smso-admin.php';		 
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/smso-public.php';
		$this->loader = new Smso_Loader();
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() 
	{
		$smso_i18n = new Smso_i18n();
		$this->loader->add_action( 'plugins_loaded', $smso_i18n, 'load_plugin_textdomain' );
	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_default_values()
	{
		if(get_option('smso_processing_active') === false){
			$message = 'Buna {billing_first_name}, {billing_last_name} Doar pentru informare — comanda cu numarul {order_number} trimisa pe data de {order_date} este in stare de procesare: Total de plata: {order_total}';
		   add_option('smso_processing_active', "true");
		   add_option('smso_processing_message', $message);
		}
	}
	private function define_admin_hooks() 
	{
		$smso_admin = new Smso_Admin( $this->get_plugin_name(), $this->get_version() );
		$smso_admin_callback = new Smso_Admin_Callback( $this->get_plugin_name(), $this->get_version() );		 
		$smso_admin->add_menu_page( 'Custom Login', 'SMSO', 'manage_options', 'smso', $smso_admin_callback, 'admin_api_settings', 'dashicons-smartphone', '110');
		$this->loader->add_action( 'admin_enqueue_scripts', $smso_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $smso_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $smso_admin, 'run_menu_page' );
		$this->loader->add_action("woocommerce_order_status_changed", $smso_admin,"smso_order_status_changed");
		// $this->loader->add_action("woocommerce_checkout_update_order_meta", $smso_admin,"smso_checkout_update_order_meta");
		// $this->loader->add_action("woocommerce_after_checkout_validation", $smso_admin,"smso_after_checkout_validation", 10, 2);
	}
	private function define_admin_filter()
	{
		$smso_admin = new Smso_Admin( $this->get_plugin_name(), $this->get_version() );
		// $this->loader->add_filter("woocommerce_shipping_fields", $smso_admin, "smso_shipping_fields");
	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() 
	{
		//$smso_public = new Smso_Public( $this->get_plugin_name(), $this->get_version() );
		//$this->loader->add_action( 'wp_enqueue_scripts', $smso_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $smso_public, 'enqueue_scripts' );
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() 
	{
		$this->loader->run();
	}
	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() 
	{
		return $this->plugin_name;
	}
	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() 
	{
		return $this->loader;
	}
	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() 
	{
		return $this->version;
	}
}
