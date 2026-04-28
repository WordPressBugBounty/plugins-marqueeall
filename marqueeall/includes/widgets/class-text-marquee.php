<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Text_Marquee extends Widget_Base {

	public function get_name() {
		return 'masscie-text-marquee';
	}

	public function get_title() {
		return __( 'Text Marquee', 'marqueeall' );
	}

	public function get_icon() {
		return 'eicon-animated-headline marquee-all-widget-icon';
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

		/**
		 * Content Section
		 */
		$this->start_controls_section(
			'content',
			[
				'label' => __( 'Content', 'marqueeall' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			[
				'label'       => __( 'Text', 'marqueeall' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Your marquee text', 'marqueeall' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label'   => __( 'Icon (optional)', 'marqueeall' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'items',
			[
				'label'   => __( 'Items', 'marqueeall' ),
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[ 'text' => __( 'Smooth', 'marqueeall' ) ],
					[ 'text' => __( 'Scrolling', 'marqueeall' ) ],
					[ 'text' => __( 'Text', 'marqueeall' ) ],
				],
			]
		);
		
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

		/**
		 * Marquee Settings Section
		 */
		$this->start_controls_section(
			'marquee_settings',
			[
				'label' => __( 'Marquee Settings', 'marqueeall' ),
			]
		);

		$this->add_control(
			'orientation',
			[
				'label'   => __( 'Orientation', 'marqueeall' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'marqueeall' ),
					'vertical'   => __( 'Vertical', 'marqueeall' ),
				],
			]
		);

		$this->add_control(
			'vertical_height',
			[
				'label'      => __( 'Height (vh)', 'marqueeall' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'vh' ],
				'range'      => [
					'vh' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 60,
					'unit' => 'vh',
				],
				'condition'  => [
					'orientation' => 'vertical',
				],
			]
		);

		$this->add_control(
			'reverse',
			[
				'label'        => __( 'Reverse (flip direction)', 'marqueeall' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'pause',
			[
				'label'        => __( 'Pause on Hover', 'marqueeall' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label'      => __( 'Speed (px/s)', 'marqueeall' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 400,
					],
				],
				'default'    => [
					'size' => 20,
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'gap',
			[
				'label'   => __( 'Gap (px)', 'marqueeall' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 24,
			]
		);

		$this->add_control(
			'mask_edges',
			[
				'label'        => __( 'Soft Edge Mask', 'marqueeall' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Section
		 */
		$this->start_controls_section(
			'style',
			[
				'label' => __( 'Style', 'marqueeall' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .masscie-text',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'marqueeall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .masscie-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'marqueeall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .masscie-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Item Padding', 'marqueeall' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .masscie-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'marqueeall' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .masscie-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .masscie-item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .masscie-item',
			]
		);

		// ✅ New: Icon Style controls
		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'marqueeall' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .masscie-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'marqueeall' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 8,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .masscie-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_margin',
			[
				'label'      => __( 'Icon Margin', 'marqueeall' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .masscie-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();

		$speed       = isset( $s['speed']['size'] ) ? floatval( $s['speed']['size'] ) : 20;
		$gap         = ! empty( $s['gap'] ) ? intval( $s['gap'] ) : 24;
		$orientation = ! empty( $s['orientation'] ) ? $s['orientation'] : 'horizontal';
		$reverse     = ( ! empty( $s['reverse'] ) && 'yes' === $s['reverse'] ) ? 'yes' : 'no';
		$pause       = ( ! empty( $s['pause'] ) && 'yes' === $s['pause'] ) ? 'yes' : 'no';
		$mask        = ( ! empty( $s['mask_edges'] ) && 'yes' === $s['mask_edges'] );
		$vh          = ( 'vertical' === $orientation && ! empty( $s['vertical_height']['size'] ) ) ? $s['vertical_height']['size'] . 'vh' : '';

		echo '<div class="masscie-marquee-wrap ' . ( 'vertical' === $orientation ? 'masscie-vertical' : '' ) . ' ' . ( $mask ? 'masscie-mask-edges' : '' ) . '"';
		echo ' data-speed="' . esc_attr( $speed ) . '" data-gap="' . esc_attr( $gap ) . '"';
		echo ' data-reverse="' . esc_attr( $reverse ) . '" data-pause="' . esc_attr( $pause ) . '"';
		echo ( $vh ? ' style="height:' . esc_attr( $vh ) . ';"' : '' ) . '>';

		echo '<div class="masscie-track">';

		if ( ! empty( $s['items'] ) ) {
			foreach ( $s['items'] as $item ) {
				echo '<div class="masscie-item">';
				if ( ! empty( $item['icon']['value'] ) ) {
					echo '<span class="masscie-icon">';
					\Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
					echo '</span>';
				}
				echo '<span class="masscie-text">' . esc_html( $item['text'] ) . '</span>';
				echo '</div>';
			}
		}

		echo '</div></div>';
	}
}
