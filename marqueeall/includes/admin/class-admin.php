<?php
/**
 * Admin Class
 *
 * Registers the "MarqueeAll" top-level admin menu page (Widget Manager),
 * enqueues admin assets, and handles AJAX save requests.
 *
 * @package MarqueeAll
 * @since   1.3.0
 */

namespace MASSCIE\Admin;

use MASSCIE\Widget_Manager;
use MASSCIE\Widget_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 */
final class Admin {

	/**
	 * Admin page hook suffix returned by add_menu_page().
	 *
	 * @var string
	 */
	private $page_hook = '';

	/**
	 * Singleton instance.
	 *
	 * @var Admin|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — hooks into WordPress.
	 */
	private function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_head', [ $this, 'print_menu_icon_style' ] );
		add_action( 'wp_ajax_marqueeall_save_widget_status', [ $this, 'ajax_save_widget_status' ] );
	}
	/**
	 * Print inline CSS to size the GIF menu icon on every admin page.
	 *
	 * wp-admin forces width:auto and padding overrides on img menu icons
	 * so !important is required on each property.
	 *
	 * @return void
	 */
	public function print_menu_icon_style() {
		?>
		<style>
			#adminmenu .toplevel_page_marqueeall img {
				width: 20px !important;
				height: 20px !important;
				padding: 7px !important;
				opacity: .8 !important;
				border-radius: 0 !important;
			}

			#adminmenu .toplevel_page_marqueeall:hover img,
			#adminmenu .toplevel_page_marqueeall.current img,
			#adminmenu .toplevel_page_marqueeall.wp-has-current-submenu img {
				opacity: 1 !important;
			}
		</style>
		<?php
	}

	/**
	 * Register the top-level admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		$this->page_hook = add_menu_page(
			__( 'MarqueeAll — Widget Manager', 'marqueeall' ),
			__( 'MarqueeAll', 'marqueeall' ),
			'manage_options',
			'marqueeall',
			[ $this, 'render_page' ],
			'https://ps.w.org/marqueeall/assets/icon-128x128.gif',
			58
		);
	}

	/**
	 * Enqueue admin-page assets — only on our own page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( $hook_suffix !== $this->page_hook ) {
			return;
		}

		wp_enqueue_style(
			'marqueeall-admin',
			MASSCIE_URL . 'assets/css/marqueeall-admin.css',
			[],
			MASSCIE_VERSION
		);

		wp_enqueue_script(
			'marqueeall-admin',
			MASSCIE_URL . 'assets/js/marqueeall-admin.js',
			[ 'jquery' ],
			MASSCIE_VERSION,
			true
		);

		wp_localize_script(
			'marqueeall-admin',
			'MARQUEEALL_ADMIN',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'marqueeall_save_widget_status' ),
				'i18n'     => [
					'saving'       => __( 'Saving…', 'marqueeall' ),
					'saved'        => __( 'Settings saved!', 'marqueeall' ),
					'save_error'   => __( 'Could not save settings. Please try again.', 'marqueeall' ),
					/* translators: 1: Number of enabled widgets, 2: Total available widgets. */
					'enabled_count' => __( '%1$d of %2$d available widgets enabled', 'marqueeall' ),
				],
			]
		);
	}

	/**
	 * Render the Widget Manager admin page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'marqueeall' ) );
		}

		$registry       = Widget_Registry::instance()->get_all();
		$total          = Widget_Registry::instance()->count();
		$widget_manager = Widget_Manager::instance();
		$status         = $widget_manager->get_status();
		$enabled_count  = $widget_manager->get_enabled_count();
		?>
		<div class="wrap marqueeall-wrap">

			<!-- Header -->
			<div class="marqueeall-header">
				<div class="marqueeall-header__brand">
					<span class="marqueeall-header__logo">
						<img
							src="https://ps.w.org/marqueeall/assets/icon-128x128.gif"
							alt="<?php esc_attr_e( 'MarqueeAll', 'marqueeall' ); ?>"
							width="36"
							height="36"
						/>
					</span>
					<h1 class="marqueeall-header__title"><?php esc_html_e( 'Widgets Manager', 'marqueeall' ); ?></h1>
				</div>
				<div class="marqueeall-header__actions">
					<button id="marqueeall-save" class="button marqueeall-btn marqueeall-btn--primary">
						<?php esc_html_e( 'Save Settings', 'marqueeall' ); ?>
					</button>
				</div>
			</div>

			<!-- Tab bar -->
			<div class="marqueeall-tabs">
				<button class="marqueeall-tab marqueeall-tab--active" data-tab="widgets">
					<span class="dashicons dashicons-screenoptions"></span>
					<?php esc_html_e( 'Widgets', 'marqueeall' ); ?>
				</button>
			</div>

			<!-- Panel -->
			<div class="marqueeall-panel" id="marqueeall-tab-widgets">

				<!-- Toolbar -->
				<div class="marqueeall-toolbar">
					<div class="marqueeall-toolbar__left">
						<h2 class="marqueeall-section-heading"><?php esc_html_e( 'Manage Widgets', 'marqueeall' ); ?></h2>
						<p class="marqueeall-stats" id="marqueeall-stats">
							<?php
							printf(
								/* translators: 1: enabled count, 2: total count */
								esc_html__( '%1$d of %2$d available widgets enabled', 'marqueeall' ),
								(int) $enabled_count,
								(int) $total
							);
							?>
						</p>
					</div>
					<div class="marqueeall-toolbar__right">
						<div class="marqueeall-search-wrap">
							<span class="dashicons dashicons-search marqueeall-search-icon"></span>
							<input
								type="search"
								id="marqueeall-search"
								class="marqueeall-search"
								placeholder="<?php esc_attr_e( 'Search widgets…', 'marqueeall' ); ?>"
							/>
						</div>
						<button id="marqueeall-enable-all" class="button marqueeall-btn marqueeall-btn--outline">
							<?php esc_html_e( 'Enable All', 'marqueeall' ); ?>
						</button>
						<button id="marqueeall-disable-all" class="button marqueeall-btn marqueeall-btn--outline marqueeall-btn--danger">
							<?php esc_html_e( 'Disable All', 'marqueeall' ); ?>
						</button>
					</div>
				</div>

				<!-- General Widgets -->
				<div class="marqueeall-section">
					<h3 class="marqueeall-section__title"><?php esc_html_e( 'General Widgets', 'marqueeall' ); ?></h3>
					<div class="marqueeall-grid" id="marqueeall-grid">
						<?php foreach ( $registry as $widget ) : ?>
							<?php
							$slug    = $widget['slug'];
							$enabled = isset( $status[ $slug ] ) ? (bool) $status[ $slug ] : true;
							$pro     = ! empty( $widget['pro'] );
							?>
							<div
								class="marqueeall-card<?php echo $pro ? ' marqueeall-card--pro' : ''; ?><?php echo $enabled ? ' marqueeall-card--active' : ''; ?>"
								data-slug="<?php echo esc_attr( $slug ); ?>"
								data-title="<?php echo esc_attr( strtolower( $widget['title'] ) ); ?>"
							>
								<?php if ( $pro ) : ?>
									<span class="marqueeall-card__badge"><?php esc_html_e( 'Pro', 'marqueeall' ); ?></span>
								<?php endif; ?>

								<div class="marqueeall-card__body">
									<span class="marqueeall-card__icon eicon <?php echo esc_attr( $widget['icon'] ); ?>"></span>
									<span class="marqueeall-card__name"><?php echo esc_html( $widget['title'] ); ?></span>
								</div>

								<div class="marqueeall-card__footer">
									<label class="marqueeall-toggle">
										<input
											type="checkbox"
											class="marqueeall-toggle__input"
											name="widget_status[<?php echo esc_attr( $slug ); ?>]"
											value="1"
											<?php checked( $enabled ); ?>
											<?php disabled( $pro ); ?>
										/>
										<span class="marqueeall-toggle__slider"></span>
									</label>

									<div class="marqueeall-card__links" style="display:none;">
										<?php if ( ! empty( $widget['docs'] ) ) : ?>
											<a href="<?php echo esc_url( $widget['docs'] ); ?>" target="_blank" rel="noopener noreferrer" class="marqueeall-card__link">
												<?php esc_html_e( 'Doc', 'marqueeall' ); ?>
											</a>
										<?php endif; ?>
										<?php if ( ! empty( $widget['demo'] ) ) : ?>
											<a href="<?php echo esc_url( $widget['demo'] ); ?>" target="_blank" rel="noopener noreferrer" class="marqueeall-card__link">
												<?php esc_html_e( 'Demo', 'marqueeall' ); ?>
											</a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<!-- Empty search state -->
					<p class="marqueeall-no-results" id="marqueeall-no-results" style="display:none;">
						<?php esc_html_e( 'No widgets match your search.', 'marqueeall' ); ?>
					</p>
				</div>

				<!-- Leave a Review -->
				<div class="marqueeall-review-box">
					<div class="marqueeall-review-box__icon">⭐</div>
					<div class="marqueeall-review-box__content">
						<strong><?php esc_html_e( 'Enjoying MarqueeAll?', 'marqueeall' ); ?></strong>
						<p>
							<?php
							printf(
								/* translators: %s opening/closing anchor tags */
								esc_html__( 'Please %1$sleave us a 5-star review%2$s — it really helps!', 'marqueeall' ),
								'<a href="https://wordpress.org/support/plugin/marqueeall/reviews/#new-post" target="_blank" rel="noopener noreferrer">',
								'</a>'
							);
							?>
						</p>
					</div>
				</div>

			</div><!-- /.marqueeall-panel -->

			<!-- Toast notification -->
			<div id="marqueeall-toast" class="marqueeall-toast" role="status" aria-live="polite"></div>

		</div><!-- /.wrap.marqueeall-wrap -->
		<?php
	}

	/**
	 * AJAX handler — save widget statuses.
	 *
	 * Expects POST fields:
	 *   nonce          string   WordPress nonce
	 *   widget_status  array    Complete map of slug => 1|0 for every widget
	 *
	 * @return void  Sends JSON response and exits.
	 */
	public function ajax_save_widget_status() {
		// Nonce verification.
		check_ajax_referer( 'marqueeall_save_widget_status', 'nonce' );

		// Capability check.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				[ 'message' => __( 'You do not have permission to perform this action.', 'marqueeall' ) ],
				403
			);
			return;
		}

		// Read and sanitize the incoming status map.
		// The JS sends a complete object with 1 or 0 for every slug,
		// so unchecked (disabled) widgets are explicitly included as 0.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- already verified above.
		$raw_status = isset( $_POST['widget_status'] ) && is_array( $_POST['widget_status'] )
			? wp_unslash( $_POST['widget_status'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: [];

		$incoming = [];
		foreach ( $raw_status as $slug => $value ) {
			$incoming[ sanitize_key( $slug ) ] = absint( $value );
		}

		// Pass to the manager, which builds the full canonical map.
		$result = Widget_Manager::instance()->save_status( $incoming );

		$enabled_count = Widget_Manager::instance()->get_enabled_count();
		$total         = Widget_Registry::instance()->count();

		if ( 'error' === $result ) {
			wp_send_json_error(
				[ 'message' => __( 'Could not save settings. Please try again.', 'marqueeall' ) ]
			);
			return;
		}

		// Both 'saved' and 'unchanged' are reported as success to the user.
		$message = 'saved' === $result
			? __( 'Settings saved!', 'marqueeall' )
			: __( 'Settings saved!', 'marqueeall' );

		wp_send_json_success(
			[
				'message'       => $message,
				'enabled_count' => $enabled_count,
				'total'         => $total,
			]
		);
	}
}
