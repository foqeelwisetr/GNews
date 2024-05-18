<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( ' G_News_Admin' ) ) {
	class G_News_Admin {
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );


		}

		public function add_settings_page() {
			add_menu_page( 'GNews Settings', 'GNews Settings', 'manage_options', 'wpgnews-settings', array( $this, 'settings_page_html' ), 'dashicons-admin-generic' );
		}

		public function register_settings() {
			register_setting( 'wpgnews_settings', 'wpgnews_api_key' );
		}

		public function settings_page_html() {
			?>
            <div class="wrap">
                <h1>GNews API Settings</h1>
                <form method="post" action="options.php">
					<?php
					settings_fields( 'wpgnews_settings' );
					do_settings_sections( 'wpgnews_settings' );
					?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API Key</th>
                            <td><input type="text" name="wpgnews_api_key" value="<?php echo esc_attr( get_option( 'wpgnews_api_key' ) ); ?>" class="regular-text"/></td>
                        </tr>
                    </table>
					<?php submit_button(); ?>
                </form>
                <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                    <input type="hidden" name="action" value="wpgnews_scrape_news">
					<?php submit_button( 'Scrape News' ); ?>
                </form>
				<?php if ( isset( $_GET['scrape'] ) && $_GET['scrape'] === 'success' ) : ?>
                    <div class="notice notice-success is-dismissible">
                        <p>News articles scraped and saved successfully!</p>
                    </div>
				<?php endif; ?>
            </div>
			<?php
		}

	}

	new G_News_Admin();

}