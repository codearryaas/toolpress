<?php
/**
 * Font Awesome file management for ToolPress.
 *
 * @package ToolPress
 * @subpackage Includes
 * @since 1.0.0
 */

/**
 * Download Font Awesome files to WordPress uploads directory.
 *
 * Downloads Font Awesome CSS, JS, and font files from CDN and stores them
 * in the WordPress uploads directory for local usage. Also updates CSS
 * to use local font files.
 *
 * @since 1.0.0
 * @return array Array of downloaded file URLs.
 */
function toolpress_download_font_awesome() {
	// URLs of Font Awesome files to download
	$files_to_download = array(
		'css'      => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/css/all.min.css',
		'js'       => 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/js/all.min.js',
		'webfonts' => array(
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/webfonts/fa-brands-400.woff2',
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/webfonts/fa-regular-400.woff2',
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/webfonts/fa-solid-900.woff2',
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/webfonts/fa-v4compatibility.woff2',
		),
	);

	// Get WordPress uploads directory
	$upload_dir       = wp_upload_dir();
	$font_awesome_dir = $upload_dir['basedir'] . '/toolpress/font-awesome';
	$font_awesome_url = $upload_dir['baseurl'] . '/toolpress/font-awesome';

	// Initialize WordPress filesystem
	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	WP_Filesystem();
	global $wp_filesystem;

	// Create directories if they don't exist
	wp_mkdir_p( $font_awesome_dir );
	wp_mkdir_p( $font_awesome_dir . '/webfonts' );

	// Function to download and save file
	function download_and_save_file( $url, $destination, $wp_filesystem ) {
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$content = wp_remote_retrieve_body( $response );
		return $wp_filesystem->put_contents( $destination, $content, FS_CHMOD_FILE );
	}

	$downloaded_files = array();

	// Download CSS
	$css_file = $font_awesome_dir . '/all.min.css';
	if ( download_and_save_file( $files_to_download['css'], $css_file, $wp_filesystem ) ) {
		$downloaded_files['css'] = $font_awesome_url . '/all.min.css';

		// Update CSS file to point to local webfonts
		$css_content = $wp_filesystem->get_contents( $css_file );
		$css_content = str_replace(
			'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@7.1.0/webfonts/',
			$font_awesome_url . '/webfonts/',
			$css_content
		);
		$wp_filesystem->put_contents( $css_file, $css_content );
	}

	// Download JS
	$js_file = $font_awesome_dir . '/all.min.js';
	if ( download_and_save_file( $files_to_download['js'], $js_file, $wp_filesystem ) ) {
		$downloaded_files['js'] = $font_awesome_url . '/all.min.js';
	}

	// Download webfonts
	foreach ( $files_to_download['webfonts'] as $font_url ) {
		$filename  = basename( $font_url );
		$font_file = $font_awesome_dir . '/webfonts/' . $filename;
		if ( download_and_save_file( $font_url, $font_file, $wp_filesystem ) ) {
			$downloaded_files['webfonts'][] = $font_awesome_url . '/webfonts/' . $filename;
		}
	}

	// Store the URLs in WordPress options
	update_option( 'toolpress_font_awesome_files', $downloaded_files );

	return $downloaded_files;
}

/**
 * Register and enqueue downloaded Font Awesome files
 */
function toolpress_enqueue_font_awesome() {
	$files = get_option( 'toolpress_font_awesome_files', array() );

	if ( ! empty( $files['css'] ) ) {
		wp_enqueue_style(
			'toolpress-font-awesome',
			$files['css'],
			array(),
			'6.4.2'
		);
	}

	if ( ! empty( $files['js'] ) ) {
		wp_enqueue_script(
			'toolpress-font-awesome',
			$files['js'],
			array(),
			'6.4.2',
			true
		);
	}
}

/**
 * Check if Font Awesome files exist in uploads directory
 */
function toolpress_check_font_awesome_files() {
	$upload_dir       = wp_upload_dir();
	$font_awesome_dir = $upload_dir['basedir'] . '/toolpress/font-awesome';

	if ( ! file_exists( $font_awesome_dir . '/all.min.css' ) ||
		! file_exists( $font_awesome_dir . '/all.min.js' ) ) {
		return false;
	}

	return true;
}

/**
 * Initialize Font Awesome download and setup
 */
function toolpress_init_font_awesome() {
	if ( ! toolpress_check_font_awesome_files() ) {
		$files = toolpress_download_font_awesome();
		if ( ! empty( $files ) ) {
			// Add success notice in admin
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-success is-dismissible">';
					echo '<p>Font Awesome files have been downloaded successfully.</p>';
					echo '</div>';
				}
			);
		} else {
			// Add error notice in admin
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error is-dismissible">';
					echo '<p>Error downloading Font Awesome files. Please try again.</p>';
					echo '</div>';
				}
			);
		}
	}
}

// Hook into WordPress
add_action( 'admin_init', 'toolpress_init_font_awesome' );
add_action( 'wp_enqueue_scripts', 'toolpress_enqueue_font_awesome' );
