/**
 * MarqueeAll — Widget Manager Admin JS
 *
 * @package MarqueeAll
 * @since   1.3.0
 */
/* global MARQUEEALL_ADMIN, jQuery */

(function ($) {
	'use strict';

	var $grid       = null;
	var $stats      = null;
	var $noResults  = null;
	var $search     = null;
	var $saveBtn    = null;
	var $enableAll  = null;
	var $disableAll = null;
	var $toast      = null;
	var toastTimer  = null;

	/* ------------------------------------------------------------------ */
	/* Boot                                                                  */
	/* ------------------------------------------------------------------ */

	$(function () {
		$grid       = $('#marqueeall-grid');
		$stats      = $('#marqueeall-stats');
		$noResults  = $('#marqueeall-no-results');
		$search     = $('#marqueeall-search');
		$saveBtn    = $('#marqueeall-save');
		$enableAll  = $('#marqueeall-enable-all');
		$disableAll = $('#marqueeall-disable-all');
		$toast      = $('#marqueeall-toast');

		bindEvents();
		updateStats(); // Initialise counter on load.
	});

	/* ------------------------------------------------------------------ */
	/* Event binding                                                         */
	/* ------------------------------------------------------------------ */

	function bindEvents() {
		// Click anywhere on the card body (except links) toggles the widget.
		$grid.on('click', '.marqueeall-card__body', function (e) {
			if ($(e.target).is('a')) { return; }
			var $card  = $(this).closest('.marqueeall-card');
			var $input = $card.find('.marqueeall-toggle__input');
			if ($input.is(':disabled')) { return; }
			$input.prop('checked', !$input.prop('checked')).trigger('change');
		});

		// Toggle input change — update card state + counter.
		$grid.on('change', '.marqueeall-toggle__input', function () {
			var $input = $(this);
			var $card  = $input.closest('.marqueeall-card');
			$card.toggleClass('marqueeall-card--active', $input.prop('checked'));
			updateStats();
		});

		$enableAll.on('click',  function () { setAllWidgets(true);  });
		$disableAll.on('click', function () { setAllWidgets(false); });

		$search.on('input', debounce(onSearch, 150));

		$saveBtn.on('click', onSave);
	}

	/* ------------------------------------------------------------------ */
	/* Enable / Disable All                                                  */
	/* ------------------------------------------------------------------ */

	function setAllWidgets(enable) {
		$grid.find('.marqueeall-toggle__input').each(function () {
			var $input = $(this);
			if ($input.is(':disabled')) { return; }
			$input.prop('checked', enable);
			$input.closest('.marqueeall-card').toggleClass('marqueeall-card--active', enable);
		});
		updateStats();
	}

	/* ------------------------------------------------------------------ */
	/* Stats counter                                                          */
	/* ------------------------------------------------------------------ */

	function updateStats() {
		var total   = $grid.find('.marqueeall-card').length;
		var enabled = $grid.find('.marqueeall-toggle__input:checked').length;

		var text = MARQUEEALL_ADMIN.i18n.enabled_count
			.replace('%1$d', enabled)
			.replace('%2$d', total);

		$stats.text(text);
	}

	/* ------------------------------------------------------------------ */
	/* Search                                                                 */
	/* ------------------------------------------------------------------ */

	function onSearch() {
		var query   = $search.val().toLowerCase().trim();
		var visible = 0;

		$grid.find('.marqueeall-card').each(function () {
			var $card = $(this);
			var title = ($card.data('title') || '').toLowerCase();
			var show  = !query || title.indexOf(query) !== -1;
			$card.toggleClass('marqueeall-card--hidden', !show);
			if (show) { visible++; }
		});

		$noResults.toggle(visible === 0 && query.length > 0);
	}

	/* ------------------------------------------------------------------ */
	/* AJAX Save                                                              */
	/* ------------------------------------------------------------------ */

	function onSave() {
		$saveBtn.prop('disabled', true).text(MARQUEEALL_ADMIN.i18n.saving);

		// Build a complete status object for EVERY widget — 1 or 0.
		// Unchecked checkboxes are NOT submitted by normal form serialization,
		// so we must explicitly include 0 values here.
		var widgetStatus = {};

		$grid.find('.marqueeall-toggle__input').each(function () {
			var $input = $(this);
			var name   = $input.attr('name');               // widget_status[slug]
			var match  = name.match(/widget_status\[([^\]]+)\]/);
			if (match && match[1]) {
				widgetStatus[ match[1] ] = $input.prop('checked') ? 1 : 0;
			}
		});

		$.ajax({
			url:  MARQUEEALL_ADMIN.ajax_url,
			type: 'POST',
			data: {
				action:        'marqueeall_save_widget_status',
				nonce:         MARQUEEALL_ADMIN.nonce,
				widget_status: widgetStatus,
			},
			success: function (response) {
				if (response && response.success) {
					showToast(response.data.message || MARQUEEALL_ADMIN.i18n.saved, 'success');

					if (response.data.enabled_count !== undefined) {
						var text = MARQUEEALL_ADMIN.i18n.enabled_count
							.replace('%1$d', response.data.enabled_count)
							.replace('%2$d', response.data.total);

						$stats.text(text);
					}
				} else {
					var msg = (response && response.data && response.data.message)
						? response.data.message
						: MARQUEEALL_ADMIN.i18n.save_error;
					showToast(msg, 'error');
				}
			},
			error: function () {
				showToast(MARQUEEALL_ADMIN.i18n.save_error, 'error');
			},
			complete: function () {
				$saveBtn.prop('disabled', false).text('Save Settings');
			},
		});
	}

	/* ------------------------------------------------------------------ */
	/* Toast                                                                  */
	/* ------------------------------------------------------------------ */

	function showToast(message, type) {
		clearTimeout(toastTimer);
		$toast
			.text(message)
			.removeClass('marqueeall-toast--success marqueeall-toast--error marqueeall-toast--visible')
			.addClass('marqueeall-toast--visible' + (type ? ' marqueeall-toast--' + type : ''));

		toastTimer = setTimeout(function () {
			$toast.removeClass('marqueeall-toast--visible marqueeall-toast--success marqueeall-toast--error');
		}, 3500);
	}

	/* ------------------------------------------------------------------ */
	/* Utility                                                                */
	/* ------------------------------------------------------------------ */

	function debounce(fn, delay) {
		var timer;
		return function () {
			var ctx  = this;
			var args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () { fn.apply(ctx, args); }, delay);
		};
	}

}(jQuery));
