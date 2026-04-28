<?php
/**
 * Main Plugin Class
 *
 * @package MarqueeAll
 */

namespace MASSCIE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Plugin {

	/**
	 * Instance
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
		private function __construct() {
			add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_elementor_editor_assets' ] );

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
				add_action( 'admin_footer-plugins.php', [ $this, 'render_deactivation_modal' ] );
				add_action( 'wp_ajax_masscie_deactivation_feedback', [ $this, 'handle_deactivation_feedback' ] );
			}
		}

	/**
	 * Plugins loaded.
	 */
	public function on_plugins_loaded() {
		// Ensure Elementor is active.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action(
				'admin_notices',
				function () {
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__( 'MarqueeAll requires Elementor to be installed and activated.', 'marqueeall' )
					);
				}
			);
			return;
		}

		// Register Elementor category.
		add_action(
			'elementor/elements/categories_registered',
			function ( $manager ) {
				$manager->add_category(
					'masscie-widgets',
					[
						'title' => __( 'MarqueeAll Widgets', 'marqueeall' ),
						'icon'  => 'fa fa-arrows-h',
					]
				);
			}
		);

		// Register widgets.
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	/**
	 * Enqueue assets.
	 */
	public function enqueue_assets() {
		wp_register_style(
			'masscie-style',
			MASSCIE_URL . 'assets/css/masscie.css',
			[],
			MASSCIE_VERSION
		);

		wp_register_style(
			'masscie-crypto-style',
			MASSCIE_URL . 'assets/css/masscie-crypto.css',
			[],
			MASSCIE_VERSION
		);

		wp_register_script(
			'masscie-marquee',
			MASSCIE_URL . 'assets/js/masscie-marquee.js',
			[ 'jquery' ],
			MASSCIE_VERSION,
			true
		);
	}

	/**
	 * Register widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
		public function register_widgets( $widgets_manager ) {
			// Include widget files
			require_once MASSCIE_PATH . 'includes/widgets/class-text-marquee.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-image-marquee.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-testimonial-marquee.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-news-ticker.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-post-grid-marquee.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-team-members-marquee.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-text-scramble.php';
			require_once MASSCIE_PATH . 'includes/widgets/class-crypto-marquee.php';

			// Register them with Elementor
			$widgets_manager->register( new \MASSCIE\Widgets\Text_Marquee() );
			$widgets_manager->register( new \MASSCIE\Widgets\Image_Marquee() );
			$widgets_manager->register( new \MASSCIE\Widgets\Testimonial_Marquee() );
			$widgets_manager->register( new \MASSCIE\Widgets\News_Ticker() );
			$widgets_manager->register( new \MASSCIE\Widgets\Post_Grid_Marquee() );
			$widgets_manager->register( new \MASSCIE\Widgets\Team_Members_Marquee() );
			$widgets_manager->register( new \MASSCIE\Widgets\Text_Scramble() );
			$widgets_manager->register( new \MASSCIE\Widgets\Crypto_Marquee() );
		}

		public function enqueue_admin_assets( $hook_suffix ) {
		if ( 'plugins.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'masscie-admin-feedback',
			MASSCIE_URL . 'assets/css/masscie-admin-feedback.css',
			[],
			MASSCIE_VERSION
		);

		wp_enqueue_script(
			'masscie-admin-feedback',
			MASSCIE_URL . 'assets/js/masscie-admin-feedback.js',
			[ 'jquery' ],
			MASSCIE_VERSION,
			true
		);

		wp_localize_script(
			'masscie-admin-feedback',
			'MASSCIE_FEEDBACK',
			[
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'masscie_deactivation_feedback' ),
				'plugin_slug'   => 'marqueeall', // folder name
				'deactivateText'=> __( 'Deactivate', 'marqueeall' ),
			]
		);
	}
	/**
		 * Enqueue Elementor editor assets.
		 */
		public function enqueue_elementor_editor_assets() {
		// Enqueue editor styles
		wp_enqueue_style(
			'marqueeall-elementor-editor',
			MASSCIE_URL . 'assets/css/elementor-editor.css',
			[],
			MASSCIE_VERSION
		);

		// Enqueue crypto editor script
		wp_enqueue_script(
			'masscie-crypto-editor',
			MASSCIE_URL . '/assets/js/crypto-editor.js',
			[ 'jquery', 'select2' ],
			MASSCIE_VERSION,
			true
		);

		// Localize script with necessary data
		wp_localize_script(
			'masscie-crypto-editor',
			'masscieCryptoEditor',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'masscie_crypto_nonce' ),
				'i18n'     => [
					'fetching'   => __( 'Fetching...', 'marqueeall' ),
					'error'      => __( 'Error fetching data. Please try again.', 'marqueeall' ),
					'no_cryptos' => __( 'No cryptocurrencies found.', 'marqueeall' ),
				],
			]
		);

		// Enqueue Select2 if not already loaded by Elementor
		if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
		}
	}

	public function render_deactivation_modal() {
		?>
		<div id="masscie-feedback-modal" style="display:none;">
			<div class="masscie-feedback-backdrop"></div>
			<div class="masscie-feedback-dialog">
				<h2><?php esc_html_e( 'Quick Feedback', 'marqueeall' ); ?></h2>
				<p><?php esc_html_e( 'If you have a moment, please let us know why you are deactivating:', 'marqueeall' ); ?></p>

				<form id="masscie-feedback-form">
					<label><input type="radio" name="reason" value="not_what_i_was_looking_for"> <?php esc_html_e( "It's not what I was looking for", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="not_working"> <?php esc_html_e( 'The plugin is not working', 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="found_better"> <?php esc_html_e( 'I found a better plugin', 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="didnt_work_as_expected"> <?php esc_html_e( "The plugin didn't work as expected", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="couldnt_understand"> <?php esc_html_e( "I couldn't understand how to make it work", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="missing_feature"> <?php esc_html_e( "The plugin is great, but I need specific feature that you don't support", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="temporary"> <?php esc_html_e( "It's a temporary deactivation - I'm troubleshooting an issue", 'marqueeall' ); ?></label>
					<label>
						<input type="radio" name="reason" value="other">
						<?php esc_html_e( 'Other', 'marqueeall' ); ?>
					</label>
					<textarea name="details" placeholder="<?php esc_attr_e( 'Please share more details (optional)', 'marqueeall' ); ?>"></textarea>

					<div class="masscie-feedback-buttons">
						<button type="button" class="button button-secondary" id="masscie-feedback-skip">
							<?php esc_html_e( 'Skip & Deactivate', 'marqueeall' ); ?>
						</button>
						<button type="submit" class="button button-primary" id="masscie-feedback-submit">
							<?php esc_html_e( 'Submit & Deactivate', 'marqueeall' ); ?>
						</button>
						<button type="button" class="button" id="masscie-feedback-cancel">
							<?php esc_html_e( 'Cancel', 'marqueeall' ); ?>
						</button>
					</div>

					<input type="hidden" name="deactivate_url" id="masscie-deactivate-url" value="">
				</form>
			</div>
		</div>
		<?php
	}

	public function handle_deactivation_feedback() {
		check_ajax_referer( 'masscie_deactivation_feedback', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied', 'marqueeall' ) ] );
		}

		$reason        = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';
		$details       = isset( $_POST['details'] ) ? wp_kses_post( wp_unslash( $_POST['details'] ) ) : '';
		$deactivate_url= isset( $_POST['deactivate_url'] ) ? esc_url_raw( wp_unslash( $_POST['deactivate_url'] ) ) : '';

		$site_url      = home_url();

		$subject = sprintf( 'MarqueeAll Deactivation Feedback from %s', $site_url );

		$body  = "Plugin: MarqueeAll\n";
		$body .= "Site: $site_url\n";
		$body .= "Reason: $reason\n\n";
		$body .= "Details:\n$details\n";

		wp_mail(
			'amandeepsingh@webspero.com',
			$subject,
			$body
		);

		wp_send_json_success(
			[
				'deactivate_url' => $deactivate_url,
			]
		);
	}
}

Plugin::instance();
