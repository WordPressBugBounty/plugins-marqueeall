<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class News_Ticker extends Widget_Base {

	public function get_name() {
		return 'masscie-news-ticker';
	}

	public function get_title() {
		return __( 'Marquee News Ticker', 'marqueeall' );
	}

	public function get_icon() {
		return 'eicon-post-list marquee-all-widget-icon';
	}

	public function get_categories() {
		return [ 'masscie-widgets' ];
	}

	public function get_style_depends() {
		return [ 'masscie-style' ];
	}

	public function get_script_depends() {
		return [ 'masscie-marquee' ];
	}

	protected function register_controls() {

		// Layout
		$this->start_controls_section(
			'layout_section',
			[ 'label' => __( 'Layout', 'marqueeall' ) ]
		);

		$this->add_control(
			'posts_per_ticker',
			[
				'label' => __( 'Post Per Ticker', 'marqueeall' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'default' => 5,
				'condition' => [
					'source_type' => 'posts',
				],
			]
		);

		$this->add_control(
			'show_label',
			[
				'label' => __( 'Show Label', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_title',
			[
				'label' => __( 'Label Text', 'marqueeall' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Latest News', 'marqueeall' ),
				'condition' => [ 'show_label' => 'yes' ],
			]
		);

		$this->add_control(
			'label_tag',
			[
				'label' => __( 'Label HTML Tag', 'marqueeall' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','h5'=>'H5','h6'=>'H6','div'=>'div','span'=>'span',
				],
				'condition' => [ 'show_label' => 'yes' ],
			]
		);

		$this->add_control(
			'label_icon',
			[
				'label' => __( 'Label Icon', 'marqueeall' ),
				'type' => Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-newspaper', 'library' => 'fa-solid' ],
				'condition' => [ 'show_label' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// Query
		$this->start_controls_section(
			'query_section',
			[ 'label' => __( 'Query', 'marqueeall' ) ]
		);

		// Source Type: Posts or Custom Text
		$this->add_control(
			'source_type',
			[
				'label' => __( 'Source Type', 'marqueeall' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'posts',
				'options' => [
					'posts' => __( 'Posts', 'marqueeall' ),
					'custom' => __( 'Custom Text', 'marqueeall' ),
				],
			]
		);

		// Separator controls
		$this->add_control(
			'separator_type',
			[
				'label' => __( 'Separator Type', 'marqueeall' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'marqueeall' ),
					'icon' => __( 'Icon', 'marqueeall' ),
				],
			]
		);

		$this->add_control(
			'separator_icon',
			[
				'label' => __( 'Separator Icon', 'marqueeall' ),
				'type' => Controls_Manager::ICONS,
				'default' => [ 'value' => 'fas fa-circle', 'library' => 'fa-solid' ],
				'condition' => [ 'separator_type' => 'icon' ],
			]
		);

		$this->add_control(
			'separator_text',
			[
				'label' => __( 'Separator Text', 'marqueeall' ),
				'type' => Controls_Manager::TEXT,
				'default' => '|',
				'condition' => [ 'separator_type' => 'text' ],
			]
		);

		// Custom Text Repeater
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'custom_text_item',
			[
				'label' => __( 'Text Item', 'marqueeall' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Sample text', 'marqueeall' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'custom_text_link',
			[
				'label' => __( 'Link', 'marqueeall' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'https://your-link.com',
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => false,
					'nofollow' => false,
				],
			]
		);

		$this->add_control(
			'custom_items',
			[
				'label' => __( 'Custom Text Items', 'marqueeall' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ custom_text_item }}}',
				'condition' => [
					'source_type' => 'custom',
				],
				'default' => [
					['custom_text_item' => '🚨 Breaking: Major event happening now!', 'custom_text_link' => ['url'=>'#']],
					['custom_text_item' => '🌟 Trending: New viral challenge on the internet!', 'custom_text_link' => ['url'=>'#']],
					['custom_text_item' => '📰 Just In: Local sports team wins big!', 'custom_text_link' => ['url'=>'#']],
					['custom_text_item' => '⚡ Alert: Weather update – storms approaching!', 'custom_text_link' => ['url'=>'#']],
				],
			]
		);


		$this->end_controls_section();

		// Marquee settings
		$this->start_controls_section(
			'marquee_section',
			[ 'label' => __( 'Marquee Settings', 'marqueeall' ) ]
		);

		$this->add_control(
			'reverse',
			[
				'label' => __( 'Reverse', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => __( 'Pause On Hover', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Speed (px/s)', 'marqueeall' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 10,
				'min' => 1,
				'max' => 1000,
			]
		);

		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'style_section',
			[
				'label' => __( 'Style Options', 'marqueeall' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Label Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .masscie-news-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masscie-news-title svg path' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .masscie-news-title',
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label' => __( 'Separator Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .masscie-item.masscie-separator' => 'color: {{VALUE}};',
					'{{WRAPPER}} .masscie-item.masscie-separator svg path' => 'fill: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'link_color',
			[
				'label' => __( 'Link Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .masscie-item a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_color',
			[
				'label' => __( 'Link Hover Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff0000',
				'selectors' => [
					'{{WRAPPER}} .masscie-item a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$reverse = ( ! empty( $settings['reverse'] ) && 'yes' === $settings['reverse'] ) ? 'yes' : 'no';
		$pause   = ( ! empty( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'] ) ? 'yes' : 'no';

		// Label HTML
		$label_html = '';
		if ( ! empty( $settings['show_label'] ) && 'yes' === $settings['show_label'] ) {
			$title_tag = $settings['label_tag'] ?? 'h4';
			$title_text = $settings['label_title'] ?? __( 'Latest Updates', 'marqueeall' );
			$icon_html = '';
			if ( ! empty( $settings['label_icon']['value'] ) ) {
				ob_start();
				Icons_Manager::render_icon( $settings['label_icon'], [ 'aria-hidden' => 'true' ] );
				$icon_html = ob_get_clean();
			}
			$label_html = sprintf(
				'<div class="masscie-news-label"><%1$s class="masscie-news-title">%2$s %3$s</%1$s></div>',
				esc_attr( $title_tag ),
				esc_html( $title_text ),
				$icon_html
			);
		}

		?>
		<div class="masscie-marquee-wrap masscie-news-ticker"
			data-speed="<?php echo esc_attr( $settings['speed'] ); ?>"
			data-reverse="<?php echo esc_attr( $reverse ); ?>"
			data-pause="<?php echo esc_attr( $pause ); ?>"
		>
		<?php 
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $label_html; ?> 

			<div class="masscie-track">
				<?php
				if ( $settings['source_type'] === 'posts' ) {
					$posts_per = ! empty( $settings['posts_per_ticker'] ) ? intval( $settings['posts_per_ticker'] ) : 5;
					$query = new \WP_Query( [
						'post_type' => 'post',
						'posts_per_page' => $posts_per,
						'ignore_sticky_posts' => true,
					] );

					if ( $query->have_posts() ) :
						$first = true;
						while ( $query->have_posts() ) : $query->the_post();
							echo '<div class="masscie-item masscie-separator">';
							if ( $settings['separator_type'] === 'icon' && ! empty( $settings['separator_icon']['value'] ) ) {
								Icons_Manager::render_icon( $settings['separator_icon'], ['aria-hidden' => 'true'] );
							} else {
								echo esc_html( $settings['separator_text'] ?? '|' );
							}
							echo '</div>';

							echo '<div class="masscie-item">';
							echo '<a href="' . esc_html( get_the_permalink() ) . '"';
							echo '>' . esc_html(get_the_title() ) . '</a>';
							echo '</div>';

							$first = false;
						endwhile;
						wp_reset_postdata();
					else :
						echo '<div class="masscie-item"><span>' . esc_html__( 'No posts found.', 'marqueeall' ) . '</span></div>';
					endif;

				} elseif ( $settings['source_type'] === 'custom' ) {

					if ( ! empty( $settings['custom_items'] ) ) {
						foreach ( $settings['custom_items'] as $item ) {
							echo '<div class="masscie-item masscie-separator">';
							if ( $settings['separator_type'] === 'icon' && ! empty( $settings['separator_icon']['value'] ) ) {
								Icons_Manager::render_icon( $settings['separator_icon'], ['aria-hidden' => 'true'] );
							} else {
								echo esc_html( $settings['separator_text'] ?? '|' );
							}
							echo '</div>';

							$url = ! empty( $item['custom_text_link']['url'] ) ? esc_url( $item['custom_text_link']['url'] ) : '';
							$target = ! empty( $item['custom_text_link']['is_external'] ) ? ' target="_blank"' : '';
							$nofollow = ! empty( $item['custom_text_link']['nofollow'] ) ? ' rel="nofollow"' : '';

							echo '<div class="masscie-item">';
							if ( $url ) {
								echo '<a href="' . esc_html( $url ) . '"' . esc_html( $target ) . esc_html ( $nofollow ) . '>' . esc_html( $item['custom_text_item'] ) . '</a>';
							} else {
								echo esc_html( $item['custom_text_item'] );
							}
							echo '</div>';
						}
					}

				}
				?>
			</div>
		</div>
		<?php
	}
}
