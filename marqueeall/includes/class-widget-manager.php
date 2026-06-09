<?php
/**
 * Widget Manager
 *
 * Manages enable/disable state for every MarqueeAll widget and drives
 * the dynamic Elementor registration loop.
 *
 * @package MarqueeAll
 * @since   1.3.0
 */

namespace MASSCIE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Manager
 */
final class Widget_Manager {

	/**
	 * WordPress option key for widget statuses.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'marqueeall_widget_status';

	/**
	 * Singleton instance.
	 *
	 * @var Widget_Manager|null
	 */
	private static $instance = null;

	/**
	 * In-memory status cache (loaded once per request).
	 *
	 * @var array|null
	 */
	private $status_cache = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Widget_Manager
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {}

	/**
	 * Build default statuses: free widgets = 1, pro widgets = 0.
	 *
	 * @return array  [ 'slug' => 1|0 ]
	 */
	public function get_default_status() {
		$defaults = [];
		foreach ( Widget_Registry::instance()->get_all() as $widget ) {
			$defaults[ $widget['slug'] ] = empty( $widget['pro'] ) ? 1 : 0;
		}
		return $defaults;
	}

	/**
	 * Get the stored (or default) status map.
	 *
	 * Uses an in-memory cache so the DB is only hit once per request.
	 *
	 * @return array  [ 'slug' => 1|0 ]
	 */
	public function get_status() {
		if ( null !== $this->status_cache ) {
			return $this->status_cache;
		}

		$saved = get_option( self::OPTION_KEY, false );

		// No option yet (fresh install or upgrade without activation): use defaults.
		if ( false === $saved || ! is_array( $saved ) ) {
			$saved = $this->get_default_status();
		}

		$this->status_cache = $saved;
		return $this->status_cache;
	}

	/**
	 * Check whether a widget is currently enabled.
	 *
	 * Widgets that exist in the registry but not yet in the saved option
	 * (added in a later version) default to enabled.
	 *
	 * @param string $slug Widget slug.
	 * @return bool
	 */
	public function is_enabled( $slug ) {
		$status = $this->get_status();

		// Slug not present → newly added widget → treat as enabled.
		if ( ! array_key_exists( $slug, $status ) ) {
			return true;
		}

		return 1 === (int) $status[ $slug ];
	}

	/**
	 * Count currently enabled widgets.
	 *
	 * @return int
	 */
	public function get_enabled_count() {
		return count( array_filter( $this->get_status() ) );
	}

	/**
	 * Persist a new status map.
	 *
	 * Builds a canonical full map from the registry so every slug is
	 * always present in the option, whether the JS sent it or not.
	 *
	 * Returns:
	 *   'saved'     — value changed and was written to DB
	 *   'unchanged' — value identical to what was already stored
	 *   'error'     — update_option failed
	 *
	 * @param array $incoming  [ slug => 1|0 ] from the AJAX call.
	 * @return string 'saved'|'unchanged'|'error'
	 */
	public function save_status( array $incoming ) {
		$canonical = [];

		foreach ( Widget_Registry::instance()->get_slugs() as $slug ) {
			// Anything in $incoming is a real value (JS sends explicit 0 or 1).
			// Anything missing defaults to 0 (safety fallback only).
			$canonical[ $slug ] = isset( $incoming[ $slug ] ) ? (int) (bool) $incoming[ $slug ] : 0;
		}

		// Compare to current stored value before writing.
		$existing = get_option( self::OPTION_KEY, false );

		// Update the in-memory cache immediately so subsequent calls in this
		// request (e.g. get_enabled_count()) reflect the new values.
		$this->status_cache = $canonical;

		if ( is_array( $existing ) && $existing === $canonical ) {
			// Force a write anyway so autoload flag is consistent.
			update_option( self::OPTION_KEY, $canonical, false );
			return 'unchanged';
		}

		$ok = update_option( self::OPTION_KEY, $canonical, false );
		return $ok ? 'saved' : 'error';
	}

	/**
	 * Register all enabled widgets with Elementor.
	 *
	 * Called from Plugin on the elementor/widgets/register action.
	 * Disabled widgets never have their files loaded.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widget manager instance.
	 * @return void
	 */
	public function register_with_elementor( $widgets_manager ) {
		foreach ( Widget_Registry::instance()->get_all() as $widget ) {
			if ( ! $this->is_enabled( $widget['slug'] ) ) {
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
	 * Seed default statuses on plugin activation (runs once).
	 *
	 * Uses add_option so it is a no-op on subsequent activations,
	 * preserving any settings the user has already saved.
	 *
	 * @return void
	 */
	public static function on_activation() {
		if ( false === get_option( self::OPTION_KEY ) ) {
			add_option(
				self::OPTION_KEY,
				self::instance()->get_default_status(),
				'',
				false  // Do not autoload.
			);
		}
	}

	/**
	 * Upgrade routine — ensure the option exists for users upgrading
	 * from versions before 1.3.0 that never triggered the activation hook.
	 *
	 * Hooked on init (low priority) from Plugin::init_hooks().
	 *
	 * @return void
	 */
	public static function maybe_seed_option() {
		if ( false === get_option( self::OPTION_KEY ) ) {
			add_option(
				self::OPTION_KEY,
				self::instance()->get_default_status(),
				'',
				false
			);
		}
	}
}
