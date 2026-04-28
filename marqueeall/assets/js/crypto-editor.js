(function($) {
    'use strict';

    const MasscieCryptoEditor = {
        init: function() {
            if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
                console.warn('jQuery or Select2 not loaded');
                return;
            }

            this.initializeSelect2();
            this.setupEventListeners();
        },

        initializeSelect2: function() {
            const $select = $('.elementor-control-crypto_ids select');
            if (!$select.length) return;

            try {
                $select.select2({
                    width: '100%',
                    placeholder: 'Select cryptocurrencies',
                    allowClear: true
                });
            } catch (error) {
                console.error('Error initializing Select2:', error);
            }
        },

        setupEventListeners: function() {
            // Handle API key changes to refresh the crypto list
            $(document).on('change', '.elementor-control-api_key input', function() {
                const $apiKey = $(this).val().trim();
                if ($apiKey) {
                    const $cryptoSelect = $(this).closest('.elementor-control').siblings('.elementor-control-crypto_ids').find('select');
                    MasscieCryptoEditor.validateApiKey($apiKey, $cryptoSelect, elementor.getPanelView().getCurrentPageView());
                }
            });
        },
        
        validateApiKey: function(apiKey, $cryptoSelect, panel) {
            if (!apiKey) {
                this.showApiKeyError(panel, 'Please enter a CoinGecko API key');
                $cryptoSelect.prop('disabled', true);
                return;
            }
            
            // CoinGecko API keys start with 'cg_' and are at least 40 chars
            if (!/^cg_[a-zA-Z0-9]{36,}$/.test(apiKey)) {
                this.showApiKeyError(panel, 'Invalid API key format. It should start with cg_ and be at least 40 characters.');
                $cryptoSelect.prop('disabled', true);
                return;
            }
            
            // Show loading
            this.showApiKeyLoading(panel, 'Validating API key...');
            
            // Test API key with a simple request
            $.ajax({
                url: 'https://api.coingecko.com/api/v3/ping',
                method: 'GET',
                headers: { 'x-cg-demo-api-key': apiKey },
                success: () => {
                    this.showApiKeySuccess(panel, 'API key is valid');
                    $cryptoSelect.prop('disabled', false);
                    
                    // Refresh crypto list with the valid API key
                    this.refreshCryptoList(apiKey, $cryptoSelect);
                },
                error: (xhr) => {
                    if (xhr.status === 401) {
                        this.showApiKeyError(panel, 'Invalid API key. Please check your key.');
                    } else if (xhr.status === 429) {
                        this.showApiKeyError(panel, 'API rate limit exceeded. Please try again later.');
                    } else {
                        this.showApiKeyError(panel, 'Failed to validate API key. Please try again.');
                    }
                    $cryptoSelect.prop('disabled', true);
                }
            });
        },
        
        showApiKeyLoading: function(panel, message) {
            this.showApiKeyMessage(panel, message, 'elementor-control-field-description');
        },
        
        showApiKeySuccess: function(panel, message) {
            this.showApiKeyMessage(panel, message, 'elementor-control-field-description elementor-control-field-description-success');
        },
        
        showApiKeyError: function(panel, message) {
            this.showApiKeyMessage(panel, message, 'elementor-control-field-description elementor-control-field-description-error');
        },
        
        showApiKeyMessage: function(panel, message, className) {
            let $message = panel.$el.find('.api-key-validation-message');
            
            if (!$message.length) {
                $message = $('<div>', {
                    class: 'api-key-validation-message ' + className,
                    css: { marginTop: '5px' }
                });
                panel.$el.find('.elementor-control-api_key').append($message);
            }
            
            $message
                .removeClass('elementor-control-field-description elementor-control-field-description-success elementor-control-field-description-error')
                .addClass(className)
                .text(message);
        },
        
        refreshCryptoList: function(apiKey, $select) {
            if (!$select.length) return;
            
            const $spinner = $('<span>', {
                class: 'elementor-control-field-description',
                text: 'Loading cryptocurrencies...',
                css: { display: 'block', marginTop: '5px' }
            });
            
            $select.after($spinner);
            
            $.ajax({
                url: 'https://api.coingecko.com/api/v3/coins/markets',
                method: 'GET',
                data: {
                    vs_currency: 'usd',
                    order: 'market_cap_desc',
                    per_page: 100, // Increased to show more options
                    page: 1,
                    sparkline: false,
                    price_change_percentage: '24h'
                },
                headers: { 'x-cg-demo-api-key': apiKey },
                success: function(response) {
                    if (Array.isArray(response) && response.length > 0) {
                        MasscieCryptoEditor.populateCryptoSelect($select, response);
                    } else {
                        MasscieCryptoEditor.showApiKeyError(
                            elementor.getPanelView().getCurrentPageView(),
                            'No cryptocurrencies found. Please try again.'
                        );
                        $select.prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching crypto list:', error);
                    let errorMessage = 'Failed to load cryptocurrencies. ';
                    
                    if (xhr.status === 401) {
                        errorMessage = 'Invalid API key. Please check your key.';
                    } else if (xhr.status === 429) {
                        errorMessage = 'API rate limit exceeded. Please try again later.';
                    } else if (error === 'timeout') {
                        errorMessage = 'Connection timeout. Please check your internet connection.';
                    }
                    
                    MasscieCryptoEditor.showApiKeyError(
                        elementor.getPanelView().getCurrentPageView(),
                        errorMessage
                    );
                    
                    $select.prop('disabled', true);
                    
                    // Don't fall back to default list - we want to enforce API key validation
                    $select.empty().append(
                        $('<option>', {
                            value: '',
                            text: 'Failed to load cryptocurrencies',
                            disabled: true,
                            selected: true
                        })
                    );
                },
                complete: function() {
                    $spinner.remove();
                },
                timeout: 10000 // 10 second timeout
            });
        },
        
        populateCryptoSelect: function($select, cryptos) {
            if (!$select.length) return;

            try {
                // Store current values
                const currentValues = $select.val() || [];
                
                // Clear and repopulate
                $select.empty().append(
                    $('<option>', {
                        value: '',
                        text: 'Select Cryptocurrencies'
                    })
                );

                // Add crypto options
                cryptos.forEach(function(crypto) {
                    $select.append(
                        $('<option>', {
                            value: crypto.id,
                            text: crypto.name + ' (' + crypto.symbol.toUpperCase() + ')',
                            selected: currentValues.includes(crypto.id)
                        })
                    );
                });

                // Reinitialize Select2
                if ($.fn.select2) {
                    $select.select2('destroy').select2({
                        width: '100%',
                        placeholder: 'Select cryptocurrencies',
                        allowClear: true
                    });
                }
            } catch (error) {
                console.error('Error populating select:', error);
            }
        },
        
        setupCryptoControls: function(panel) {
            // Handle API key changes to refresh the crypto list
            $(document).on('change', '.elementor-control-api_key input', function() {
                const $apiKey = $(this).val().trim();
                if ($apiKey) {
                    MasscieCryptoEditor.validateApiKey($apiKey, panel.$el.find('.elementor-control-crypto_ids select'), panel);
                }
            });
        }
    };

    // Initialize when Elementor is ready
    if (typeof elementor !== 'undefined') {
        elementor.hooks.addAction('panel/init', function() {
            MasscieCryptoEditor.init();
            
            // Also initialize when a new widget is added
            elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
                if (model.attributes.widgetType === 'crypto_marquee') {
                    const $apiKeyInput = panel.$el.find('input[data-setting="api_key"]');
                    const $cryptoSelect = panel.$el.find('.elementor-control-crypto_ids select');
                    
                    if ($apiKeyInput.length && $cryptoSelect.length) {
                        // Disable crypto select initially
                        $cryptoSelect.prop('disabled', true);
                        
                        // Validate API key when it changes
                        $apiKeyInput.off('input').on('input', _.debounce(function() {
                            const apiKey = $(this).val().trim();
                            MasscieCryptoEditor.validateApiKey(apiKey, $cryptoSelect, panel);
                        }, 500));
                        
                        // Initial validation if API key exists
                        const initialApiKey = $apiKeyInput.val().trim();
                        if (initialApiKey) {
                            MasscieCryptoEditor.validateApiKey(initialApiKey, $cryptoSelect, panel);
                        }
                    }
                }
            });
        });
    } else {
        $(window).on('load', function() {
            MasscieCryptoEditor.init();
        });
    }

})(jQuery);
