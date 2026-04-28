<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Text_Scramble extends Widget_Base {
    public function get_name() {
        return 'masscie_text_scramble';
    }

    public function get_title() {
        return __('Text Scramble', 'marqueeall');
    }

    public function get_icon() {
        return 'eicon-animation-text marquee-all-widget-icon';
    }

    public function get_categories() {
        return ['masscie-widgets']; 
    }
    
    public function get_keywords() {
        return [
            'marquee', 
            'text',
            'scramble',
            'animation',
            'typing',
            'effect',
            'text effect',
            'animated text',
            'marquee text'
        ];
    }

    protected function _register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'marqueeall'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'text',
            [
                'label' => __('Text', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Add your text here', 'marqueeall'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'texts',
            [
                'label' => __('Text Items', 'marqueeall'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'text' => __('Create stunning infinite marquees & tickers in Elementor', 'marqueeall'),
                    ],
                    [
                        'text' => __('for images, text, posts, testimonials,', 'marqueeall'),
                    ],
                    [
                        'text' => __('and custom text with smooth scrolling effects.', 'marqueeall'),
                    ],
                ],
                'title_field' => '{{{ text }}}',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => __('Animation Speed (ms)', 'marqueeall'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 500,
                'step' => 100,
            ]
        );

        // Add toggle for before/after text
        $this->add_control(
            'show_before_after',
            [
                'label' => __('Show Before/After Text', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'marqueeall'),
                'label_off' => __('No', 'marqueeall'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        // Add before text control
        $this->add_control(
            'before_text',
            [
                'label' => __('Before Text', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'default' => 'MarqueeAll -',
                'label_block' => true,
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        // Add after text control
        $this->add_control(
            'after_text',
            [
                'label' => __('After Text', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'default' => '🌀',
                'label_block' => true,
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Base Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'base_typography',
                'label' => __('Before/After Text Typography', 'marqueeall'),
                'selector' => '{{WRAPPER}} .masscie-scramble-container',
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT,
                ],
            ]
        );
        // Scrambled Text Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'scrambled_text_typography',
                'label' => __('Scrambled Text Typography', 'marqueeall'),
                'selector' => '{{WRAPPER}} .masscie-scramble-text',
                'separator' => 'before',
            ]
        );

        // Scrambled Text Background
        $this->add_control(
            'scrambled_text_background',
            [
                'label' => __('Background Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-text' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Scrambled Text Padding
        $this->add_responsive_control(
            'scrambled_text_padding',
            [
                'label' => __('Padding', 'marqueeall'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Scrambled Text Border
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'scrambled_text_border',
                'label' => __('Border', 'marqueeall'),
                'selector' => '{{WRAPPER}} .masscie-scramble-text',
            ]
        );

        // Scrambled Text Border Radius
        $this->add_control(
            'scrambled_text_border_radius',
            [
                'label' => __('Border Radius', 'marqueeall'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        // Before Text Style
        $this->add_control(
            'before_text_style',
            [
                'label' => __('Before Text', 'marqueeall'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'before_text_color',
            [
                'label' => __('Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-before' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        // Scrambled Text Style
        $this->add_control(
            'scrambled_text_style',
            [
                'label' => __('Scrambled Text', 'marqueeall'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'scrambled_text_color',
            [
                'label' => __('Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        // After Text Style
        $this->add_control(
            'after_text_style',
            [
                'label' => __('After Text', 'marqueeall'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'after_text_color',
            [
                'label' => __('Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-after' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        // Spacing
        $this->add_control(
            'text_spacing',
            [
                'label' => __('Text Spacing', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .masscie-scramble-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .masscie-scramble-after' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'show_before_after' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $texts = [];
        
        foreach ($settings['texts'] as $item) {
            if (!empty($item['text'])) {
                $texts[] = $item['text'];
            }
        }

        $this->add_render_attribute('scramble', 'class', 'masscie-scramble-text');
        $this->add_render_attribute('scramble', 'data-texts', wp_json_encode($texts));
        $this->add_render_attribute('scramble', 'data-speed', $settings['speed']);
        ?>
        
        <div class="masscie-scramble-container">
            <?php if ($settings['show_before_after'] === 'yes' && !empty($settings['before_text'])) : ?>
                <span class="masscie-scramble-before">
                    <?php echo esc_html($settings['before_text']); ?>
                </span>
            <?php endif; ?>

          <div <?php echo wp_kses_post( $this->get_render_attribute_string( 'scramble' ) ); ?>></div>


            <?php if ($settings['show_before_after'] === 'yes' && !empty($settings['after_text'])) : ?>
                <span class="masscie-scramble-after">
                    <?php echo esc_html($settings['after_text']); ?>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }
}