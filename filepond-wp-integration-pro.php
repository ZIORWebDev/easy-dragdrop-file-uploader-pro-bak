<?php
/**
 * Plugin Name:  FilePond WP Integration Pro
 * Plugin URI:   
 * Description:  Adds a FilePond drag and drop uploader field to Elementor Pro Forms for seamless file uploads.
 * Author:       ZiorWeb.Dev
 * Author URI:   https://ziorweb.dev
 * Version:      1.0.0
 * Requires PHP: 8.0
 * Requires WP:  6.0
 * License:      GPL-2.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wp-filepond
 * Domain Path:  /languages
 */

namespace ZIOR\FilePond;

use ZIOR\FilePond\Pro\Loader as LoaderPro;
use Mimey\MimeMappingBuilder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * Handles plugin initialization, constants, includes, and hooks.
 */
class Plugin {

	/**
	 * The single instance of the plugin.
	 *
	 * @var Plugin|null
	 */
	protected static ?Plugin $instance = null;

	/**
	 * Includes necessary plugin files.
	 */
	private function includes(): void {
		require_once WP_FILEPOND_PLUGIN_DIR . 'vendor/autoload.php';
		require_once WP_FILEPOND_PLUGIN_DIR . 'includes/functions.php';
		require_once WP_FILEPOND_PLUGIN_DIR . 'includes/loader.php';

		require_once WP_FILEPOND_PLUGIN_DIR_PRO . 'includes/functions.php';
		require_once WP_FILEPOND_PLUGIN_DIR_PRO . 'includes/loader.php';
	}

	/**
	 * Initializes the plugin.
	 *
	 * Sets up constants, includes required files, and loads the plugin.
	 */
	private function init(): void {
		$this->setup_constants();
		$this->includes();

		$loader = Loader::get_instance();
		$loader->load();

		// Load pro features
		$loaderPro = LoaderPro::get_instance();
		$loaderPro->load();
	}

	/**
	 * Defines plugin constants.
	 *
	 * Ensures constants are only defined once to prevent conflicts.
	 */
	private function setup_constants(): void {
		if ( ! defined( "WP_FILEPOND_PLUGIN_DIR" ) ) {
			define( "WP_FILEPOND_PLUGIN_DIR", plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( "WP_FILEPOND_PLUGIN_DIR_PRO" ) ) {
			define( "WP_FILEPOND_PLUGIN_DIR_PRO", plugin_dir_path( __FILE__ ) . 'pro/' );
		}

		if ( ! defined( "WP_FILEPOND_PLUGIN_URL" ) ) {
			define( "WP_FILEPOND_PLUGIN_URL", plugin_dir_url( __FILE__ ) );
		}

		if ( ! defined( "WP_FILEPOND_PLUGIN_FILE" ) ) {
			define( "WP_FILEPOND_PLUGIN_FILE", __FILE__ );
		}

		if ( ! defined( "WP_FILEPOND_MIME_CACHE_FILE" ) ) {
			define( "WP_FILEPOND_MIME_CACHE_FILE", plugin_dir_path( __FILE__ ) . 'cache/mimey.php' );
		}
	}

	/**
	 * Constructor.
	 *
	 * Initializes hooks required for the plugin.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Retrieves the singleton instance of the plugin.
	 *
	 * @return Plugin The single instance of the plugin.
	 */
	public static function get_instance(): Plugin {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Loads plugin text domain for translations.
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain( 'wp-filepond', false, WP_FILEPOND_PLUGIN_DIR . '/languages' );
	}

	/**
	 * Plugin activation callback.
	 *
	 * This function is executed when the plugin is activated.
	 */
	public function activate_plugin(): void {}

	/**
	 * Plugin deactivation callback.
	 *
	 * This function is executed when the plugin is deactivated.
	 */
	public function deactivate_plugin(): void {}

	/**
	 * Adds a "Settings" link on the plugins page.
	 *
	 * @param array $links The existing action links.
	 * @return array Modified action links with the "Settings" link.
	 */
	public function add_settings_link( array $links ): array {
		// Define the settings link URL
		$settings_url  = admin_url( 'options-general.php?page=wp-filepond' );
		$settings_link = sprintf( '<a href="%s">', $settings_url ) . esc_html__( 'Settings', 'wp-filepond' ) . '</a>';

		// Prepend the settings link to the existing links.
		array_unshift( $links, $settings_link );

		return $links;
	}
}

/**
 * Initializes and starts the plugin.
 */
$plugin = Plugin::get_instance();

/**
 * Registers plugin activation and deactivation hooks.
 */
register_activation_hook( WP_FILEPOND_PLUGIN_FILE, array( $plugin, 'activate_plugin' ) );
register_deactivation_hook( WP_FILEPOND_PLUGIN_FILE, array( $plugin, 'deactivate_plugin' ) );
