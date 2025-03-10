<?php
namespace ZIOR\FilePond\Pro;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

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
		add_filter( 'wp_filepond_options', array( $this, 'get_options' ) );
		add_filter( 'wp_filepond_settings_fields', array( $this, 'get_settings_fields' ) );
		add_filter( 'wp_filepond_settings_sections', array( $this, 'get_settings_sections' ) );
		add_filter( 'wp_filepond_additional_mime_types', array( $this, 'get_additional_mime_types' ) );
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

	public function get_additional_mime_types( array $mime_types ): array {
		$mime_types = array_merge( $mime_types, array(
			'heic' => 'image/heic',
			'heif' => 'image/heif',
		) );

		return $mime_types;
	}

	public function get_settings_fields( $settings_fields ): array {
		$settings_fields = array_merge( $settings_fields, array(
			array(
				'id'       => 'wp_filepond_enable_media_library',
				'title'    => __( 'Add file to media library', 'wp-filepond' ),
				'callback' => array( $this, 'enable_media_library_callback' ),
				'section'  => 'wp_filepond_pro_section',
			),
			array(
				'id'       => 'wp_filepond_enable_preview',
				'title'    => __( 'Enable Preview', 'wp-filepond' ),
				'callback' => array( $this, 'enable_preview_callback' ),
				'section'  => 'wp_filepond_pro_section',
			),
			array(
				'id'       => 'wp_filepond_preview_height',
				'title'    => __( 'Preview Height', 'wp-filepond' ),
				'callback' => array( $this, 'preview_height_callback' ),
				'section'  => 'wp_filepond_pro_section',
			),
			array(
				'id'       => 'wp_filepond_upload_location',
				'title'    => __( 'Upload Location', 'wp-filepond' ),
				'callback' => array( $this, 'upload_location_callback' ),
				'section'  => 'wp_filepond_pro_section',
			),
		) );

		return $settings_fields;
	}

	/**
	 * Returns the settings options for the plugin.
	 *
	 * @return array The settings options.
	 */
	public function get_options( $options ): array {
		$options = array_merge( $options, array(
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_enable_media_library',
				'sanitize'     => 'absint',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_enable_preview',
				'sanitize'     => 'absint',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_preview_height',
				'sanitize'     => 'absint',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_upload_location',
				'sanitize'     => 'sanitize_text_field',
			),
		) );

		return $options;
	}

	/**
	 * Returns the settings sections for the plugin.
	 *
	 * @return array The settings sections.
	 */
	public function get_settings_sections( $settings_sections ): array {
		$settings_sections = array_merge( $settings_sections, array(
			'wp_filepond_pro_section' => array(
				'title'    => __( 'Pro Settings', 'wp-filepond' ),
				'callback' => array( $this, 'section_callback' )
			)
		) );

		return $settings_sections;
	}

	/**
	 * Callback function to render the section description in the settings page.
	 *
	 * This function outputs a brief description for the FilePond integration settings.
	 *
	 * @return void
	 */
	public function section_callback(): void {
		printf(
			'<p>%s</p>',
			esc_html__( 'Configure the WP FilePond Pro integration settings.', 'wp-filepond' )
		);
	}

	/**
	 * Callback function to render the "Enable Preview" checkbox in the settings page.
	 *
	 * This function retrieves the stored option for enabling file preview, ensures its validity,
	 * and outputs a checkbox input field. A description is also provided for user guidance.
	 *
	 * @return void
	 */
	public function enable_preview_callback(): void {
		// Retrieve the enable_preview option from the database, defaulting to false.
		$enable_preview = get_option( 'wp_filepond_enable_preview', 0 );
		$enable_preview = absint( $enable_preview ); // Ensure it's strictly integer

		// Description for the checkbox, with proper escaping for security.
		$description = sprintf(
			'<span class="help-text">%s</span>',
			esc_html__( 'Check if you want to preview the file uploaded.', 'wp-filepond' )
		);

		// Output the checkbox input field with proper escaping and checked attribute handling.
		printf(
			'<label><input type="checkbox" name="wp_filepond_enable_preview" value="1" %s> %s</label>',
			checked( $enable_preview, true, false ), // Ensure proper checkbox handling
			$description
		);
	}

	/**
	 * Callback function to render the "Preview Height" input field in the settings page.
	 *
	 * This function retrieves the stored preview height value, ensures it is a valid integer,
	 * and outputs a number input field. A description is also provided for user guidance.
	 *
	 * @return void
	 */
	public function preview_height_callback(): void {
		// Retrieve the preview height option from the database, defaulting to 100.
		$preview_height = get_option( 'wp_filepond_preview_height', 100 );
		$preview_height = absint( $preview_height );

		// Output the input field with proper escaping.
		printf(
			'<input type="number" name="wp_filepond_preview_height" value="%d" min="1" step="1">',
			esc_attr( $preview_height ) // Escape output to prevent XSS
		);

		// Output the description with proper escaping.
		printf(
			'<p>%s</p>',
			esc_html__( 'Height of the file preview.', 'wp-filepond' )
		);
	}

	/**
	 * Callback function to render the "Upload Location" input field in the settings page.
	 *
	 * This function retrieves the stored upload location value, ensures it is a valid string,
	 * and outputs an input field for user customization.
	 *
	 * @return void
	 */
	public function upload_location_callback(): void {
		// Retrieve the upload location option from the database, defaulting to an empty string.
		$upload_location = get_option( 'wp_filepond_upload_location', '' );
	
		// Output the input field with proper escaping.
		printf(
			'<input type="text" name="wp_filepond_upload_location" value="%s">',
			esc_attr( $upload_location ) // Escape output to prevent XSS
		);

		// Output the description with proper escaping.
		printf(
			'<p>%s</p>',
			esc_html__( 'Location of the uploaded files. The directory relative to the WordPress uploads directory (e.g. "uploads/your-custom-folder"). Leave blank to use the default WordPress upload location.', 'wp-filepond' )
		);
	}

	/**
	 * Callback function to render the "Enable Media Library" checkbox in the settings page.
	 *
	 * This function retrieves the stored option for adding uploaded files to the media library,
	 * ensures its validity, and outputs a checkbox input field with a description.
	 *
	 * @return void
	 */
	public function enable_media_library_callback(): void {
		// Retrieve the enable_media_library option from the database, defaulting to false.
		$enable = get_option( 'wp_filepond_enable_media_library', false );
		$enable = (bool) $enable; // Ensure it's strictly boolean

		// Description for the checkbox, with proper escaping for security.
		$description = sprintf(
			'<span class="help-text">%s</span>',
			esc_html__( 'Check if you want to add the uploaded file to media library.', 'wp-filepond' )
		);

		// Output the checkbox input field with proper escaping and checked attribute handling.
		printf(
			'<label><input type="checkbox" name="wp_filepond_enable_media_library" value="1" %s> %s</label>',
			checked( $enable, true, false ), // Ensure proper checkbox handling
			$description
		);
	}
}
