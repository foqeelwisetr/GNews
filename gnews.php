<?php

/**
 * Plugin Name: G News
 * Plugin URI: https://exampl.com/
 * Author URI: https://example.com/
 * Description: G News For Diligent Technology
 * Version: 1.0.0
 * Author: Diligent Technology
 * Licence: GPLv2
 * Text Domain:g_new
 *
 * @package Gnews
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( ' G_News' ) ) {
	class G_News {
		public function __construct() {
			$this->g_news_global_constants_vars();
			// for language Translation
			add_action( 'wp_loaded', array( $this, 'g_news_init' ) );
			add_action( 'register_activation_hook', array( $this, 'g_news_init' ) );
			//setting
			add_action( 'plugin_action_links_' . G_News_BASENAME, array( $this, 'plugin_settings_link' ) );

			if ( is_admin() ) {
				include_once( G_News_DIR . 'admin/admin.php' );
				include_once( G_News_DIR . 'includes/custom-post-type.php' );
				include_once( G_News_DIR . 'includes/api-handler.php' );
			}

		}

		public function g_news_global_constants_vars() {

			// Plugin URL.
			if ( ! defined( 'G_News_URL' ) ) {
				define( 'G_News_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Base.
			if ( ! defined( 'G_News_BASENAME' ) ) {
				define( 'G_News_BASENAME', plugin_basename( __FILE__ ) );
			}

			// Plugin DIR.
			if ( ! defined( 'G_News_DIR' ) ) {
				define( 'G_News_DIR', plugin_dir_path( __FILE__ ) );
			}
		}

		public function g_news_init() {
			if ( function_exists( 'load_plugin_textdomain' ) ) {
				load_plugin_textdomain( 'g_new', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			}
		}

		public function wpgnews_activate_plugin() {
			WPGNews_Custom_Post_Type::register_post_type();
			WPGNews_Custom_Post_Type::register_taxonomies();
			flush_rewrite_rules();

		}

		public function plugin_settings_link( $links ) {
			$plugin_settings_link = '<a href="edit.php?post_type=news">Settings</a>';
			array_unshift( $links, $plugin_settings_link );

			return $links;
		}

	}

	new G_News();
}
