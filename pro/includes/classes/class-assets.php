<?php
namespace ZIOR\FilePond\Pro;

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
		add_action( 'enqueue_wp_filepond_scripts', array( $this, 'enqueue_wp_filepond_scripts' ), 10 );
	}

	/**
	 * Enqueues scripts and styles.
	 */
	public function enqueue_wp_filepond_scripts(): void {
		// Enqueue heic2any script
		wp_enqueue_script( 'wp-filepond-heic2any', WP_FILEPOND_PLUGIN_URL . 'dist/heic2any.min.js', array(), null, true );
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
