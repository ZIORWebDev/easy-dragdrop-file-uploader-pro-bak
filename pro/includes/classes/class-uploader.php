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
	private function add_to_media_library( string $file_path ): int {
		if ( ! file_exists( $file_path ) ) {
			return 0;
		}

		// Get the file type
		$file_type = @wp_check_filetype( $file_path );

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
	 * Class constructor.
	 *
	 * Hooks into WordPress to add the plugin's settings page and register settings.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_filepond_process_field', array( $this, 'process_field' ), 10 );
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

		$files = is_array( $field['raw_value'] ) ? $field['raw_value'] : array( $field['raw_value'] );

		foreach ( $files as $file ) {
			$attach_id = $this->add_to_media_library( $file );
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
