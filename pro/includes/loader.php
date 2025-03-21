<?php
namespace ZIOR\FilePond\Pro;

use ZIOR\FilePond\Pro\Settings as SettingsPro;
use ZIOR\FilePond\Pro\Uploader as UploaderPro;
use ZIOR\FilePond\Pro\Assets as AssetsPro;

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
			if ( ! preg_match( "/^ZIOR\\\\FilePond\\\\Pro.+$/", $class ) ) {
				return;
			}

			$classes = array(
				'Settings' => WP_FILEPOND_PLUGIN_DIR_PRO . 'includes/classes/class-settings.php',
				'Uploader' => WP_FILEPOND_PLUGIN_DIR_PRO . 'includes/classes/class-uploader.php',
				'Assets'   => WP_FILEPOND_PLUGIN_DIR_PRO . 'includes/classes/class-assets.php',
			);

			$class_name = explode( "\\", $class );

			if ( ! empty( $classes[ end( $class_name ) ] ) ) {
				include $classes[ end( $class_name ) ];
			}
		} );

		SettingsPro::get_instance();
		UploaderPro::get_instance();
		AssetsPro::get_instance();
	}
}