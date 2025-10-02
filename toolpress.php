<?php
/**
 * Plugin Name: ToolPress
 * Description: ToolPress lets you manage third-party tools into your WordPress site. Enable or disable integrations like Google Tag Manager, HubSpot, jQuery, Bootstrap, Font Awesome, Tawk.to chat, and more from a simple admin dashboard.
 * Author: Rakesh Lawaju
 * Author URI: https://racase.com.np
 * Plugin URI: https://racase.com.np/plugins/toolpress/
 * Version: 1.0.1
 * Text Domain: toolpress
 * Domain Path: /languages
 * Tested up to: 6.8
 * License: GPLv3
 * Requires PHP: 7.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * ToolPress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with ToolPress. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package ToolPress
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'TOOLPRESS_VERSION', '1.0.0' );
define( 'TOOLPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TOOLPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TOOLPRESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Register the admin menu page.
 *
 * @return void
 */
function toolpress_register_admin_menu() {

	$svg_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
	<path fill="currentColor" d="M1.5 24C1.5 11.574 11.574 1.5 24 1.5S46.5 11.574 46.5 24c0 10.493-7.182 19.308-16.899 21.797V39.69c0-1.59.974-2.987 2.287-3.884A13.98 13.98 0 0 0 38 24.24c0-5.09-2.717-9.547-6.78-11.997c-1.182-.712-2.553.242-2.553 1.622v8.223c0 .445-.147.878-.452 1.202c-.638.676-1.914 1.861-3.591 2.578c-.398.17-.849.17-1.247 0c-1.723-.736-3.022-1.967-3.64-2.632a1.7 1.7 0 0 1-.44-1.072c-.13-2.299-.172-5.234-.17-8.154c.002-1.401-1.397-2.367-2.582-1.62c-3.93 2.479-6.542 6.86-6.542 11.85c0 4.806 2.422 9.047 6.112 11.567c1.313.897 2.287 2.294 2.287 3.884v6.108C8.684 43.31 1.5 34.494 1.5 24" /></svg>';

	$base64 = 'data:image/svg+xml;base64,' . base64_encode( $svg_icon );

	add_menu_page(
		__( 'ToolPress', 'toolpress' ),
		__( 'ToolPress', 'toolpress' ),
		'manage_options',
		'toolpress',
		'toolpress_render_admin_page',
		$base64,
		100
	);
}
add_action( 'admin_menu', 'toolpress_register_admin_menu' );

/**
 * Render the admin page content.
 *
 * @return void
 */
function toolpress_render_admin_page() {
	$handle = 'toolpress-admin-dashboard';
	// Enqueue the registered script and styles.
	wp_enqueue_script( $handle );
	wp_enqueue_style( $handle . '-styles' );
	?>
	<div id="toolpress-app"></div>
	<?php
}

/**
 * Enqueue admin assets for the ToolPress admin page.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function toolpress_enqueue_admin_assets( $hook ) {
	// Load assets only on the ToolPress admin page.
	if ( 'toplevel_page_toolpress' !== $hook ) {
		return;
	}

	$handle     = 'toolpress-admin-dashboard';
	$asset_file = TOOLPRESS_PLUGIN_DIR . 'build/admin/dashboard/index.asset.php';
	if ( file_exists( $asset_file ) ) {
		$assets = require $asset_file;

		// Enqueue the bundled JavaScript file.
		wp_register_script(
			$handle,
			TOOLPRESS_PLUGIN_URL . 'build/admin/dashboard/index.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		// Localize script for translations and REST API root URL.
		wp_localize_script(
			$handle,
			'toolpressData',
			array(
				'restUrl' => esc_url_raw( rest_url() ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'tools'   => toolspress_get_tools_properties(),
			)
		);

		// Tell WordPress where to find the JSON translations.
		wp_set_script_translations( $handle, 'toolpress', plugin_dir_path( __FILE__ ) . 'languages' );

		// Enqueue admin styles if needed.
		wp_register_style(
			$handle . '-styles',
			TOOLPRESS_PLUGIN_URL . 'build/admin/dashboard/style-index.css',
			array( 'wp-components' ),
			$assets['version']
		);
	}
}
add_action( 'admin_enqueue_scripts', 'toolpress_enqueue_admin_assets' );

/**
 * Register settings for ToolPress tools.
 *
 * @return void
 */
function toolpress_register_settings() {
	register_setting(
		'general',
		'toolpress_tools_settings',
		array(
			'type'         => 'object',
			'description'  => 'ToolPress Tools Settings',
			'default'      => '',
			'show_in_rest' => array(
				'schema' => array(
					'type'       => 'object',
					'properties' => toolspress_get_tools_properties(),
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'toolpress_register_settings' );

/**
 * Get the properties for each tool managed by ToolPress.
 *
 * @return array The properties of the tools.
 */
function toolspress_get_tools_properties() {
	return array(
		'google-tag-manager' => array(
			'description' => __( 'Google Tag Manager', 'toolpress' ),
			'label'       => __( 'Google Tag Manager', 'toolpress' ),
			'type'        => 'object',
			'default'     => array(
				'enabled' => false,
				'id'      => '',
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
				'id'      => array(
					'type'              => 'string',
					'sanitize_callback' => 'rest_sanitize_string',
					'default'           => '',
				),
			),
		),
		'google-tag'         => array(
			'type'        => 'object',
			'label'       => __( 'Google Tag', 'toolpress' ),
			'description' => __( 'Google Tag', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
				'id'      => '',
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
				'id'      => array(
					'type'              => 'string',
					'sanitize_callback' => 'rest_sanitize_string',
					'default'           => '',
				),
			),
		),
		'hubspot-tracking'   => array(
			'type'        => 'object',
			'label'       => __( 'HubSpot Tracking', 'toolpress' ),
			'description' => __( 'HubSpot Tracking Code', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
				'id'      => '',
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
				'id'      => array(
					'type'              => 'string',
					'sanitize_callback' => 'rest_sanitize_string',
					'default'           => '',
				),
			),
		),
		'tawk-to-chat'       => array(
			'type'        => 'object',
			'label'       => __( 'Tawk.to Chat', 'toolpress' ),
			'description' => __( 'Tawk.to Live Chat', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
			),
			'properties'  => array(
				'enabled'     => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
				'property_id' => array(
					'type'              => 'string',
					'sanitize_callback' => 'rest_sanitize_string',
					'default'           => '',
				),
				'widget_id'   => array(
					'type'              => 'string',
					'sanitize_callback' => 'rest_sanitize_string',
					'default'           => '',
				),
			),
		),
		'jquery'             => array(
			'type'        => 'object',
			'label'       => __( 'jQuery', 'toolpress' ),
			'description' => __( 'jQuery Library', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
			),
		),
		'twbs'               => array(
			'type'        => 'object',
			'label'       => __( 'Bootstrap', 'toolpress' ),
			'description' => __( 'Load Bootstrap Library', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
			),
		),
		'twbs-icons'         => array(
			'type'        => 'object',
			'label'       => __( 'Bootstrap Icons', 'toolpress' ),
			'description' => __( 'Load Bootstrap Icons Library', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
			),
		),
		'font-awesome'       => array(
			'type'        => 'object',
			'label'       => __( 'Font Awesome', 'toolpress' ),
			'description' => __( 'Load Font Awesome Library', 'toolpress' ),
			'default'     => array(
				'enabled' => false,
			),
			'properties'  => array(
				'enabled' => array(
					'type'              => 'boolean',
					'sanitize_callback' => 'rest_sanitize_boolean',
					'default'           => false,
				),
			),
		),
	);
}

// Include and initialize the Run_Tags class.
require_once TOOLPRESS_PLUGIN_DIR . 'includes/class-run-tags.php';
