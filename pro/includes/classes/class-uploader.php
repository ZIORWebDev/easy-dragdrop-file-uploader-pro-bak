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
	 * Adds a file to the WordPress media library.
	 *
	 * @param string $file_url The URL of the file to add.
	 * @return int The attachment ID on success, 0 on failure.
	 */
	private function add_to_media_library( string $file_url ): int {
		// Get the file path from the URL
		$file_path = $this->get_file_path_from_url( $file_url );

		if ( ! file_exists( $file_path ) ) {
			return 0; // File does not exist
		}

		// Get the file type
		$file_type = wp_check_filetype( $file_path );

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

		return $attach_id;
	}

	/**
	 * Converts a file URL to a file path.
	 *
	 * @param string $file_url The file URL.
	 * @return string The absolute file path.
	 */
	private function get_file_path_from_url( string $file_url ): string {
		$upload_dir = wp_upload_dir();
		$base_url   = $upload_dir['baseurl'];
		$base_path  = $upload_dir['basedir'];

		// Ensure the file is inside the uploads directory
		if ( strpos( $file_url, $base_url ) !== false ) {
			return str_replace( $base_url, $base_path, $file_url );
		}

		return '';
	}

	/**
	 * Class constructor.
	 *
	 * Hooks into WordPress to add the plugin's settings page and register settings.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_filepond_process_field', array( $this, 'process_field' ), 10, 3 );
	}

	public function process_field( array $field ): void {
		if ( empty( $field ) ) {
			return;
		}

		if ( empty( $field['raw_value'] ?? '' ) ) {
			return;
		}

		$plugin_options = get_plugin_options();

		if ( ! ( $plugin_options['wp_filepond_enable_media_library'] ?? false ) ) {
			return;
		}

		foreach ( $field['raw_value'] as $file_url ) {
			$attach_id = $this->add_to_media_library( $file_url );
		}

		return;
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
