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
		add_filter( 'wp_filepond_settings_fields', array( $this, 'get_settings_fields' ) );
		add_filter( 'wp_filepond_settings_options', array( $this, 'get_settings_options' ) );
		add_filter( 'wp_filepond_settings_sections', array( $this, 'get_settings_sections' ) );
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


	public function get_settings_fields( $settings_fields ): array {
		$settings_fields = array_merge( $settings_fields, array(
			array(
				'id'       => 'wp_filepond_enable_preview',
				'title'    => __( 'Enable Preview', 'wp-filepond' ),
				'callback' => array( $this, 'enable_preview_callback' ),
				'section'  => 'wp_filepond_pro_section',
			),
			// array(
			// 	'id'       => 'wp_filepond_preview_height',
			// 	'title'    => __( 'Preview Height', 'wp-filepond' ),
			// 	'callback' => array( $this, 'preview_height_callback' ),
			// ),
			// array(
			// 	'id'       => 'wp_filepond_upload_location',
			// 	'title'    => __( 'Upload Location', 'wp-filepond' ),
			// 	'callback' => array( $this, 'upload_location_callback' ),
			// ),
			// array(
			// 	'id'       => 'wp_filepond_enable_media_library',
			// 	'title'    => __( 'Add file to media library', 'wp-filepond' ),
			// 	'callback' => array( $this, 'enable_media_library_callback' ),
			// )
		) );
		print_r($settings_fields);
		return $settings_fields;
	}

	/**
	 * Returns the settings options for the plugin.
	 *
	 * @return array The settings options.
	 */
	public function get_settings_options( $settings_options ): array {
		$settings_options = array_merge( $settings_options, array(
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_button_label',
				'sanitize'     => 'sanitize_text_field',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_file_types_allowed',
				'sanitize'     => 'sanitize_text_field',
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
				'option_name'  => 'wp_filepond_file_type_error',
				'sanitize'     => 'sanitize_text_field',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_file_size_error',
				'sanitize'     => 'sanitize_text_field',
			),
			array(
				'option_group' => 'wp_filepond_options_group',
				'option_name'  => 'wp_filepond_max_file_size',
				'sanitize'     => 'absint',
			),
		) );

		return $settings_options;
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
	 * Callback function to render the "File Size Error Message" textarea in the settings page.
	 *
	 * This function retrieves the stored error message for files exceeding the size limit,
	 * sanitizes it, and outputs a textarea input field for user customization.
	 *
	 * @return void
	 */
	public function file_size_error_message_callback(): void {
		// Retrieve the file size error message, defaulting to an empty string.
		$message = get_option( 'wp_filepond_file_size_error', '' );
		$message = sanitize_textarea_field( $message ); // Ensure safe text output

		// Output the textarea field with proper escaping.
		printf(
			'<textarea name="wp_filepond_file_size_error" rows="3" cols="50" maxlength="120">%s</textarea>',
			esc_textarea( $message ) // Escape output to prevent XSS
		);

		// Output the description with proper escaping.
		printf(
			'<p class="help-text">%s</p>',
			esc_html__( 'Enter an error message to show when an uploaded file exceeds the file size limit.', 'wp-filepond' )
		);
	}

	/**
	 * Callback function to render the "Button Label" input field in the settings page.
	 *
	 * This function retrieves the stored button label, ensures its validity,
	 * and outputs a text input field for user customization.
	 *
	 * @return void
	 */
	public function button_label_callback(): void {
		// Retrieve the button label option from the database, defaulting to an empty string.
		$button_label = get_option( 'wp_filepond_button_label', '' );
		$button_label = sanitize_text_field( $button_label ); // Ensure safe text output

		// Output the input field with proper escaping.
		printf(
			'<input type="text" name="wp_filepond_button_label" value="%s">',
			esc_attr( $button_label ) // Escape output to prevent XSS
		);
	}

	/**
	 * Callback function to render the file types allowed input field in the settings page.
	 *
	 * This function retrieves the allowed file types from the database, sanitizes the value,
	 * and outputs an input field for users to modify it. It also includes a description 
	 * to guide users on how to format the input.
	 *
	 * @return void
	 */
	public function file_types_allowed_callback(): void {
		// Retrieve the allowed file types option from the database, defaulting to an empty string.
		$file_types = get_option( 'wp_filepond_file_types_allowed', '' );
		$file_types = is_string( $file_types ) ? sanitize_text_field( $file_types ) : ''; // Ensure it's a clean string

		// Output the input field with proper escaping to prevent XSS.
		printf(
			'<input type="text" name="wp_filepond_file_types_allowed" value="%s">',
			esc_attr( $file_types ) // Escape output to prevent XSS
		);

		// Output the description with proper escaping for security.
		printf(
			'<p>%s</p>',
			esc_html__( 'Default allowed file types, separated by a comma (jpg, gif, pdf, etc). Can be overridden in the field settings.', 'wp-filepond' )
		);
	}

	/**
	 * Callback function to render the max file size setting field.
	 * 
	 * This function retrieves the max file size option from the database and 
	 * displays an input field along with a description. The value is sanitized
	 * and properly escaped for security.
	 */
	public function max_file_size_callback(): void {
		// Retrieve the max file size setting from the database, defaulting to 100 MB.
		$max_file_size = get_option( 'wp_filepond_max_file_size', 100 );
		$max_file_size = (int) $max_file_size; // Ensure it is strictly an integer.

		// Output a number input field with proper escaping and value handling.
		printf(
			'<input type="number" name="wp_filepond_max_file_size" value="%d" min="1" step="1">',
			esc_attr( $max_file_size ) // Escape for output safety.
		);

		// Display a help text for the input field.
		printf(
			'<p class="description">%s</p>',
			esc_html__( 'Default max. file size in MB. Can be overridden in the field settings.', 'wp-filepond' )
		);
	}
}
