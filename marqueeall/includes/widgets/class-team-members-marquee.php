<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;

if (!defined('ABSPATH')) {
    exit;
}

class Team_Members_Marquee extends Widget_Base {

    public function get_name() {
        return 'masscie-team-members-marquee';
    }

    public function get_title() {
        return __('Team Members Marquee', 'marqueeall');
    }

    public function get_icon() {
        return 'eicon-person marquee-all-widget-icon';
    }

    public function get_categories() {
        return ['masscie-widgets'];
    }

    public function get_style_depends() {
        return ['masscie-style'];
    }

    public function get_script_depends() {
        return ['masscie-marquee'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Team Members', 'marqueeall'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'member_image',
            [
                'label' => __('Choose Image', 'marqueeall'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'member_name',
            [
                'label' => __('Name', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'default' => __('John Doe', 'marqueeall'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'member_role',
            [
                'label' => __('Job Role', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Team Member', 'marqueeall'),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'team_members',
            [
                'label' => __('Team Members', 'marqueeall'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'member_name' => __('John Doe', 'marqueeall'),
                        'member_role' => __('CEO & Founder', 'marqueeall'),
                    ],
                    [
                        'member_name' => __('Jane Smith', 'marqueeall'),
                        'member_role' => __('Lead Developer', 'marqueeall'),
                    ],
                    [
                        'member_name' => __('Mike Johnson', 'marqueeall'),
                        'member_role' => __('Designer', 'marqueeall'),
                    ],
                ],
                'title_field' => '{{{ member_name }}}',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'medium_large',
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Marquee Settings Section
        $this->start_controls_section(
            'marquee_settings',
            [
                'label' => __('Marquee Settings', 'marqueeall'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'orientation',
            [
                'label' => __('Orientation', 'marqueeall'),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal', 'marqueeall'),
                    'vertical' => __('Vertical', 'marqueeall'),
                ],
            ]
        );

        $this->add_control(
            'vertical_height',
            [
                'label' => __('Height (vh)', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['vh'],
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
                'label' => __('Reverse Direction', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'pause',
            [
                'label' => __('Pause on Hover', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => __('Speed (px/s)', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label' => __('Gap (px)', 'marqueeall'),
                'type' => Controls_Manager::NUMBER,
                'default' => 24,
            ]
        );

        $this->add_control(
            'mask_edges',
            [
                'label' => __('Soft Edge Mask', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'image_effect',
            [
                'label' => __('Image Effect', 'marqueeall'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'marqueeall'),
                    'grayscale' => __('Grayscale', 'marqueeall'),
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();

        // Style Tab - Team Member Box
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Team Member Box', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'box_background',
            [
                'label' => __('Background Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'box_border',
                'selector' => '{{WRAPPER}} .masscie-team-member',
            ]
        );

        $this->add_control(
            'box_border_radius',
            [
                'label' => __('Border Radius', 'marqueeall'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .masscie-team-member',
            ]
        );

        $this->add_responsive_control(
            'box_padding',
            [
                'label' => __('Padding', 'marqueeall'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'content_alignment',
            [
                'label' => __('Content Alignment', 'marqueeall'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'marqueeall'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'marqueeall'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'marqueeall'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Image Style
        $this->start_controls_section(
            'image_style_section',
            [
                'label' => __('Image', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __('Width', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_spacing',
            [
                'label' => __('Spacing', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .masscie-team-member-image img',
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'marqueeall'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-image, {{WRAPPER}} .masscie-team-member-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .masscie-team-member-image img',
            ]
        );

        $this->end_controls_section();

        // Name Style
        $this->start_controls_section(
            'name_style_section',
            [
                'label' => __('Name', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .masscie-team-member-name',
            ]
        );

        $this->add_responsive_control(
            'name_spacing',
            [
                'label' => __('Spacing', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Role Style
        $this->start_controls_section(
            'role_style_section',
            [
                'label' => __('Job Role', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'role_color',
            [
                'label' => __('Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-team-member-role' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'role_typography',
                'selector' => '{{WRAPPER}} .masscie-team-member-role',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Marquee settings
        $speed = isset($settings['speed']['size']) ? floatval($settings['speed']['size']) : 20;
        $gap = !empty($settings['gap']) ? intval($settings['gap']) : 24;
        $orientation = !empty($settings['orientation']) ? $settings['orientation'] : 'horizontal';
        $reverse = (!empty($settings['reverse']) && 'yes' === $settings['reverse']) ? 'yes' : 'no';
        $pause = (!empty($settings['pause']) && 'yes' === $settings['pause']) ? 'yes' : 'no';
        $mask = (!empty($settings['mask_edges']) && 'yes' === $settings['mask_edges']);
        $vh = ('vertical' === $orientation && !empty($settings['vertical_height']['size'])) ? $settings['vertical_height']['size'] . 'vh' : '';

        // Get image size
        $image_size = 'medium_large';
        if (!empty($settings['thumbnail_size'])) {
            $image_size = $settings['thumbnail_size'];
        }

        // Marquee wrapper
        echo '<div class="masscie-marquee-wrap ' . 
             ('vertical' === $orientation ? 'masscie-vertical' : '') . ' ' . 
             ($mask ? 'masscie-mask-edges' : '') . '"';
        echo ' data-speed="' . esc_attr($speed) . '" data-gap="' . esc_attr($gap) . '"';
        echo ' data-reverse="' . esc_attr($reverse) . '" data-pause="' . esc_attr($pause) . '"';
        echo ($vh ? ' style="height:' . esc_attr($vh) . ';"' : '') . '>';

        echo '<div class="masscie-track">';

            // Inside your render() method, find the team members loop and update it like this:

            if (!empty($settings['team_members'])) {
                foreach ($settings['team_members'] as $index => $item) {
                    // Add render attributes for each team member
                    $this->add_render_attribute('team_member_wrapper' . $index, 'class', [
                        'masscie-team-member',
                        $settings['image_effect'] === 'grayscale' ? 'masscie-grayscale' : '',
                    ]);
                    
                    // Output the team member with attributes
					echo '<div ' . wp_kses_post( $this->get_render_attribute_string( 'team_member_wrapper' . $index ) ) . '>';
                    echo '<div class="masscie-team-member-content">';
                    
                    // Member Image
					if ( ! empty( $item['member_image']['url'] ) ) {
						echo '<div class="masscie-team-member-image">';
						echo wp_kses_post( \Elementor\Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'member_image' ) );
						echo '</div>';
					}

                    
                    // Member Name
                    if (!empty($item['member_name'])) {
                        echo '<h3 class="masscie-team-member-name">' . 
                            esc_html($item['member_name']) . 
                            '</h3>';
                    }
                    
                    // Member Role
                    if (!empty($item['member_role'])) {
                        echo '<div class="masscie-team-member-role">' . 
                            esc_html($item['member_role']) . 
                            '</div>';
                    }
                    
                    echo '</div>'; // .masscie-team-member-content
                    echo '</div>'; // .masscie-team-member
                }
            }
        echo '</div></div>'; // .masscie-track and .masscie-marquee-wrap
    }
}