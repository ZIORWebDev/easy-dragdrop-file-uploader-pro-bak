<?php
namespace ZIOR\FilePond\Pro;

use function ZIOR\FilePond\get_configuration;
use function ZIOR\FilePond\get_plugin_options;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Uploader {

	/**
	 * Contains instance or null
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 *
	 * Hooks into WordPress to add the plugin's settings page and register settings.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_filepond_after_upload', array( $this, 'after_upload' ), 10, 3 );
	}

	public function after_upload( $uploaded, $file, $args ) {
		$plugin_options = get_plugin_options();

		if ( ! ( $plugin_options['wp_filepond_enable_media_library'] ?? false ) ) {
			return;
		}

		// Prepare an array of post data for the attachment
		$attachment = array(
			'post_mime_type' => $file_type['type'],
			'post_title'     => sanitize_file_name( basename( $file_path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the attachment
		$attach_id = wp_insert_attachment( $attachment, $file_path );

		// Generate attachment metadata
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id; // Return attachment ID
	}

	/**
	 * Returns instance of Settings.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
