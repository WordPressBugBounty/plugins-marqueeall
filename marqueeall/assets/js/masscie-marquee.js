(function ($) {
  'use strict';

  function initMarquee($wrap) {
    var prevState = $wrap.data('masscie-state');
    if (prevState) {
      try { if (prevState.ro) prevState.ro.disconnect(); } catch (e) {}
      $wrap.off('.massciePause masscieResize masscieImg');
      $wrap.removeData('masscie-state');
    }

    var speed = parseFloat($wrap.data('speed') || $wrap.data('animation-speed') || 60);
    if (!isFinite(speed) || speed <= 0) speed = 60;
    var reverse = $wrap.data('reverse') === true || $wrap.data('reverse') === 'yes' || $wrap.hasClass('masscie-reverse');
    var pause = $wrap.data('pause') !== 'no' && $wrap.data('pause-on-hover') !== 'no';
    var gap = parseInt($wrap.data('gap') || 24, 10);
    var isVertical = $wrap.hasClass('masscie-vertical') || $wrap.data('vertical') === 'yes';
    var $track = $wrap.find('.masscie-track');

    $wrap.css('--masscie-gap', gap + 'px');
    var $originalItems = $track.children().clone(true, true);

    function measureGroupPx($group) {
      var rect = $group[0].getBoundingClientRect();
      return Math.max(0, Math.round(isVertical ? rect.height : rect.width));
    }

    function build() {
      $track.empty();
      var $g1 = $('<div class="masscie-group"></div>');
      var $g2 = $('<div class="masscie-group"></div>');

      $g1.append($originalItems.clone(true, true));
      $g2.append($originalItems.clone(true, true));
      $track.append($g1).append($g2);

      if (isVertical) $track.addClass('masscie-group-vertical'); else $track.removeClass('masscie-group-vertical');

      var wrapSize = isVertical ? Math.round($wrap.innerHeight()) : Math.round($wrap.innerWidth());
      var groupPx = measureGroupPx($g1);

      if (!groupPx) {
        $g1.children().each(function () {
          var $el = $(this);
          if ($el.css('display') === 'none') $el.css({ display: isVertical ? 'block' : 'inline-block', visibility: 'hidden' });
        });
        groupPx = measureGroupPx($g1);
      }

      if (!groupPx || groupPx <= 0) { setTimeout(build, 80); return; }

      while (groupPx < wrapSize) {
        $g1.append($originalItems.clone(true, true));
        $g2.append($originalItems.clone(true, true));
        groupPx = measureGroupPx($g1);
        if (groupPx > 50000) break;
      }

      var duration = groupPx / Math.max(1, speed);
      if (Math.abs(duration - 3) < 0.01) duration += 0.01;

      $wrap.css('--masscie-duration', duration + 's');
      $track.find('.masscie-group').css({
        'animation-duration': duration + 's',
        'animation-play-state': 'paused' // pause initially for smooth start
      });

      if (reverse) $track.find('.masscie-group').css('animation-direction', 'reverse'); 
      else $track.find('.masscie-group').css('animation-direction', '');

      $wrap.off('.massciePause');
      if (pause) {
        $wrap.on('mouseenter.massciePause', function () { $track.find('.masscie-group').css('animation-play-state', 'paused'); });
        $wrap.on('mouseleave.massciePause', function () { $track.find('.masscie-group').css('animation-play-state', 'running'); });
      }

      // Smooth start after images or videos load
      var $media = $track.find('img');
      var loadedCount = 0;
      function tryStart() {
        loadedCount++;
        if (loadedCount >= $media.length) {
          requestAnimationFrame(function () {
            $track.find('.masscie-group').css('animation-play-state', 'running');
          });
        }
      }

      if ($media.length === 0) $track.find('.masscie-group').css('animation-play-state', 'running');
      else {
        $media.each(function () {
          if (this.tagName.toLowerCase() === 'img') {
            if (this.complete) tryStart();
            else $(this).on('load', tryStart);
          } else if (this.tagName.toLowerCase() === 'video') {
            if (this.readyState >= 2) tryStart();
            else $(this).on('loadeddata', tryStart);
          }
        });
      }

      var state = $wrap.data('masscie-state') || {};
      state.groupPx = groupPx;
      state.duration = duration;
      $wrap.data('masscie-state', state);

      if (window.__masscie_debug) console.debug('masscie build:', { wrapSize: wrapSize, groupPx: groupPx, duration: duration, reverse: reverse, pause: pause });
    }

    build();

    if ('ResizeObserver' in window) {
      var ro = new ResizeObserver(function () {
        clearTimeout($wrap.data('masscie-ro-t'));
        build();
      });
      ro.observe($wrap[0]);
      $track.find('img').each(function () { try { ro.observe(this); } catch (e) {} });
      var state = $wrap.data('masscie-state') || {};
      state.ro = ro;
      $wrap.data('masscie-state', state);
    } else {
      $(window).on('resize.masscie', function () { clearTimeout($wrap.data('masscie-resize-t')); build(); });
      $track.find('img').on('load.masscie loadeddata.masscie', build);
      var state2 = $wrap.data('masscie-state') || {};
      state2.ro = null;
      $wrap.data('masscie-state', state2);
    }
  }

  $(window).on('elementor/frontend/init', function () {
    var handler = function ($scope) { 
      // Handle standard marquee wraps
      $scope.find('.masscie-marquee-wrap, .masscie-crypto-marquee').each(function () { 
        initMarquee($(this)); 
      }); 
    };

    // Existing widgets
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-text-marquee.default', handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-image-marquee.default', handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-testimonial-marquee.default', handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-marquee.default', handler);

    // ✅ Crypto Marquee widget
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-crypto-marquee.default', handler);
    
    // ✅ News Ticker widget
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-news-ticker.default', handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/post-grid-marquee.default', handler);
    elementorFrontend.hooks.addAction('frontend/element_ready/masscie-team-members-marquee.default', handler);
  });

  // Initialize on document ready for both standard and crypto marquees
  $(function () { 
    $('.masscie-marquee-wrap, .masscie-crypto-marquee').each(function () { 
      initMarquee($(this)); 
    }); 
  });

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

// Initialize Text Scramble for both frontend and Elementor editor
function initTextScramble($scope = null) {
    const elements = $scope 
        ? $scope[0].querySelectorAll('.masscie-scramble-text:not([data-scramble-initialized])')
        : document.querySelectorAll('.masscie-scramble-text:not([data-scramble-initialized])');
    
    elements.forEach(el => {
        el.setAttribute('data-scramble-initialized', 'true');
        new TextScramble(el);
    });
}

// Initialize on DOM ready
jQuery(function($) {
    // Initialize any existing elements
    initTextScramble();
    
    // Initialize for Elementor editor
    if (typeof elementorFrontend !== 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/masscie_text_scramble.default', function($scope) {
            initTextScramble($scope);
        });
    }
    
    // Handle Elementor preview refresh
    if (window.elementor) {
        $(document).on('elementor/nested-elements/after-rebuild', function() {
            initTextScramble();
        });
    }
});