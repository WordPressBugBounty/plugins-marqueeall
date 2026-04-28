<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Image Marquee Widget for Elementor.
 *
 * @since 1.1.0
 */
class Image_Marquee extends Widget_Base {

    public function get_name() {
        return 'masscie-image-marquee';
    }

    public function get_title() {
        return esc_html__( 'Image Marquee', 'marqueeall' );
    }

    public function get_icon() {
        return 'eicon-slider-push marquee-all-widget-icon';
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

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        /**
         * Content Section (Images & Links)
         */
        $this->start_controls_section(
            'content',
            [ 'label' => esc_html__( 'Images', 'marqueeall' ) ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image',
            [
                'label'   => esc_html__( 'Image', 'marqueeall' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label'        => esc_html__( 'Custom Link', 'marqueeall' ),
                'type'         => Controls_Manager::URL,
                'show_external'=> true,
                'default'      => [
                    'is_external' => false,
                    'nofollow'    => false,
                ],
            ]
        );

        $this->add_control(
            'items',
            [
                'label'   => esc_html__( 'Items', 'marqueeall' ),
                'type'    => Controls_Manager::REPEATER,
                'fields'  => $repeater->get_controls(),
                'default' => array_fill( 0, 3, [
                    'image' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
                ] ),
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

        // Image size control.
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'thumbnail',
                'default'   => 'medium',
                'separator' => 'none',
            ]
        );

        // Image width & height.
        $this->add_control(
            'image_width',
            [
                'label'      => esc_html__( 'Image Width', 'marqueeall' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 20, 'max' => 800 ],
                    '%'  => [ 'min' => 5, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .masscie-item img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label'      => esc_html__( 'Image Height', 'marqueeall' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range'      => [
                    'px' => [ 'min' => 20, 'max' => 800 ],
                    '%'  => [ 'min' => 5, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .masscie-item img' => 'height: {{SIZE}}{{UNIT}}; object-fit: contain;',
                ],
            ]
        );

        // Other settings.
        $this->add_control(
            'link_to',
            [
                'label'   => esc_html__( 'Link To', 'marqueeall' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'   => esc_html__( 'None', 'marqueeall' ),
                    'file'   => esc_html__( 'Media File (Lightbox)', 'marqueeall' ),
                    'custom' => esc_html__( 'Custom URL', 'marqueeall' ),
                ],
            ]
        );

        $this->add_control(
            'open_lightbox',
            [
                'label'     => esc_html__( 'Open in Lightbox', 'marqueeall' ),
                'type'      => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'   => 'yes',
                'condition' => [ 'link_to' => 'file' ],
            ]
        );

        $this->add_control(
            'lazy',
            [
                'label'        => esc_html__( 'Lazy Load Images', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();

        /**
         * Marquee Settings Section (separate tab)
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
                'label'        => esc_html__( 'Reverse (flip direction)', 'marqueeall' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
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
                'default'      => '',
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on frontend.
     */
    protected function render() {
        $s = $this->get_settings_for_display();

        $speed       = isset( $s['speed']['size'] ) ? floatval( $s['speed']['size'] ) : 60;
        $gap         = ! empty( $s['gap'] ) ? intval( $s['gap'] ) : 24;
        $orientation = $s['orientation'] ?? 'horizontal';
        $reverse     = ( ! empty( $s['reverse'] ) && 'yes' === $s['reverse'] ) ? 'yes' : 'no';
        $pause       = ( ! empty( $s['pause'] ) && 'yes' === $s['pause'] ) ? 'yes' : 'no';
        $mask        = ( ! empty( $s['mask_edges'] ) && 'yes' === $s['mask_edges'] );
        $link_to     = $s['link_to'] ?? 'none';
        $open_lightbox = ( ! empty( $s['open_lightbox'] ) && 'yes' === $s['open_lightbox'] );
        $lazy        = ( ! empty( $s['lazy'] ) && 'yes' === $s['lazy'] );
        $vh          = ( 'vertical' === $orientation && ! empty( $s['vertical_height']['size'] ) )
            ? $s['vertical_height']['size'] . 'vh' : '';

        ?>
        <div class="masscie-marquee-wrap <?php echo ( 'vertical' === $orientation ) ? 'masscie-vertical' : ''; ?> <?php echo $mask ? 'masscie-mask-edges' : ''; ?>"
             data-speed="<?php echo esc_attr( $speed ); ?>"
             data-gap="<?php echo esc_attr( $gap ); ?>"
             data-reverse="<?php echo esc_attr( $reverse ); ?>"
             data-pause="<?php echo esc_attr( $pause ); ?>"
             <?php echo $vh ? 'style="height:' . esc_attr( $vh ) . ';"' : ''; ?>>
            <div class="masscie-track">
                <?php
                if ( ! empty( $s['items'] ) ) {
                    foreach ( $s['items'] as $item ) {
                        $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                        if ( $lazy ) {
                            $image_html = preg_replace( '/<img(\s+)/i', '<img loading="lazy" $1', $image_html );
                        }

                        $content = '<div class="masscie-item">' . $image_html . '</div>';

                        if ( 'file' === $link_to && ! empty( $item['image']['url'] ) ) {
                            printf(
                                '<a data-elementor-open-lightbox="%s" href="%s" class="masscie-link">%s</a>',
                                esc_attr( $open_lightbox ? 'yes' : 'no' ),
                                esc_url( $item['image']['url'] ),
                                wp_kses_post( $content )
                            );
                        } elseif ( 'custom' === $link_to && ! empty( $item['link']['url'] ) ) {
                            $attrs  = 'href="' . esc_url( $item['link']['url'] ) . '"';
                            $attrs .= ! empty( $item['link']['is_external'] ) ? ' target="_blank"' : '';
                            $attrs .= ! empty( $item['link']['nofollow'] ) ? ' rel="nofollow"' : '';
                            printf(
                                '<a %s class="masscie-link">%s</a>',
                                $attrs,
                                wp_kses_post( $content )
                            );
                        } else {
                            echo wp_kses_post( $content );
                        }
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
}
