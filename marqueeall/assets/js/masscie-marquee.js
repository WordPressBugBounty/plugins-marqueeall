(function ($) {
	'use strict';

	/**
	 * Text Scramble
	 */
	function initTextScramble($scope = null) {

		const elements = $scope
			? $scope[0].querySelectorAll('.masscie-scramble-text:not([data-scramble-initialized])')
			: document.querySelectorAll('.masscie-scramble-text:not([data-scramble-initialized])');

		elements.forEach(function (el) {

			el.setAttribute('data-scramble-initialized', 'true');

			if (typeof TextScramble !== 'undefined') {
				new TextScramble(el);
			}
		});
	}

	/**
	 * Marquee
	 */
	function initMarquee($wrap) {

		var prevState = $wrap.data('masscie-state');

		if (prevState) {
			try {
				if (prevState.ro) {
					prevState.ro.disconnect();
				}
			} catch (e) {}

			$wrap.off('.massciePause masscieResize masscieImg');
			$wrap.removeData('masscie-state');
		}

		var speed = parseFloat(
			$wrap.data('speed') ||
			$wrap.data('animation-speed') ||
			60
		);

		if (!isFinite(speed) || speed <= 0) {
			speed = 60;
		}

		var reverse =
			$wrap.data('reverse') === true ||
			$wrap.data('reverse') === 'yes' ||
			$wrap.hasClass('masscie-reverse');

		var pause =
			$wrap.data('pause') !== 'no' &&
			$wrap.data('pause-on-hover') !== 'no';

		var gap = parseInt($wrap.data('gap') || 24, 10);

		var isVertical =
			$wrap.hasClass('masscie-vertical') ||
			$wrap.data('vertical') === 'yes';

		var $track = $wrap.find('.masscie-track');

		$wrap.css('--masscie-gap', gap + 'px');

		var $originalItems = $track.children().clone(true, true);

		function measureGroupPx($group) {
			var rect = $group[0].getBoundingClientRect();
			return Math.max(
				0,
				Math.round(isVertical ? rect.height : rect.width)
			);
		}

		function build() {

			$track.empty();

			var $g1 = $('<div class="masscie-group"></div>');
			var $g2 = $('<div class="masscie-group"></div>');

			$g1.append($originalItems.clone(true, true));
			$g2.append($originalItems.clone(true, true));

			$track.append($g1).append($g2);

			if (isVertical) {
				$track.addClass('masscie-group-vertical');
			} else {
				$track.removeClass('masscie-group-vertical');
			}

			var wrapSize = isVertical
				? Math.round($wrap.innerHeight())
				: Math.round($wrap.innerWidth());

			var groupPx = measureGroupPx($g1);

			if (!groupPx) {

				$g1.children().each(function () {

					var $el = $(this);

					if ($el.css('display') === 'none') {
						$el.css({
							display: isVertical ? 'block' : 'inline-block',
							visibility: 'hidden'
						});
					}
				});

				groupPx = measureGroupPx($g1);
			}

			if (!groupPx || groupPx <= 0) {
				//setTimeout(build, 80);
				return;
			}

			while (groupPx < wrapSize) {

				$g1.append($originalItems.clone(true, true));
				$g2.append($originalItems.clone(true, true));

				groupPx = measureGroupPx($g1);

				if (groupPx > 50000) {
					break;
				}
			}

			var duration = groupPx / Math.max(1, speed);

			if (Math.abs(duration - 3) < 0.01) {
				duration += 0.01;
			}

			$wrap.css('--masscie-duration', duration + 's');

			$track.find('.masscie-group').css({
				'animation-duration': duration + 's',
				'animation-play-state': 'paused'
			});

			if (reverse) {
				$track.find('.masscie-group').css(
					'animation-direction',
					'reverse'
				);
			} else {
				$track.find('.masscie-group').css(
					'animation-direction',
					''
				);
			}

			$wrap.off('.massciePause');

			if (pause) {

				$wrap.on('mouseenter.massciePause', function () {
					$track.find('.masscie-group').css(
						'animation-play-state',
						'paused'
					);
				});

				$wrap.on('mouseleave.massciePause', function () {
					$track.find('.masscie-group').css(
						'animation-play-state',
						'running'
					);
				});
			}

			var $media = $track.find('img, video');
			var loadedCount = 0;

			function tryStart() {

				loadedCount++;

				if (loadedCount >= $media.length) {

					requestAnimationFrame(function () {
						$track.find('.masscie-group').css(
							'animation-play-state',
							'running'
						);
					});
				}
			}

			if ($media.length === 0) {

				$track.find('.masscie-group').css(
					'animation-play-state',
					'running'
				);

			} else {

				$media.each(function () {

					var tag = this.tagName.toLowerCase();

					if (tag === 'img') {

						if (this.complete) {
							tryStart();
						} else {
							$(this).one('load', tryStart);
						}

					} else if (tag === 'video') {

						if (this.readyState >= 2) {
							tryStart();
						} else {
							$(this).one('loadeddata', tryStart);
						}
					}
				});
			}

			var state = $wrap.data('masscie-state') || {};
			state.groupPx = groupPx;
			state.duration = duration;

			$wrap.data('masscie-state', state);
		}

		build();

		if ('ResizeObserver' in window) {

			var ro = new ResizeObserver(function () {
				build();
			});

			ro.observe($wrap[0]);

			$track.find('img').each(function () {
				try {
					ro.observe(this);
				} catch (e) {}
			});

			var state = $wrap.data('masscie-state') || {};
			state.ro = ro;

			$wrap.data('masscie-state', state);

		} else {

			$(window).on('resize.masscie', build);

			var state2 = $wrap.data('masscie-state') || {};
			state2.ro = null;

			$wrap.data('masscie-state', state2);
		}
	}

	/**
	 * FAQ Marquee
	 *
	 * The marquee track duplicates every chip into two scrolling groups,
	 * so a click handler is delegated off the outer wrapper (rather than
	 * bound per-chip) and every clone sharing the same question is kept
	 * in sync when one of them is opened, closed, or replaced.
	 */
	function initFaqMarquee($scope = null) {

		var $context = $scope || $(document);

		$context.find('.masscie-faq-marquee:not([data-faq-initialized])').each(function () {

			var $widget = $(this);

			$widget.attr('data-faq-initialized', 'true');

			var $wrap = $widget.find('.masscie-faq-track-wrap');
			var $panel = $widget.find('.masscie-faq-panel');
			var $panelQuestion = $panel.find('.masscie-faq-panel-question');
			var $panelAnswer = $panel.find('.masscie-faq-panel-answer');
			var singleOpen = $widget.data('single-open') !== 'no';
			var openQuestion = null;

			function pauseTrack() {
				$wrap.find('.masscie-group').css('animation-play-state', 'paused');
			}

			function resumeTrack() {
				if (!openQuestion) {
					$wrap.find('.masscie-group').css('animation-play-state', 'running');
				}
			}

			function syncActiveState() {
				$widget.find('.masscie-faq-chip').each(function () {
					var $chip = $(this);
					var isActive = openQuestion !== null &&
						$chip.data('faq-question') === openQuestion;

					$chip.toggleClass('masscie-faq-active', isActive);
					$chip.attr('aria-expanded', isActive ? 'true' : 'false');
				});
			}

			function openChip($chip) {
				openQuestion = $chip.data('faq-question');

				$panelQuestion.text($chip.data('faq-question'));
				$panelAnswer.text($chip.data('faq-answer'));
				$panel.stop(true, true).slideDown(180);

				pauseTrack();
				syncActiveState();
			}

			function closeChip() {
				openQuestion = null;

				$panel.stop(true, true).slideUp(180);

				syncActiveState();
				resumeTrack();
			}

			$widget.on('click', '.masscie-faq-chip', function () {
				var $chip = $(this);
				var question = $chip.data('faq-question');

				if (!singleOpen) {
					// Multi-open mode: toggle this chip's own panel state only.
					if (openQuestion === question) {
						closeChip();
					} else {
						openChip($chip);
					}
					return;
				}

				if (openQuestion === question) {
					closeChip();
				} else {
					openChip($chip);
				}
			});

			$widget.on('keydown', '.masscie-faq-chip', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					$(this).trigger('click');
				}
			});
		});
	}

	/**
	 * Global Initializer
	 */
	function initMasscieWidgets($scope = null) {

		initTextScramble($scope);
		initFaqMarquee($scope);

		var $context = $scope || $(document);

		$context
			.find('.masscie-marquee-wrap, .masscie-crypto-marquee')
			.each(function () {
				initMarquee($(this));
			});
	}

	/**
	 * Frontend Init
	 */
	$(document).ready(function () {
		initMasscieWidgets();
	});

	/**
	 * Elementor Init
	 */
	$(window).on('elementor/frontend/init', function () {

		var handler = function ($scope) {
			initMasscieWidgets($scope);
		};

		// Scramble widget
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie_text_scramble.default',
			handler
		);

		// Marquee widgets
		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-text-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-image-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-testimonial-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-crypto-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-news-ticker.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/post-grid-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-team-members-marquee.default',
			handler
		);

		elementorFrontend.hooks.addAction(
			'frontend/element_ready/masscie-faq-marquee.default',
			handler
		);
	});

	/**
	 * Elementor Editor Refresh
	 */
	if (window.elementor) {

		$(document).on(
			'elementor/nested-elements/after-rebuild',
			function () {
				initMasscieWidgets();
			}
		);
	}

})(jQuery);

// Text Scramble Effect
class TextScramble {
    constructor(el) {
        this.el = el;
        this.chars = '!<>-_\\/[]{}—=+*^?#________';
        this.update = this.update.bind(this);
        this.texts = JSON.parse(this.el.dataset.texts || '[]');
        this.speed = parseInt(this.el.dataset.speed) || 3000; // Updated default to match widget
        this.currentIndex = 0;
        this.init();
    }

    init() {
        if (this.texts.length > 0) {
            this.setText(this.texts[0]);
            this.next();
        }
    }

    setText(newText) {
        const oldText = this.el.innerText;
        const length = Math.max(oldText.length, newText.length);
        this.queue = [];
        
        for (let i = 0; i < length; i++) {
            const from = oldText[i] || '';
            const to = newText[i] || '';
            const start = Math.floor(Math.random() * 40);
            const end = start + Math.floor(Math.random() * 40);
            this.queue.push({ from, to, start, end });
        }
        
        cancelAnimationFrame(this.frameRequest);
        this.frame = 0;
        this.update();
    }

    update() {
        let output = '';
        let complete = 0;
        
        for (let i = 0, n = this.queue.length; i < n; i++) {
            let { from, to, start, end, char } = this.queue[i];
            if (this.frame >= end) {
                complete++;
                output += to;
            } else if (this.frame >= start) {
                if (!char || Math.random() < 0.28) {
                    char = this.randomChar();
                    this.queue[i].char = char;
                }
                output += `<span class="dud">${char}</span>`;
            } else {
                output += from;
            }
        }
        
        this.el.innerHTML = output;
        
        if (complete === this.queue.length) {
            this.resolve && this.resolve();
        } else {
            this.frameRequest = requestAnimationFrame(this.update);
            this.frame++;
        }
    }

    randomChar() {
        return this.chars[Math.floor(Math.random() * this.chars.length)];
    }

    next() {
        if (this.texts.length > 1) {
            this.timeout = setTimeout(() => {
                this.currentIndex = (this.currentIndex + 1) % this.texts.length;
                this.setText(this.texts[this.currentIndex]);
                this.next();
            }, this.speed);
        }
    }

    // Add cleanup method
    destroy() {
        cancelAnimationFrame(this.frameRequest);
        clearTimeout(this.timeout);
    }
}