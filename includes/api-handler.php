<?php
if (! class_exists('API_Handler')){
	class API_Handler {
		public  function __construct(){
			// Handle form submission
			add_action( 'admin_post_wpgnews_scrape_news', array( $this, 'scrape_news' ) );
		}
		public function scrape_news() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$api_key  = get_option( 'wpgnews_api_key' );
			$response = wp_remote_get( "https://gnews.io/api/v4/top-headlines?token={$api_key}&lang=en" );

			if ( is_wp_error( $response ) ) {
				wp_die( 'Failed to retrieve data from GNews API.' );
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( ! isset( $data['articles'] ) ) {
				wp_die( 'Invalid response from GNews API.' );
			}

			foreach ( $data['articles'] as $article ) {
				$post_id = wp_insert_post( array(
					'post_title'   => $article['title'],
					'post_content' => $article['content'],
					'post_status'  => 'publish',
					'post_type'    => 'news',
				) );

				if ( $post_id ) {
					update_post_meta( $post_id, 'source', $article['source']['name'] );
					update_post_meta( $post_id, 'published_at', $article['publishedAt'] );

					// Set the post thumbnail if the image exists
					if ( ! empty( $article['image'] ) ) {
						$image_id = $this->upload_image_from_url( $article['image'] );
						if ( $image_id ) {
							set_post_thumbnail( $post_id, $image_id );
						}
					}

					// Set post terms
					wp_set_post_terms( $post_id, array( $article['source']['name'] ), 'news_category', true );
				}
			}

			wp_redirect( admin_url( 'admin.php?page=wpgnews-settings&scrape=success' ) );
			exit;
		}

// Helper function to upload image from URL
		private function upload_image_from_url( $image_url ) {
			$image = media_sideload_image( $image_url, 0, null, 'id' );
			if ( is_wp_error( $image ) ) {
				return false;
			}

			return $image;
		}

	}
	new API_Handler();

}
