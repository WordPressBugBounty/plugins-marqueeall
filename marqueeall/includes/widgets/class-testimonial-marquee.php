<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit;

class Testimonial_Marquee extends Widget_Base {
    public function get_name() { return 'masscie-testimonial-marquee'; }
    public function get_title() { return __( 'Testimonial Marquee', 'marqueeall' ); }
    public function get_icon() { return 'eicon-testimonial-carousel marquee-all-widget-icon'; }
    public function get_categories() { return [ 'masscie-widgets' ]; }
    public function get_style_depends() { return [ 'masscie-style' ]; }
    public function get_script_depends() { return [ 'masscie-marquee' ]; }

    protected function register_controls() {
        // Content / repeater
        $this->start_controls_section('content', [ 'label' => __( 'Testimonials', 'marqueeall' ) ]);

        $repeater = new Repeater();
        $repeater->add_control('content', [
            'label' => __( 'Content', 'marqueeall' ),
            'type'  => Controls_Manager::TEXTAREA,
            'default' => __( 'This is a testimonial text', 'marqueeall' ),
        ]);
        $repeater->add_control('avatar', [
            'label' => __( 'Avatar', 'marqueeall' ),
            'type'  => Controls_Manager::MEDIA,
            'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
        ]);
        $repeater->add_control('name', [
            'label' => __( 'Name', 'marqueeall' ),
            'type'  => Controls_Manager::TEXT,
            'default' => __( 'John Doe', 'marqueeall' ),
        ]);
        $repeater->add_control('role', [
            'label' => __( 'Role/Designation', 'marqueeall' ),
            'type'  => Controls_Manager::TEXT,
            'default' => __( 'Project Manager', 'marqueeall' ),
        ]);
        $repeater->add_control('rating', [
            'label' => __( 'Rating', 'marqueeall' ),
            'type'  => Controls_Manager::NUMBER,
            'min'   => 1,
            'max'   => 5,
            'step'  => 1,
            'default' => 5,
        ]);

        $this->add_control('items', [
            'label' => __( 'Testimonials List', 'marqueeall' ),
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $repeater->get_controls(),
            'default' => [
                [ 'content' => __( 'MarqueeAll Addons is the perfect tool for anyone using Elementor.', 'marqueeall' ), 'name' => __( 'Wade Warren', 'marqueeall' ), 'role' => __( 'Project Manager', 'marqueeall' ), 'rating' => 5 ],
            ],
            'title_field' => '{{{ name }}}',
        ]);
		
		// Tip / Notice for best performance
		$this->add_control(
			'masscie_image_marquee_tip',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => '<strong>💡 Tip:</strong> For a smoother slider experience, add <strong>8–10 items</strong>.',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			]
		);

        $this->end_controls_section();

        // Marquee settings (same names as Image_Marquee)
        $this->start_controls_section('settings', [ 'label' => __( 'Marquee Settings', 'marqueeall' ) ]);

        $this->add_control('orientation', [
            'label' => __( 'Orientation', 'marqueeall' ),
            'type'  => Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => __( 'Horizontal', 'marqueeall' ),
                'vertical'   => __( 'Vertical', 'marqueeall' ),
            ],
        ]);

        $this->add_control('vertical_height', [
            'label' => __( 'Height (vh)', 'marqueeall' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'vh' ],
            'range' => [ 'vh' => [ 'min' => 20, 'max' => 100 ] ],
            'default' => [ 'size' => 60, 'unit' => 'vh' ],
            'condition' => [ 'orientation' => 'vertical' ],
        ]);

        $this->add_control('reverse', [
            'label' => __( 'Reverse (flip direction)', 'marqueeall' ),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_control('pause', [
            'label' => __( 'Pause on Hover', 'marqueeall' ),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('speed', [
            'label' => __( 'Speed (px/s)', 'marqueeall' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range' => [ 'px' => [ 'min' => 10, 'max' => 400 ] ],
            'default' => [ 'size' => 60, 'unit' => 'px' ],
        ]);

        $this->add_control('gap', [
            'label' => __( 'Gap (px)', 'marqueeall' ),
            'type'  => Controls_Manager::NUMBER,
            'default' => 24,
        ]);

        $this->add_control('mask_edges', [
            'label' => __( 'Soft Edge Mask', 'marqueeall' ),
            'type'  => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->end_controls_section();

        // Style controls: card bg, content/name/role color, stars color
        $this->start_controls_section('style_section', [ 'label' => __( 'Card Style', 'marqueeall' ), 'tab' => Controls_Manager::TAB_STYLE ]);

        $this->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'card_background',
            'label' => __( 'Card Background', 'marqueeall' ),
            'types' => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .masscie-testimonial .masscie-card',
        ]);

        $this->add_control('content_color', [
            'label' => __( 'Content Color', 'marqueeall' ),
            'type'  => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .masscie-testimonial .masscie-content' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control('name_color', [
            'label' => __( 'Name Color', 'marqueeall' ),
            'type'  => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .masscie-testimonial .masscie-name' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control('role_color', [
            'label' => __( 'Role Color', 'marqueeall' ),
            'type'  => Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .masscie-testimonial .masscie-role' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control('stars_color', [
            'label' => __( 'Stars Color', 'marqueeall' ),
            'type'  => Controls_Manager::COLOR,
            'default' => '#f1c40f',
            'selectors' => [ '{{WRAPPER}} .masscie-testimonial .masscie-star' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'content_typo',
            'selector' => '{{WRAPPER}} .masscie-testimonial .masscie-content',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $speed = isset($s['speed']['size']) ? floatval($s['speed']['size']) : 60;
        $gap   = !empty($s['gap']) ? intval($s['gap']) : 24;
        $orientation = !empty($s['orientation']) ? $s['orientation'] : 'horizontal';
        $reverse = !empty($s['reverse']) && $s['reverse'] === 'yes' ? 'yes' : 'no';
        $pause = !empty($s['pause']) && $s['pause'] === 'yes' ? 'yes' : 'no';
        $mask = !empty($s['mask_edges']) && $s['mask_edges'] === 'yes';
        $vh = ($orientation === 'vertical' && !empty($s['vertical_height']['size'])) ? $s['vertical_height']['size'] . 'vh' : '';

        echo '<div class="masscie-marquee-wrap ' 
            . ($orientation==='vertical' ? 'masscie-vertical' : '') 
            . ' ' . ($mask ? 'masscie-mask-edges' : '') . '"'
            . ' data-speed="' . esc_attr($speed) . '" data-gap="'. esc_attr($gap) .'"'
            . ' data-reverse="'. esc_attr($reverse) .'" data-pause="'. esc_attr($pause) .'"'
            . ($vh ? ' style="height:'. esc_attr($vh) .';"' : '') . '>';

        echo '<div class="masscie-track">';
        if ( ! empty( $s['items'] ) ) {
            foreach ( $s['items'] as $item ) {
                $stars = '';
                $count = isset($item['rating']) ? intval($item['rating']) : 5;
                for ($i=0; $i < 5; $i++) $stars .= '<span class="masscie-star'.($i < $count ? '' : ' inactive').'">★</span>';

                echo '<div class="masscie-item masscie-testimonial">';
                echo '<div class="masscie-card">';

                echo '<div class="masscie-content">'. wp_kses_post( nl2br( $item['content'] ) ) .'</div>';

                echo '<div class="masscie-author">';
                if ( ! empty( $item['avatar']['url'] ) ) {
                    echo ' <div class="masscie-avatar"><img class="masscie-avatar" src="'. esc_url( $item['avatar']['url'] ) .'" alt="'. esc_attr( $item['name'] ) .'"></div>';
                }
                echo '<div><div class="masscie-name">'. esc_html( $item['name'] ) .'</div>';
                if ( ! empty( $item['role'] ) ) {
                    echo '<div class="masscie-role">'. esc_html( $item['role'] ) .'</div>';
                }
            	 echo '<div class="masscie-rating">' . wp_kses_post( $stars ) . '</div>';
                echo '</div>'; // .masscie-author
                echo '</div>'; // .masscie-card
                echo '</div>'; // .masscie-card
                echo '</div>'; // .masscie-testimonial
            }
        }
        echo '</div></div>';
    }
}
