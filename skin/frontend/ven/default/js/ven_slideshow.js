/**
 * JavaScript library for ven_content/slideshow block
 *
 * @package Ven_Content
 * @author  Andrey Legayev <andrey@ven.com>
 */

(function (Element, Selector, Effect) {

    var PLAY_INTERVAL = 6000;
    var FADE_DURATION = 2000;

    var _lock = false;
    var show_next = function (slideshow) {
        if (_lock) {
            return false;
        } else {
            _lock = true;
        }

        var index, slides = slideshow.getElementsByTagName('li');
        for (index = 0; index < slides.length; index++) {
            if (Element.visible(slides[index])) {
                break;
            }
        }

        if (index == slides.length) {
            return false; // can't find visible slide
        }

        var current_el = slides[index];
        var next_el = slides[index == slides.length - 1 ? 0 : index + 1];

        Effect.Fade(current_el, { duration:FADE_DURATION / 1000 });
        Effect.Appear(next_el, { duration:FADE_DURATION / 1000, afterFinish:function () {
            _lock = false;
        } });

        return true;
    };

    var show_prev = function (slideshow) {
        if (_lock) {
            return false;
        } else {
            _lock = true;
        }

        var index, slides = slideshow.getElementsByTagName('li');
        for (index = 0; index < slides.length; index++) {
            if (Element.visible(slides[index])) {
                break;
            }
        }

        if (index == slides.length) {
            return false; // can't find visible slide
        }

        var current_el = slides[index];
        var next_el = slides[index == 0 ? slides.length - 1 : index - 1];

        Effect.Fade(current_el, { duration:FADE_DURATION / 1000 });
        Effect.Appear(next_el, { duration:FADE_DURATION / 1000, afterFinish:function () {
            _lock = false;
        } });

        return true;
    };

    document.observe("dom:loaded", function () {
        $$('.ven_slideshow').each(function (el) {

            var interval_id = null;
            var autoplay = function () {
                if (interval_id) {
                    clearInterval(interval_id);
                }

                interval_id = setInterval(function () {
                    show_next(el);
                }, PLAY_INTERVAL);
            };

            autoplay();

            // bind to prev/next buttons
            Selector.findChildElements(el, ['.ven_slideshow_prev'])[0].observe('click', function () {
                show_prev(el);
                autoplay(); // restart autoplay
            });
            Selector.findChildElements(el, ['.ven_slideshow_next'])[0].observe('click', function () {
                show_next(el);
                autoplay(); // restart autoplay
            });
        });
    });

})(window.Element, window.Selector, window.Effect);
