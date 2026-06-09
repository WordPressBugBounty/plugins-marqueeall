<?php
/**
 * Main Plugin Class
 *
 * @package MarqueeAll
 * @since   1.0.0
 */

namespace MASSCIE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 */
final class Plugin {

	/**
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
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
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_elementor_editor_assets' ) );

		if ( is_admin() ) {
			// Load admin + widget manager only in dashboard.
			$this->load_admin();

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
			add_action( 'admin_footer-plugins.php', array( $this, 'render_deactivation_modal' ) );
			add_action( 'wp_ajax_masscie_deactivation_feedback', array( $this, 'handle_deactivation_feedback' ) );
		}
	}

	/**
	 * Load admin-only files (Widget Manager page + AJAX).
	 * Wrapped in a try/catch so a missing file never breaks the front end.
	 *
	 * @return void
	 */
	private function load_admin() {
		$registry_file = MASSCIE_PATH . 'includes/class-widget-registry.php';
		$manager_file  = MASSCIE_PATH . 'includes/class-widget-manager.php';
		$admin_file    = MASSCIE_PATH . 'includes/admin/class-admin.php';

		if ( file_exists( $registry_file ) ) {
			require_once $registry_file;
		}
		if ( file_exists( $manager_file ) ) {
			require_once $manager_file;
		}
		if ( file_exists( $admin_file ) && class_exists( 'MASSCIE\Widget_Registry' ) ) {
			require_once $admin_file;
			Admin\Admin::instance();
		}
	}

	/**
	 * Fired on plugins_loaded.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
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

		// Register MarqueeAll widget category.
		add_action(
			'elementor/elements/categories_registered',
			function ( $manager ) {
				$manager->add_category(
					'masscie-widgets',
					array(
						'title' => __( 'MarqueeAll Widgets', 'marqueeall' ),
						'icon'  => 'fa fa-arrows-h',
					)
				);
			}
		);

		// Register widgets with Elementor.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Widget map — mirrors the original plugin.php manual list.
	 * Each entry: file path relative to MASSCIE_PATH, fully-qualified class name.
	 *
	 * @return array[]
	 */
	private function get_widget_map() {
		return array(
			'text-marquee'         => array(
				'file'  => 'includes/widgets/class-text-marquee.php',
				'class' => 'MASSCIE\Widgets\Text_Marquee',
			),
			'image-marquee'        => array(
				'file'  => 'includes/widgets/class-image-marquee.php',
				'class' => 'MASSCIE\Widgets\Image_Marquee',
			),
			'testimonial-marquee'  => array(
				'file'  => 'includes/widgets/class-testimonial-marquee.php',
				'class' => 'MASSCIE\Widgets\Testimonial_Marquee',
			),
			'news-ticker'          => array(
				'file'  => 'includes/widgets/class-news-ticker.php',
				'class' => 'MASSCIE\Widgets\News_Ticker',
			),
			'post-grid-marquee'    => array(
				'file'  => 'includes/widgets/class-post-grid-marquee.php',
				'class' => 'MASSCIE\Widgets\Post_Grid_Marquee',
			),
			'team-members-marquee' => array(
				'file'  => 'includes/widgets/class-team-members-marquee.php',
				'class' => 'MASSCIE\Widgets\Team_Members_Marquee',
			),
			'text-scramble'        => array(
				'file'  => 'includes/widgets/class-text-scramble.php',
				'class' => 'MASSCIE\Widgets\Text_Scramble',
			),
			'crypto-marquee'       => array(
				'file'  => 'includes/widgets/class-crypto-marquee.php',
				'class' => 'MASSCIE\Widgets\Crypto_Marquee',
			),
		);
	}

	/**
	 * Register Elementor widgets.
	 *
	 * Reads the saved option directly — zero dependency on Widget_Registry
	 * or Widget_Manager — so widgets always load even if those files are
	 * missing, mis-uploaded, or cached.
	 *
	 * Logic:
	 *   - Option missing / empty array  → ALL widgets enabled (fresh install default)
	 *   - Option present, slug = 0      → widget SKIPPED
	 *   - Option present, slug = 1      → widget LOADED
	 *   - Option present, slug missing  → widget LOADED (new widget added later)
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widget manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		// Read saved statuses. Returns false if option doesn't exist yet.
		$status = get_option( 'marqueeall_widget_status', false );

		foreach ( $this->get_widget_map() as $slug => $widget ) {
			// Only skip when we have a saved option AND it explicitly sets this slug to 0.
			if ( false !== $status && is_array( $status ) && isset( $status[ $slug ] ) && 0 === (int) $status[ $slug ] ) {
				continue;
			}

			$file = MASSCIE_PATH . $widget['file'];

			if ( ! file_exists( $file ) ) {
				continue;
			}

			require_once $file;

			$class = $widget['class'];

			if ( ! class_exists( $class ) ) {
				continue;
			}

			$widgets_manager->register( new $class() );
		}
	}

	/**
	 * Register front-end assets (scripts/styles registered; widgets enqueue on demand).
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		wp_register_style(
			'masscie-style',
			MASSCIE_URL . 'assets/css/masscie.css',
			array(),
			MASSCIE_VERSION
		);

		wp_register_style(
			'masscie-crypto-style',
			MASSCIE_URL . 'assets/css/masscie-crypto.css',
			array(),
			MASSCIE_VERSION
		);

		wp_register_script(
			'masscie-marquee',
			MASSCIE_URL . 'assets/js/masscie-marquee.js',
			array( 'jquery' ),
			MASSCIE_VERSION,
			true
		);
	}

	/**
	 * Enqueue plugins.php admin assets (deactivation feedback).
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( 'plugins.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'masscie-admin-feedback',
			MASSCIE_URL . 'assets/css/masscie-admin-feedback.css',
			array(),
			MASSCIE_VERSION
		);

		wp_enqueue_script(
			'masscie-admin-feedback',
			MASSCIE_URL . 'assets/js/masscie-admin-feedback.js',
			array( 'jquery' ),
			MASSCIE_VERSION,
			true
		);

		wp_localize_script(
			'masscie-admin-feedback',
			'MASSCIE_FEEDBACK',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'masscie_deactivation_feedback' ),
				'plugin_slug'    => 'marqueeall',
				'deactivateText' => __( 'Deactivate', 'marqueeall' ),
			)
		);
	}

	/**
	 * Enqueue Elementor editor assets.
	 *
	 * @return void
	 */
	public function enqueue_elementor_editor_assets() {
		wp_enqueue_style(
			'marqueeall-elementor-editor',
			MASSCIE_URL . 'assets/css/elementor-editor.css',
			array(),
			MASSCIE_VERSION
		);

		wp_enqueue_script(
			'masscie-crypto-editor',
			MASSCIE_URL . 'assets/js/crypto-editor.js',
			array( 'jquery', 'select2' ),
			MASSCIE_VERSION,
			true
		);

		wp_localize_script(
			'masscie-crypto-editor',
			'masscieCryptoEditor',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'masscie_crypto_nonce' ),
				'i18n'     => array(
					'fetching'   => __( 'Fetching...', 'marqueeall' ),
					'error'      => __( 'Error fetching data. Please try again.', 'marqueeall' ),
					'no_cryptos' => __( 'No cryptocurrencies found.', 'marqueeall' ),
				),
			)
		);

		if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_style( 'select2' );
		}
	}

	/**
	 * Render deactivation feedback modal on plugins.php.
	 *
	 * @return void
	 */
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
					<label><input type="radio" name="reason" value="missing_feature"> <?php esc_html_e( "The plugin is great, but I need a specific feature that you don't support", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="temporary"> <?php esc_html_e( "It's a temporary deactivation - I'm troubleshooting an issue", 'marqueeall' ); ?></label>
					<label><input type="radio" name="reason" value="other"> <?php esc_html_e( 'Other', 'marqueeall' ); ?></label>
					<textarea name="details" placeholder="<?php esc_attr_e( 'Please share more details (optional)', 'marqueeall' ); ?>"></textarea>
					<div class="masscie-feedback-buttons">
						<button type="button" class="button button-secondary" id="masscie-feedback-skip"><?php esc_html_e( 'Skip & Deactivate', 'marqueeall' ); ?></button>
						<button type="submit" class="button button-primary" id="masscie-feedback-submit"><?php esc_html_e( 'Submit & Deactivate', 'marqueeall' ); ?></button>
						<button type="button" class="button" id="masscie-feedback-cancel"><?php esc_html_e( 'Cancel', 'marqueeall' ); ?></button>
					</div>
					<input type="hidden" name="deactivate_url" id="masscie-deactivate-url" value="">
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * AJAX — handle deactivation feedback submission.
	 *
	 * @return void
	 */
	public function handle_deactivation_feedback() {
		check_ajax_referer( 'masscie_deactivation_feedback', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied', 'marqueeall' ) ) );
		}

		$reason         = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '';
		$details        = isset( $_POST['details'] ) ? wp_kses_post( wp_unslash( $_POST['details'] ) ) : '';
		$deactivate_url = isset( $_POST['deactivate_url'] ) ? esc_url_raw( wp_unslash( $_POST['deactivate_url'] ) ) : '';
		$site_url       = home_url();

		wp_mail(
			'amandeepsingh@webspero.com',
			sprintf( 'MarqueeAll Deactivation Feedback from %s', $site_url ),
			"Plugin: MarqueeAll\nSite: {$site_url}\nReason: {$reason}\n\nDetails:\n{$details}\n"
		);

		wp_send_json_success( array( 'deactivate_url' => $deactivate_url ) );
	}
}

Plugin::instance();
