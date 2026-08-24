<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Faq_Marquee extends Widget_Base {

	public function get_name() {
		return 'masscie-faq-marquee';
	}

	public function get_title() {
		return __( 'FAQ Marquee', 'marqueeall' );
	}

	public function get_icon() {
		return 'eicon-accordion marquee-all-widget-icon';
	}

	public function get_categories() {
		return [ 'masscie-widgets' ];
	}

	public function get_keywords() {
		return [ 'faq', 'accordion', 'marquee', 'ticker', 'questions', 'help' ];
	}

	public function get_style_depends() {
		return [ 'masscie-style' ];
	}

	public function get_script_depends() {
		return [ 'masscie-marquee' ];
	}

	protected function register_controls() {

		// Content: FAQ Items
		$this->start_controls_section(
			'faq_section',
			[ 'label' => __( 'FAQ Items', 'marqueeall' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'faq_question',
			[
				'label' => __( 'Question', 'marqueeall' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Sample question?', 'marqueeall' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'faq_answer',
			[
				'label' => __( 'Answer', 'marqueeall' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Sample answer text goes here.', 'marqueeall' ),
				'label_block' => true,
				'rows' => 4,
			]
		);

		$this->add_control(
			'faq_items',
			[
				'label' => __( 'Questions', 'marqueeall' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ faq_question }}}',
				'default' => [
					[ 'faq_question' => __( 'Does it work with any theme?', 'marqueeall' ), 'faq_answer' => __( 'Yes, it works with any theme that is compatible with Elementor.', 'marqueeall' ) ],
					[ 'faq_question' => __( 'Can I control speed and direction?', 'marqueeall' ), 'faq_answer' => __( 'Yes, adjust speed, direction, and spacing for every marquee widget.', 'marqueeall' ) ],
					[ 'faq_question' => __( 'How many items for best performance?', 'marqueeall' ), 'faq_answer' => __( 'We recommend 8 to 10 items per marquee for a smooth continuous loop.', 'marqueeall' ) ],
					[ 'faq_question' => __( 'Can I pause on hover?', 'marqueeall' ), 'faq_answer' => __( 'Every widget has a pause on hover option so visitors can read easily.', 'marqueeall' ) ],
				],
			]
		);

		$this->end_controls_section();

		// Marquee Settings
		$this->start_controls_section(
			'marquee_section',
			[ 'label' => __( 'Marquee Settings', 'marqueeall' ) ]
		);

		$this->add_control(
			'orientation',
			[
				'label' => __( 'Orientation', 'marqueeall' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'marqueeall' ),
					'vertical' => __( 'Vertical', 'marqueeall' ),
				],
			]
		);

		$this->add_control(
			'vertical_height',
			[
				'label' => __( 'Height (vh)', 'marqueeall' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'vh' ],
				'range' => [
					'vh' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 60,
					'unit' => 'vh',
				],
				'condition' => [
					'orientation' => 'vertical',
				],
			]
		);

		$this->add_control(
			'reverse',
			[
				'label' => __( 'Reverse (flip direction)', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => __( 'Pause on Hover', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => __( 'Speed (px/s)', 'marqueeall' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 400,
					],
				],
				'default' => [
					'size' => 40,
					'unit' => 'px',
				],
			]
		);

		$this->add_control(
			'gap',
			[
				'label' => __( 'Gap (px)', 'marqueeall' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 12,
			]
		);

		$this->add_control(
			'mask_edges',
			[
				'label' => __( 'Soft Edge Mask', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$this->end_controls_section();

		// Behavior
		$this->start_controls_section(
			'behavior_section',
			[ 'label' => __( 'Behavior', 'marqueeall' ) ]
		);

		$this->add_control(
			'single_open',
			[
				'label' => __( 'Only One Answer Open At A Time', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'schema_markup',
			[
				'label' => __( 'Add FAQ Schema (SEO)', 'marqueeall' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'description' => __( 'Outputs FAQPage structured data so search engines can show rich results.', 'marqueeall' ),
			]
		);

		$this->end_controls_section();

		// Style: Chips
		$this->start_controls_section(
			'style_chip_section',
			[
				'label' => __( 'Question Chips', 'marqueeall' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'chip_color',
			[
				'label' => __( 'Text Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chip_bg_color',
			[
				'label' => __( 'Background Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chip_active_color',
			[
				'label' => __( 'Active Text Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip.masscie-faq-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'chip_active_bg_color',
			[
				'label' => __( 'Active Background Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000000',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip.masscie-faq-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'chip_typography',
				'selector' => '{{WRAPPER}} .masscie-faq-chip',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'chip_border',
				'selector' => '{{WRAPPER}} .masscie-faq-chip',
			]
		);

		$this->add_responsive_control(
			'chip_radius',
			[
				'label' => __( 'Radius', 'marqueeall' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'chip_padding',
			[
				'label' => __( 'Padding', 'marqueeall' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-chip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Style: Answer Panel
		$this->start_controls_section(
			'style_panel_section',
			[
				'label' => __( 'Answer Panel', 'marqueeall' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'panel_bg_color',
			[
				'label' => __( 'Background Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fafafa',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-panel' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'question_color',
			[
				'label' => __( 'Question Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1a1a1a',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-panel-question' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'answer_color',
			[
				'label' => __( 'Answer Color', 'marqueeall' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#555555',
				'selectors' => [
					'{{WRAPPER}} .masscie-faq-panel-answer' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'answer_typography',
				'selector' => '{{WRAPPER}} .masscie-faq-panel-answer',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$orientation = ! empty( $settings['orientation'] ) ? $settings['orientation'] : 'horizontal';
		$reverse     = ( ! empty( $settings['reverse'] ) && 'yes' === $settings['reverse'] ) ? 'yes' : 'no';
		$pause       = ( ! empty( $settings['pause_on_hover'] ) && 'yes' === $settings['pause_on_hover'] ) ? 'yes' : 'no';
		$speed       = isset( $settings['speed']['size'] ) ? floatval( $settings['speed']['size'] ) : 40;
		$gap         = isset( $settings['gap'] ) ? intval( $settings['gap'] ) : 12;
		$mask        = ( ! empty( $settings['mask_edges'] ) && 'yes' === $settings['mask_edges'] );
		$vh          = ( 'vertical' === $orientation && ! empty( $settings['vertical_height']['size'] ) ) ? $settings['vertical_height']['size'] . 'vh' : '';
		$single      = ( ! empty( $settings['single_open'] ) && 'yes' === $settings['single_open'] ) ? 'yes' : 'no';

		$items = ! empty( $settings['faq_items'] ) ? $settings['faq_items'] : [];

		if ( empty( $items ) ) {
			return;
		}

		$widget_id = 'masscie-faq-' . esc_attr( $this->get_id() );

		$wrap_classes = 'masscie-marquee-wrap masscie-faq-track-wrap';
		$wrap_classes .= ( 'vertical' === $orientation ) ? ' masscie-vertical' : '';
		$wrap_classes .= $mask ? ' masscie-mask-edges' : '';

		?>
		<div class="masscie-faq-marquee"
			id="<?php echo esc_attr( $widget_id ); ?>"
			data-single-open="<?php echo esc_attr( $single ); ?>"
		>
			<div class="<?php echo esc_attr( $wrap_classes ); ?>"
				data-speed="<?php echo esc_attr( $speed ); ?>"
				data-reverse="<?php echo esc_attr( $reverse ); ?>"
				data-pause="<?php echo esc_attr( $pause ); ?>"
				data-gap="<?php echo esc_attr( $gap ); ?>"
				<?php echo $vh ? ' style="height:' . esc_attr( $vh ) . ';"' : ''; ?>
			>
				<div class="masscie-track">
					<?php foreach ( $items as $index => $item ) : ?>
						<div class="masscie-item masscie-faq-chip"
							data-faq-question="<?php echo esc_attr( $item['faq_question'] ); ?>"
							data-faq-answer="<?php echo esc_attr( $item['faq_answer'] ); ?>"
							role="button"
							tabindex="0"
							aria-expanded="false"
						>
							<?php echo esc_html( $item['faq_question'] ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="masscie-faq-panel" style="display:none;">
				<p class="masscie-faq-panel-question"></p>
				<p class="masscie-faq-panel-answer"></p>
			</div>
		</div>

		<?php if ( ! empty( $settings['schema_markup'] ) && 'yes' === $settings['schema_markup'] ) : ?>
			<script type="application/ld+json">
			<?php
			$schema = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => [],
			];

			foreach ( $items as $item ) {
				$schema['mainEntity'][] = [
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $item['faq_question'] ),
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $item['faq_answer'] ),
					],
				];
			}

			echo wp_json_encode( $schema );
			?>
			</script>
		<?php endif; ?>
		<?php
	}
}
