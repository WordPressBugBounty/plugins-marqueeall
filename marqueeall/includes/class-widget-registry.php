<?php
/**
 * Widget Registry
 *
 * Single source of truth for every MarqueeAll widget.
 * Elementor registration, Widget Manager admin page, and future
 * additions all read from this one list.
 *
 * @package MarqueeAll
 * @since   1.3.0
 */

namespace MASSCIE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Widget_Registry
 */
final class Widget_Registry {

	/**
	 * Singleton instance.
	 *
	 * @var Widget_Registry|null
	 */
	private static $instance = null;

	/**
	 * All registered widget definitions.
	 *
	 * @var array
	 */
	private $widgets = [];

	/**
	 * Get singleton instance.
	 *
	 * @return Widget_Registry
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor — populates the widget list.
	 */
	private function __construct() {
		$this->build();
	}

	/**
	 * Build the widget list.
	 *
	 * Schema per entry:
	 *   slug  — kebab-case option key shown in Widget Manager
	 *   title — human-readable label
	 *   class — fully-qualified PHP class (single backslash at runtime)
	 *   file  — path relative to MASSCIE_PATH (no leading slash)
	 *   icon  — Elementor eicon class for the admin card
	 *   docs  — documentation URL
	 *   demo  — live demo URL
	 *   pro   — true = Pro-only, toggle disabled in Widget Manager
	 *
	 * @return void
	 */
	private function build() {
		$docs = 'https://marqueeall.com/docs/';
		$demo = 'https://marqueeall.com/demo/';

		$this->widgets = [
			[
				'slug'  => 'text-marquee',
				'title' => __( 'Text Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Text_Marquee',
				'file'  => 'includes/widgets/class-text-marquee.php',
				'icon'  => 'eicon-animated-headline',
				'docs'  => $docs . 'text-marquee/',
				'demo'  => $demo . 'text-marquee/',
				'pro'   => false,
			],
			[
				'slug'  => 'image-marquee',
				'title' => __( 'Image Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Image_Marquee',
				'file'  => 'includes/widgets/class-image-marquee.php',
				'icon'  => 'eicon-slider-push',
				'docs'  => $docs . 'image-marquee/',
				'demo'  => $demo . 'image-marquee/',
				'pro'   => false,
			],
			[
				'slug'  => 'testimonial-marquee',
				'title' => __( 'Testimonial Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Testimonial_Marquee',
				'file'  => 'includes/widgets/class-testimonial-marquee.php',
				'icon'  => 'eicon-testimonial',
				'docs'  => $docs . 'testimonial-marquee/',
				'demo'  => $demo . 'testimonial-marquee/',
				'pro'   => false,
			],
			[
				'slug'  => 'news-ticker',
				'title' => __( 'News Ticker', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\News_Ticker',
				'file'  => 'includes/widgets/class-news-ticker.php',
				'icon'  => 'eicon-post-list',
				'docs'  => $docs . 'news-ticker/',
				'demo'  => $demo . 'news-ticker/',
				'pro'   => false,
			],
			[
				'slug'  => 'post-grid-marquee',
				'title' => __( 'Post Grid Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Post_Grid_Marquee',
				'file'  => 'includes/widgets/class-post-grid-marquee.php',
				'icon'  => 'eicon-posts-grid',
				'docs'  => $docs . 'post-grid-marquee/',
				'demo'  => $demo . 'post-grid-marquee/',
				'pro'   => false,
			],
			[
				'slug'  => 'team-members-marquee',
				'title' => __( 'Team Members Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Team_Members_Marquee',
				'file'  => 'includes/widgets/class-team-members-marquee.php',
				'icon'  => 'eicon-person',
				'docs'  => $docs . 'team-members-marquee/',
				'demo'  => $demo . 'team-members-marquee/',
				'pro'   => false,
			],
			[
				'slug'  => 'text-scramble',
				'title' => __( 'Text Scramble', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Text_Scramble',
				'file'  => 'includes/widgets/class-text-scramble.php',
				'icon'  => 'eicon-t-letter',
				'docs'  => $docs . 'text-scramble/',
				'demo'  => $demo . 'text-scramble/',
				'pro'   => false,
			],
			[
				'slug'  => 'crypto-marquee',
				'title' => __( 'Crypto Marquee', 'marqueeall' ),
				'class' => 'MASSCIE\Widgets\Crypto_Marquee',
				'file'  => 'includes/widgets/class-crypto-marquee.php',
				'icon'  => 'eicon-price-list',
				'docs'  => $docs . 'crypto-marquee/',
				'demo'  => $demo . 'crypto-marquee/',
				'pro'   => false,
			],
		];

		/**
		 * Filters the widget registry.
		 *
		 * Pro add-ons and third-party code can append widget definitions here.
		 *
		 * @since 1.3.0
		 *
		 * @param array $widgets Array of widget definition arrays.
		 */
		$this->widgets = apply_filters( 'marqueeall_widget_registry', $this->widgets );
	}

	/**
	 * Return all widget definitions.
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->widgets;
	}

	/**
	 * Return a single widget definition by slug.
	 *
	 * @param string $slug Widget slug.
	 * @return array|null
	 */
	public function get( $slug ) {
		foreach ( $this->widgets as $widget ) {
			if ( $widget['slug'] === $slug ) {
				return $widget;
			}
		}
		return null;
	}

	/**
	 * Return all registered slugs.
	 *
	 * @return string[]
	 */
	public function get_slugs() {
		return array_column( $this->widgets, 'slug' );
	}

	/**
	 * Return total widget count.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->widgets );
	}
}
