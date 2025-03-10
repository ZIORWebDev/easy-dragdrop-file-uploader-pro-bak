<?php
namespace ZIOR\FilePond;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Loader {

	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Returns instance of Loader.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers the autoloader and initializes classes..
	 *
	 * Sets up an SPL autoloader to automatically load classes that match a specific
	 * naming convention.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		// Set up SPL autoloader.
		spl_autoload_register( function ( $class ) {

			if ( preg_match( "/^ZIOR\\\\FilePond\\\\Pro\\\\.+$/", $class ) ) {
				return;
			}

			if ( ! preg_match( "/^ZIOR\\\\FilePond.+$/", $class ) ) {
				return;
			}

			$classes = array(
				'Assets'         => WP_FILEPOND_PLUGIN_DIR . 'includes/classes/class-assets.php',
				'FilePondUpload' => WP_FILEPOND_PLUGIN_DIR . 'includes/classes/integration/elementor/class-filepond-upload.php',
				'Register'       => WP_FILEPOND_PLUGIN_DIR . 'includes/classes/class-register.php',
				'Settings'       => WP_FILEPOND_PLUGIN_DIR . 'includes/classes/class-settings.php',
				'Uploader'       => WP_FILEPOND_PLUGIN_DIR . 'includes/classes/class-uploader.php',
			);

			$class_name = explode( "\\", $class );

			if ( ! empty( $classes[ end( $class_name ) ] ) ) {
				include $classes[ end( $class_name ) ];
			}
		} );

		Settings::get_instance();
		Assets::get_instance();	
		Uploader::get_instance();
		Register::get_instance();
	}
}