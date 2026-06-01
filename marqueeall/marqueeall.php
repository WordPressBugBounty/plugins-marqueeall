<?php
/**
 * Plugin Name: MarqueeAll – Elementor Marquee for Image, Text, Post Grid, Testimonial,  Cryptocurrency & News Ticker 🌀
 * Description:     All-in-one Elementor marquee addon for scrolling text, images, posts, testimonials, cryptocurrency price ticker, and news ticker widgets.
 * Version:           1.2.1
 * Author:            Aman Brar
 * Author URI:        https://profiles.wordpress.org/amandeepwebspero/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Tested up to:      7.0
 * Text Domain: marqueeall
 * Requires Plugins:  elementor
 *
 * @package MarqueeAll
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.s
}

define( 'MASSCIE_VERSION', '1.1.0' );
define( 'MASSCIE_PATH', plugin_dir_path( __FILE__ ) );
define( 'MASSCIE_URL', plugin_dir_url( __FILE__ ) );

// Load the core plugin file.
require_once MASSCIE_PATH . 'includes/plugin.php';
