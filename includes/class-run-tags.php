<?php
/**
 * Run Tags Class
 *
 * @package ToolPress
 */

namespace ToolPress;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Run Tags Class
 */
class Run_Tags {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_body_open', array( $this, 'add_body_open_scripts' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_script_attrs' ), 10, 2 );
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		$settings = get_option( 'toolpress_tools_settings' );
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $tool => $config ) {
				if ( isset( $config['enabled'] ) && $config['enabled'] ) {
					switch ( $tool ) {
						case 'jquery':
								wp_enqueue_script( 'jquery' );
							break;
						case 'font-awesome':
								// Enqueue Font Awesome from CDN.
								wp_enqueue_script( 'font-awesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js', array(), 'latest', true );
								wp_enqueue_style( 'font-awesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css', array(), 'latest' );
							break;
						case 'twbs-icons':
							// Enqueue Bootstrap Icons from CDN.
							wp_enqueue_style( 'twbs-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.min.css', array(), 'latest' );
							break;
						case 'twbs':
							// Enqueue Bootstrap CSS and JS from CDN.
							wp_enqueue_style( 'bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css', array(), '5.3.0' );
							wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js', array(), 'latest', false );
							break;

						case 'tawk-to-chat':
							$tawkto_chat_property_id = ! empty( $config['property_id'] ) ? $config['property_id'] : '';
							$tawkto_chat_widget_id   = ! empty( $config['widget_id'] ) ? $config['widget_id'] : '';
							if ( ! empty( $tawkto_chat_property_id ) && ! empty( $tawkto_chat_widget_id ) ) {
								// Tawk.to chat is handled in its own class.
								$script_content = "
                                    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
                                    (function(){
                                    var s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];
                                    s1.async=true;
                                    s1.src='https://embed.tawk.to/" . esc_attr( $tawkto_chat_property_id ) . '/' . esc_attr( $tawkto_chat_widget_id ) . "';
                                    s1.charset='UTF-8';
                                    s1.setAttribute('crossorigin','*');
                                    s0.parentNode.insertBefore(s1,s0);
                                    })();
                                    ";

								$handle = 'tawkto-chat-' . esc_attr( $tawkto_chat_property_id );
								wp_register_script( $handle, false, array(), $handle, false );
								wp_add_inline_script( $handle, $script_content, 'before' );

								wp_enqueue_script( $handle );
							}
							break;
						case 'google-tag-manager':
							if ( ! empty( $config['id'] ) ) {
								$gtm_id = trim( $config['id'] );
								if ( ! empty( $gtm_id ) ) {
									// Google Tag Manager is handled in its own class.
									$script_content = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','" . esc_attr( $gtm_id ) . "');";
									$handle         = 'google-tag-manager-' . esc_attr( $gtm_id );
									wp_register_script( $handle, false, array(), $handle, false );
									wp_add_inline_script( $handle, $script_content, 'before' );
									wp_enqueue_script( $handle );
								}
							}
							break;
						case 'google-tag':
							$google_tag_id = ! empty( $config['id'] ) ? trim( $config['id'] ) : '';
							if ( ! empty( $google_tag_id ) ) {
								// Google Tag is handled in its own class.
								$script_content = "
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
    gtag('config', '" . esc_attr( $google_tag_id ) . "');
";
								$handle         = 'google-tag-' . esc_attr( $google_tag_id );
								$url            = 'https://www.googletagmanager.com/gtag/js?id=' . esc_attr( $google_tag_id );
								wp_register_script(
									$handle,
									$url,
									array(),
									null,
									array(
										'strategy'  => 'async',
										'in_footer' => false,
									)
								);
								wp_enqueue_script( $handle );
								$handle2 = 'google-tag-manager-dl-' . esc_attr( $google_tag_id );
								wp_register_script( $handle2, false, array( $handle ), $handle2, false );
								wp_add_inline_script( $handle2, $script_content, 'after' );
								wp_enqueue_script( $handle2 );
							}
							break;
						case 'hubspot-tracking':
							// HubSpot Tracking is handled in its own class.
							$hubspot_id = ! empty( $config['id'] ) ? trim( $config['id'] ) : '';
							if ( ! empty( $hubspot_id ) ) {
								wp_enqueue_script(
									'hs-script-loader-' . esc_attr( $hubspot_id ),
									'//js.hs-scripts.com/' . esc_attr( $hubspot_id ) . '.js',
									array(),
									null,
									array(
										'strategy'  => 'async',
										'in_footer' => false,
									)
								);
							}
							break;
						default:
							break;
					}
				}
			}
		}
	}

	/**
	 * Add scripts to the head section.
	 */
	public function add_body_open_scripts() {
		$settings = get_option( 'toolpress_tools_settings' );
		if ( ! empty( $settings ) ) {
			foreach ( $settings as $tool => $config ) {
				if ( isset( $config['enabled'] ) && $config['enabled'] ) {
					switch ( $tool ) {
						case 'google-tag-manager':
							$gtm_id = ! empty( $config['id'] ) ? trim( $config['id'] ) : '';
							if ( ! empty( $gtm_id ) ) {
								echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $gtm_id ) . "\"
height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->\n";
							}
							break;
					}
				}
			}
		}
	}

	/**
	 * Add async and defer attributes to specific script tags.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Modified script tag.
	 */
	public function add_script_attrs( $tag, $handle ) {
		if ( strpos( $handle, 'hs-script-loader-' ) === 0 ) {
			// Add async and defer attributes.
			return str_replace( '<script ', "<!-- Generated by ToolPress -->\n<script async defer type=\"text/javascript\" ", $tag );
		}
		return $tag;
	}
}

// Initialize the Run_Tags class.
new Run_Tags();
