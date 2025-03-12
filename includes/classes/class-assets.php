<?php
namespace ZIOR\FilePond;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles FilePond integration for WordPress.
 */
class Assets {

	/**
	 * Singleton instance of the class.
	 *
	 * @var Assets|null
	 */
	private static ?Assets $instance = null;

	/**
	 * Constructor.
	 *
	 * Hooks into WordPress to enqueue scripts and styles.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 10 );
	}

	/**
	 * Enqueues scripts and styles for the admin area.
	 */
	public function enqueue_admin_scripts(): void {}

	/**
	 * Enqueues scripts and styles for the front-end.
	 */
	public function enqueue_frontend_scripts(): void {
		$uploader_configurations = get_uploader_configurations();

		wp_enqueue_style( 'wp-filepond-vendors', WP_FILEPOND_PLUGIN_URL . 'dist/vendors.min.css', array(), null );
		wp_enqueue_script( 'wp-filepond-vendors', WP_FILEPOND_PLUGIN_URL . 'dist/vendors.min.js', array(), null, true );

		wp_enqueue_style( 'wp-filepond-uploader', WP_FILEPOND_PLUGIN_URL . 'dist/main.min.css', array(), null );
		wp_enqueue_script( 'wp-filepond-uploader', WP_FILEPOND_PLUGIN_URL . 'dist/main.min.js', array( 'jquery' ), null, true );

		wp_localize_script( 'wp-filepond-uploader', 'FilePondUploader', $uploader_configurations );

		
	}

	/**
	 * Retrieves the singleton instance of the class.
	 *
	 * @return Assets The single instance of the class.
	 */
	public static function get_instance(): Assets {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
