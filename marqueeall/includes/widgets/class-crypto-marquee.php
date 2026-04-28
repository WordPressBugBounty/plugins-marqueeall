<?php
namespace MASSCIE\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Crypto_Marquee extends Widget_Base {

    public function get_name() {
        return 'masscie-crypto-marquee';
    }

    public function get_title() {
        return __('Crypto Price Ticker', 'marqueeall');
    }

    public function get_icon() {
        return 'eicon-price-table marquee-all-widget-icon';
    }

    public function get_categories() {
        return ['masscie-widgets'];
    }
    
    public function get_style_depends() {
        return ['masscie-style', 'masscie-crypto-style'];
    }

    public function get_script_depends() {
        return ['masscie-marquee'];
    }

    public function get_keywords() {
        return ['crypto', 'marquee', 'ticker', 'price', 'cryptocurrency', 'bitcoin', 'ethereum'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // API Settings Section
        $this->start_controls_section(
            'section_api',
            [
                'label' => __('Crypto Price Ticker API Settings', 'marqueeall'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'api_key',
            [
                'label' => __('CoinGecko API Key', 'marqueeall'),
                'type' => Controls_Manager::TEXT,
                'description' => __('Check - <a href="https://support.coingecko.com/hc/en-us/articles/21880397454233-User-Guide-How-to-use-Demo-plan-API-key" target="blank">How to retrieve CoinGecko Free API Key ?</a>', 'marqueeall'),
                'placeholder' => 'cg_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            ]
        );

        $this->add_control(
            'crypto_ids',
            [
                'label' => __('Cryptocurrencies', 'marqueeall'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_available_cryptocurrencies(),
                'description' => __('Select cryptocurrencies to display <br> <a href="https://www.coingecko.com/en/developers/dashboard" target="blank">Click here to view API usage details</a>', 'marqueeall'),
                'condition' => [
                    'api_key!' => '',
                ],
            ]
        );

        // Add this in the register_controls() method, after the crypto_ids control
        $this->add_control(
            'currency',
            [
                'label' => __('Currency', 'marqueeall'),
                'type' => Controls_Manager::SELECT,
                'default' => 'usd',
                'options' => [
                    'usd' => 'USD - US Dollar',
                    'eur' => 'EUR - Euro',
                    'gbp' => 'GBP - British Pound',
                    'jpy' => 'JPY - Japanese Yen',
                    'inr' => 'INR - Indian Rupee',
                ],
                'description' => __('Select the currency for price display', 'marqueeall'),
                'condition' => [
                    'api_key!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Marquee Settings Section (separate tab)
         */
        $this->start_controls_section(
            'settings',
            [
                'label' => esc_html__('Marquee Settings', 'marqueeall'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'orientation',
            [
                'label' => esc_html__('Orientation', 'marqueeall'),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => esc_html__('Horizontal', 'marqueeall'),
                    'vertical' => esc_html__('Vertical', 'marqueeall'),
                ],
            ]
        );

        $this->add_control(
            'vertical_height',
            [
                'label' => esc_html__('Height (vh)', 'marqueeall'),
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
                'label' => esc_html__('Reverse (flip direction)', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'pause',
            [
                'label' => esc_html__('Pause on Hover', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'speed',
            [
                'label' => esc_html__('Speed (px/s)', 'marqueeall'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default' => [
                    'size' => 60,
                    'unit' => 'px',
                ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label' => esc_html__('Gap (px)', 'marqueeall'),
                'type' => Controls_Manager::NUMBER,
                'default' => 24,
            ]
        );

        $this->add_control(
            'mask_edges',
            [
                'label' => esc_html__('Soft Edge Mask', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'reverse',
            [
                'label' => __('Reverse Direction', 'marqueeall'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Yes', 'marqueeall'),
                'label_off' => __('No', 'marqueeall'),
                'return_value' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'marqueeall'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_background',
            [
                'label' => __('Item Background', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-crypto-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-crypto-item' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .masscie-crypto-item',
            ]
        );
		// Symbol color
		$this->add_control(
			'symbol_color',
			[
				'label' => __('Symbol Color', 'marqueeall'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .masscie-crypto-symbol' => 'color: {{VALUE}};',
				],
			]
		);
		// Price color
		$this->add_control(
			'price_color',
			[
				'label' => __('Price Color', 'marqueeall'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .masscie-crypto-price' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
            'price_up_color',
            [
                'label' => __('Price Up Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-crypto-change.positive' => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'price_down_color',
            [
                'label' => __('Price Down Color', 'marqueeall'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .masscie-crypto-change.negative' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_available_cryptocurrencies() {
        $transient_key = 'masscie_available_cryptos';
        $cryptos = get_transient($transient_key);

        if (false === $cryptos) {
            $api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=100&page=1';
            $response = wp_remote_get($api_url);

            if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                $cryptos = [];

                foreach ($data as $crypto) {
                    $cryptos[$crypto['id']] = $crypto['name'] . ' (' . strtoupper($crypto['symbol']) . ')';
                }

                set_transient($transient_key, $cryptos, HOUR_IN_SECONDS);
            } else {
                // Fallback options if API fails
                $cryptos = [];
            }
        }

        return $cryptos;
    }
    
    private function validate_coingecko_api_key($api_key) {

            if (empty($api_key)) {
                return new \WP_Error(
                    'missing_key',
                    __('CoinGecko API key is missing.', 'marqueeall')
                );
            }

            // Cache validation result to avoid repeated validation calls
            $cached = get_transient('masscie_cg_key_valid');
            if ($cached === true) {
                return true;
            }

            $api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=1&page=1';

            $response = wp_remote_get($api_url, [
                'timeout' => 20,
                'headers' => [
                    'Accept' => 'application/json',
                    'x-cg-demo-api-key' => $api_key,
                ],
            ]);

            if (is_wp_error($response)) {
                return $response;
            }

            $status_code = wp_remote_retrieve_response_code($response);
            $body = json_decode(wp_remote_retrieve_body($response), true);

            if ($status_code !== 200) {
                $message = __('Invalid CoinGecko API key.', 'marqueeall');

                if (!empty($body['error'])) {
                    $message = $body['error'];
                }

                return new \WP_Error('invalid_key', $message);
            }

            // Cache validation success for 1 hour
            set_transient('masscie_cg_key_valid', true, HOUR_IN_SECONDS);

            return true;
        }



    private function get_crypto_data($crypto_ids, $api_key = '', $currency = 'usd') {

        if (!empty($api_key)) {
            $validation = $this->validate_coingecko_api_key($api_key);
            if (is_wp_error($validation)) {
                return $validation;
            }
        }
        if (empty($crypto_ids)) {
            return new \WP_Error('no_cryptos', __('No cryptocurrencies selected.', 'marqueeall'));
        }

        // Include currency in the transient key to ensure cache is currency-specific
        $transient_key = 'masscie_crypto_data_' . md5(implode(',', $crypto_ids) . $api_key . $currency);
        $cached_data = get_transient($transient_key);

        if (false !== $cached_data) {
            return $cached_data;
        }

        $api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=' . urlencode($currency) . 
                '&ids=' . urlencode(implode(',', $crypto_ids)) . 
                '&price_change_percentage=24h';

        $args = [
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ];

        if (!empty($api_key)) {
            $args['headers']['x-cg-demo-api-key'] = $api_key;
        }

        $response = wp_remote_get($api_url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (200 !== $status_code) {
            $error_message = __('Invalid API or Failed to fetch cryptocurrency data.', 'marqueeall');
            if (!empty($body['error'])) {
                $error_message = $body['error'];
            }
            return new \WP_Error('api_error', $error_message);
        }

        // Cache the results for 5 minutes
        set_transient($transient_key, $body, 300);
        return $body;
    }

    private function get_currency_symbol($currency) {
    $symbols = [
        'usd' => '$',
        'eur' => '€',
        'gbp' => '£',
        'jpy' => '¥',
        'inr' => '₹',
    ];
        
        return $symbols[strtolower($currency)] ?? strtoupper($currency);
    }

    /**
     * Render widget output on frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $api_key = !empty($settings['api_key']) ? $settings['api_key'] : '';
        $crypto_ids = !empty($settings['crypto_ids']) ? $settings['crypto_ids'] : [];
        $currency = !empty($settings['currency']) ? $settings['currency'] : 'usd';

        // Check if API key is provided
        if (empty($api_key)) {
            echo '<div class="elementor-alert elementor-alert-warning">' . 
                 esc_html__('Please enter a valid CoinGecko API key in the widget settings.', 'marqueeall') . 
                 '</div>';
            return;
        }

        // Check if any cryptocurrencies are selected
        if (empty($crypto_ids)) {
            echo '<div class="elementor-alert elementor-alert-warning">' . 
                 esc_html__('Please select at least one cryptocurrency to display.', 'marqueeall') . 
                 '</div>';
            return;
        }

            // Get crypto data with API key validation
            $crypto_data = $this->get_crypto_data($crypto_ids, $api_key, $currency);

        if (is_wp_error($crypto_data)) {
            $error_message = $crypto_data->get_error_message();
            
            // More user-friendly error messages
            if (strpos($error_message, '401') !== false) {
                $error_message = __('Invalid API key. Please check your CoinGecko API key.', 'marqueeall');
            } elseif (strpos($error_message, '429') !== false) {
                $error_message = __('API rate limit exceeded. Please try again later.', 'marqueeall');
            } elseif (strpos($error_message, 'cURL error 28') !== false) {
                $error_message = __('Connection timeout. Please check your internet connection.', 'marqueeall');
            }
            
            echo '<div class="elementor-alert elementor-alert-danger">' . 
                 esc_html($error_message) . 
                 '</div>';
            return;
        }

        // Get marquee settings
        $speed = isset($settings['speed']['size']) ? floatval($settings['speed']['size']) : 60;
        $gap = !empty($settings['gap']) ? intval($settings['gap']) : 24;
        $orientation = isset($settings['orientation']) ? $settings['orientation'] : 'horizontal';
        $reverse = (!empty($settings['reverse']) && 'yes' === $settings['reverse']) ? 'yes' : 'no';
        $pause = (!empty($settings['pause']) && 'yes' === $settings['pause']) ? 'yes' : 'no';
        $mask = (!empty($settings['mask_edges']) && 'yes' === $settings['mask_edges']);
        $vh = ('vertical' === $orientation && !empty($settings['vertical_height']['size'])) 
            ? $settings['vertical_height']['size'] . 'vh' : '';
        
        // Output the marquee
        ?>
        <div class="masscie-marquee-wrap masscie-crypto-marquee <?php echo ('vertical' === $orientation) ? 'masscie-vertical' : ''; ?> <?php echo $mask ? 'masscie-mask-edges' : ''; ?>"
             data-speed="<?php echo esc_attr($speed); ?>"
             data-gap="<?php echo esc_attr($gap); ?>"
             data-reverse="<?php echo esc_attr($reverse); ?>"
             data-pause="<?php echo esc_attr($pause); ?>"
             <?php echo $vh ? 'style="height:' . esc_attr($vh) . ';"' : ''; ?>>
            <div class="masscie-track">
                <?php foreach ($crypto_data as $crypto) : ?>
                    <div class="masscie-crypto-item">
                        <?php if (!empty($crypto['image'])) : ?>
                            <img src="<?php echo esc_url($crypto['image']); ?>" 
                                 alt="<?php echo esc_attr($crypto['name']); ?>" 
                                 class="masscie-crypto-icon"
                                 loading="lazy">
                        <?php endif; ?>
                        <span class="masscie-crypto-name"><?php echo esc_html($crypto['name']); ?></span>
                        <span class="masscie-crypto-symbol">(<?php echo esc_html(strtoupper($crypto['symbol'])); ?>)</span>
                        <span class="masscie-crypto-price"><?php 
                            echo esc_html($this->get_currency_symbol($currency) . number_format($crypto['current_price'], 2)); 
                        ?></span>
                        <?php 
                        $change_class = $crypto['price_change_percentage_24h'] >= 0 ? 'positive' : 'negative';
                        $change_icon = $crypto['price_change_percentage_24h'] >= 0 ? '▲' : '▼';
                        ?>
                        <span class="masscie-crypto-change <?php echo esc_attr($change_class); ?>">
                            <?php echo esc_html($change_icon . ' ' . number_format(abs($crypto['price_change_percentage_24h']), 2) . '%'); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}