<?php
if ( ! class_exists( 'WPGNews_Custom_Post_Type' ) ) {

	class WPGNews_Custom_Post_Type {

		public function __construct() {
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'init', array( $this, 'register_taxonomies' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
		}

		public function register_post_type() {
			register_post_type( 'news', array(
				'labels'      => array(
					'name'          => __( 'News' ),
					'singular_name' => __( 'News' )
				),
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'news' ),
				'supports'    => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			) );
		}

		public function register_taxonomies() {
			register_taxonomy( 'news_category', 'news', array(
				'label'        => __( 'Categories' ),
				'rewrite'      => array( 'slug' => 'news-category' ),
				'hierarchical' => true,
			) );

			register_taxonomy( 'news_tag', 'news', array(
				'label'        => __( 'Tags' ),
				'rewrite'      => array( 'slug' => 'news-tag' ),
				'hierarchical' => false,
			) );
		}

		public function add_meta_boxes() {
			add_meta_box( 'wpgnews_meta_box', 'News Details', array( $this, 'meta_box_callback' ), 'news', 'normal', 'high' );
		}

		public function meta_box_callback( $post ) {
			// Add nonce for security
			wp_nonce_field( 'wpgnews_save_meta_box_data', 'wpgnews_meta_box_nonce' );

			$source       = get_post_meta( $post->ID, 'source', true );
			$author       = get_post_meta( $post->ID, 'author', true );
			$published_at = get_post_meta( $post->ID, 'published_at', true );

			echo '<label for="wpgnews_source">Source</label>';
			echo '<input type="text" id="wpgnews_source" name="wpgnews_source" value="' . esc_attr( $source ) . '" class="widefat">';
			echo '<label for="wpgnews_published_at">Published At</label>';
			echo '<input type="text" id="wpgnews_published_at" name="wpgnews_published_at" value="' . esc_attr( $published_at ) . '" class="widefat">';
		}

		public function save_meta_box_data( $post_id ) {
			// Check nonce for security
			if ( ! isset( $_POST['wpgnews_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wpgnews_meta_box_nonce'], 'wpgnews_save_meta_box_data' ) ) {
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $_POST['wpgnews_source'] ) ) {
				update_post_meta( $post_id, 'source', sanitize_text_field( $_POST['wpgnews_source'] ) );
			}

			if ( isset( $_POST['wpgnews_author'] ) ) {
				update_post_meta( $post_id, 'author', sanitize_text_field( $_POST['wpgnews_author'] ) );
			}

			if ( isset( $_POST['wpgnews_published_at'] ) ) {
				update_post_meta( $post_id, 'published_at', sanitize_text_field( $_POST['wpgnews_published_at'] ) );
			}
		}

	}

	new WPGNews_Custom_Post_Type();

}
?>
