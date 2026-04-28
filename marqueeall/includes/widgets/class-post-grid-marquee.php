<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit;

class Post_Grid_Marquee extends Widget_Base {

    public function get_name() {
        return 'post-grid-marquee';
    }

    public function get_title() {
        return esc_html__( 'Post Grid Marquee', 'marqueeall' );
    }

    public function get_icon() {
        return 'eicon-posts-carousel marquee-all-widget-icon';
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
         * ----------------------------
         * Query Settings
         * ----------------------------
         */
        $this->start_controls_section(
            'query_section',
            [ 'label' => esc_html__( 'Query', 'marqueeall' ) ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label'   => esc_html__( 'Number of Posts', 'marqueeall' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'post_categories',
            [
                'label'       => esc_html__( 'Select Categories', 'marqueeall' ),
                'type'        => Controls_Manager::SELECT2,
                'options'     => $this->get_all_categories(),
                'multiple'    => true,
                'label_block' => true,
            ]
        );

        $this->end_controls_section();

        /**
         * ----------------------------
         * Post Content Settings
         * ----------------------------
         */
        $this->start_controls_section(
            'post_content',
            [ 'label' => esc_html__( 'Post Content', 'marqueeall' ) ]
        );

        $this->add_control(
            'show_featured_image',
            [
                'label'        => esc_html__( 'Show Featured Image', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'marqueeall' ),
                'label_off'    => esc_html__( 'Hide', 'marqueeall' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label'        => esc_html__( 'Show Author', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label'        => esc_html__( 'Show Date', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label'   => esc_html__( 'Excerpt Word Limit', 'marqueeall' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 15,
            ]
        );

        $this->add_control(
            'read_more_text',
            [
                'label'   => esc_html__( 'Read More Text', 'marqueeall' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Read More', 'marqueeall' ),
            ]
        );

        $this->end_controls_section();

        /**
         * ----------------------------
         * Marquee Settings
         * ----------------------------
         */
        $this->start_controls_section(
            'settings',
            [ 'label' => esc_html__( 'Marquee Settings', 'marqueeall' ) ]
        );

        $this->add_control(
            'orientation',
            [
                'label'   => esc_html__( 'Orientation', 'marqueeall' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => esc_html__( 'Horizontal', 'marqueeall' ),
                    'vertical'   => esc_html__( 'Vertical', 'marqueeall' ),
                ],
            ]
        );

        $this->add_control(
            'vertical_height',
            [
                'label'     => esc_html__( 'Height (vh)', 'marqueeall' ),
                'type'      => Controls_Manager::SLIDER,
                'size_units'=> [ 'vh' ],
                'range'     => [ 'vh' => [ 'min' => 20, 'max' => 100 ] ],
                'default'   => [ 'size' => 60, 'unit' => 'vh' ],
                'condition' => [ 'orientation' => 'vertical' ],
            ]
        );

        $this->add_control(
            'reverse',
            [
                'label'        => esc_html__( 'Reverse Direction', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'pause',
            [
                'label'        => esc_html__( 'Pause on Hover', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label'      => esc_html__( 'Speed (px/s)', 'marqueeall' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [ 'px' => [ 'min' => 10, 'max' => 400 ] ],
                'default'    => [ 'size' => 60, 'unit' => 'px' ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label'   => esc_html__( 'Gap (px)', 'marqueeall' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 24,
            ]
        );

        $this->add_control(
            'mask_edges',
            [
                'label'        => esc_html__( 'Soft Edge Mask', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Keep all your existing style sections unchanged
        // Featured Image, Title, Meta, Excerpt, Read More button styling remains as before
   
        /**
         * ----------------------------
         * Style: Featured Image
         * ----------------------------
         */
        $this->start_controls_section(
            'style_thumb',
            [
                'label' => esc_html__( 'Featured Image', 'marqueeall' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'thumb_width',
            [
                'label' => esc_html__( 'Width', 'marqueeall' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 2000],
                    '%'  => ['min' => 10, 'max' => 100],
                    'em' => ['min' => 1, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pgm-thumb img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'thumb_height',
            [
                'label' => esc_html__( 'Height', 'marqueeall' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 2000],
                    '%'  => ['min' => 10, 'max' => 100],
                    'em' => ['min' => 1, 'max' => 50],
                ],
                'selectors' => [
                    '{{WRAPPER}} .pgm-thumb img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'thumb_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'marqueeall' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pgm-thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'thumb_shadow',
                'selector' => '{{WRAPPER}} .pgm-thumb img',
            ]
        );
        $this->end_controls_section();
        
        /**
         * ----------------------------
         * Style: Title
         * ----------------------------
         */
        $this->start_controls_section(
            'style_title',
            [
                'label' => esc_html__( 'Post Title', 'marqueeall' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Text Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-title a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'title_hover_color',
            [
                'label' => esc_html__( 'Hover Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-title a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typo',
                'selector' => '{{WRAPPER}} .pgm-title a',
            ]
        );
        $this->end_controls_section();


        /**
         * ----------------------------
         * Style: Meta
         * ----------------------------
         */
        $this->start_controls_section(
            'style_meta',
            [
                'label' => esc_html__( 'Meta (Author & Date)', 'marqueeall' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'meta_color',
            [
                'label' => esc_html__( 'Text Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-meta, {{WRAPPER}} .pgm-meta i' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'meta_typo',
                'selector' => '{{WRAPPER}} .pgm-meta',
            ]
        );
        $this->end_controls_section();


        /**
         * ----------------------------
         * Style: Excerpt
         * ----------------------------
         */
        $this->start_controls_section(
            'style_excerpt',
            [
                'label' => esc_html__( 'Excerpt', 'marqueeall' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'excerpt_color',
            [
                'label' => esc_html__( 'Text Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-excerpt' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typo',
                'selector' => '{{WRAPPER}} .pgm-excerpt',
            ]
        );
        $this->end_controls_section();


        /**
         * ----------------------------
         * Style: Read More Button
         * ----------------------------
         */
        $this->start_controls_section(
            'style_button',
            [
                'label' => esc_html__( 'Read More Button', 'marqueeall' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'btn_color',
            [
                'label' => esc_html__( 'Text Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-readmore' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'btn_bg',
            [
                'label' => esc_html__( 'Background Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-readmore' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'btn_hover_color',
            [
                'label' => esc_html__( 'Hover Text Color', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-readmore:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'btn_hover_bg',
            [
                'label' => esc_html__( 'Hover Background', 'marqueeall' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pgm-readmore:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'btn_border',
                'selector' => '{{WRAPPER}} .pgm-readmore',
            ]
        );
        $this->add_responsive_control(
            'btn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'marqueeall' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .pgm-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'btn_typo',
                'selector' => '{{WRAPPER}} .pgm-readmore',
            ]
        );
        $this->end_controls_section();
    }
    /**
     * Render Output
     */
    protected function render() {
        $s = $this->get_settings_for_display();
        $unique_id = 'masscie-' . esc_attr( $this->get_id() );

        $args = [
            'post_type'      => 'post',
            'posts_per_page' => ! empty( $s['posts_per_page'] ) ? intval( $s['posts_per_page'] ) : 6,
            'post_status'    => 'publish',
        ];

        if ( ! empty( $s['post_categories'] ) ) {
            $args['category__in'] = $s['post_categories'];
        }

        $q = new WP_Query( $args );

        $speed       = isset( $s['speed']['size'] ) ? floatval( $s['speed']['size'] ) : 60;
        $gap         = ! empty( $s['gap'] ) ? intval( $s['gap'] ) : 24;
        $orientation = $s['orientation'] ?? 'horizontal';
        $reverse     = ( ! empty( $s['reverse'] ) && 'yes' === $s['reverse'] ) ? 'yes' : 'no';
        $pause       = ( ! empty( $s['pause'] ) && 'yes' === $s['pause'] ) ? 'yes' : 'no';
        $mask        = ( ! empty( $s['mask_edges'] ) && 'yes' === $s['mask_edges'] );
        $vh          = ( 'vertical' === $orientation && ! empty( $s['vertical_height']['size'] ) )
            ? $s['vertical_height']['size'] . 'vh' : '';

        ?>
        <div class="masscie-marquee-wrap <?php echo esc_attr( $unique_id ); ?> <?php echo ( 'vertical' === $orientation ) ? 'masscie-vertical' : ''; ?> <?php echo $mask ? 'masscie-mask-edges' : ''; ?>"
             data-speed="<?php echo esc_attr( $speed ); ?>"
             data-gap="<?php echo esc_attr( $gap ); ?>"
             data-reverse="<?php echo esc_attr( $reverse ); ?>"
             data-pause="<?php echo esc_attr( $pause ); ?>"
             <?php echo $vh ? 'style="height:' . esc_attr( $vh ) . ';"' : ''; ?>>

            <div class="masscie-track">
                <?php if ( $q->have_posts() ) : ?>
                    <?php while ( $q->have_posts() ) : $q->the_post(); ?>
                        <div class="masscie-item pgm-post-card">
                            <div class="pgm-content">
                                <?php if ( 'yes' === $s['show_featured_image'] && has_post_thumbnail() ) : ?>
                                    <div class="pgm-thumb">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail( 'medium' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="pgm-info">
                                    <h3 class="pgm-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <?php if ( 'yes' === $s['show_author'] || 'yes' === $s['show_date'] ) : ?>
                                        <div class="pgm-meta">
                                            <?php if ( 'yes' === $s['show_author'] ) : ?>
                                                <span class="pgm-author"><i class="fas fa-user-circle"></i> <?php echo esc_html( get_the_author() ); ?></span>
                                            <?php endif; ?>
                                            <?php if ( 'yes' === $s['show_date'] ) : ?>
                                                <span class="pgm-date"><i class="far fa-calendar-alt"></i> <?php echo esc_html( get_the_date() ); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

									  <div class="pgm-excerpt">
										<?php echo esc_html( wp_trim_words( get_the_excerpt(), intval( $s['excerpt_length'] ) ) ); ?>
									  </div>


                                    <?php if ( ! empty( $s['read_more_text'] ) ) : ?>
                                        <a href="<?php the_permalink(); ?>" class="pgm-readmore"><?php echo esc_html( $s['read_more_text'] ); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private function get_all_categories() {
        $cats = get_categories( [ 'hide_empty' => false ] );
        $options = [];
        foreach ( $cats as $cat ) {
            $options[ $cat->term_id ] = $cat->name;
        }
        return $options;
    }
}
