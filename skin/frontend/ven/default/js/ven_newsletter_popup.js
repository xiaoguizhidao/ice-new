/**
 * JavaScript library for ven_newsletter/popup_form
 *
 * @package Ven_Newsletter
 * @author  Yuriy Gavdan <yuriy@ven.com>
 */

var VenNewsletterPopupAjaxForm       = Class.create(VarienForm);
VenNewsletterPopupAjaxForm.prototype =
{
    initialize: function (formId, inputId) {

        //--------------------------------------------------------------
        // set variables

        this.hideNewsletterPopup = null;
        this.formId              = formId;
        this.inputId             = inputId;
        this.gaPageViewPrefix    = '/popup/newsletter';


        //--------------------------------------------------------------
        // set gui actions after onload event

        var self = this;
        document.observe('dom:loaded', function () {

            // add rounded-corners wrappers
            self.gui_addRoundedCorners();

            // IE Hack for placeholder
            self.gui_initPlaceholder();

            // close button
            self.gui_closePopup();

            // show popup, if only first time show AND has no disable from URL param: (?s=nl)
            if (!self.gui_isHiddenPopup() && !self.gui_isPopupDisabledByUrl()) {
                self.gui_showPopup();
            } else {
                self.gui_hidePopup();
            }

            self.gui_setPopupFirstShowCookie();

            self.bindSubmit();
        });
    },

    bindSubmit: function () {
        var self = this;

        $(self.formId).observe('submit', function (event) {

            self.subscriberEmail = $(self.inputId).getValue().toString();
            self.formAction      = $(self.formId).getAttribute("action").toString();

            // Start Simple Validation
            if (self.validateString()) {

                // Start Ajax Validation
                if (self.subscriberEmail && self.formAction) {

                    new Ajax.Request(self.formAction, {
                        method:         'post',
                        parameters:     $(self.formId).serialize(),
                        requestHeaders: {Accept:'application/json'},

                        onCreate: function () {
                            var wrapperId = $(self.formId).up().getAttribute('id').toString();
                            $(wrapperId + "-form-wrapper").hide();
                            $(wrapperId + "-ajax-preloader").show();

                        },

                        onSuccess: function (request, jsonData) {

                            var responseStatus  = (!!jsonData.success);
                            var responseMessage = (!!jsonData.success) ? request.headerJSON.messages.success.toString()
                                                                       : request.headerJSON.messages.error.toString();

                            var wrapperId       = $(self.formId).up().getAttribute('id').toString();

                            $(wrapperId + "-ajax-preloader").hide();
                            $(wrapperId + "-form-wrapper").show();

                            if (!responseStatus) {
                                Validation.ajaxError($(self.inputId), responseMessage);
                            } else {
                                $(wrapperId + "-form-wrapper").hide();
                                $(wrapperId + "-form-wrapper-success").style.display = "block";

                                if (window._gaq) {
                                    try {
                                        _gaq.push(['_trackPageview', self.gaPageViewPrefix + '/success']);
                                    }
                                    catch (e) { }
                                }
                            }
                        },

                        onFailure: function () {
                            if (window._gaq) {
                                try {
                                    _gaq.push(['_trackPageview', self.gaPageViewPrefix + '/failure']);
                                }
                                catch (e) { }
                            }
                        }
                    });
                }
            }

            event.stop();
        });
    },

    validateString: function () {
        this.validator = new Validation(this.formId);
        return           !!(this.validator && this.validator.validate());
    },

    applyTrackGA: function (trackID, trackURL) {
        this.trackGA     = true;
        this.trackGA_ID  = trackID;
        this.gaPageViewPrefix = trackURL;
    },

    // GUI actions
    gui_addRoundedCorners: function () {
        var popupNewsletter;
        popupNewsletter = $('popup-newsletter');
        popupNewsletter.wrap('div', { 'id':    'popup-newsletter-wrapper'                 });
        popupNewsletter.wrap('div', { 'class': 'popup-block-wrapper-newsletter-top_bg'    });
        popupNewsletter.wrap('div', { 'class': 'popup-block-wrapper-newsletter-bottom_bg' });
    },

    gui_initPlaceholder: function () {
        // IE Hack for placeholder attribute (need to include: ven_placeholder.js)
        VenInputPlaceholder( document.getElementById(this.inputId) );
    },

    gui_closePopup: function () {
        var thisForm = this;
        $('btn-close').observe('click', function () {
            thisForm.gui_hidePopup();
        });
    },

    gui_hidePopup: function () {
        return $('popup-overlay', 'popup-newsletter', 'popup-newsletter-wrapper').invoke('hide');
    },

    gui_showPopup: function () {
        if (window._gaq) {
            try {
                _gaq.push(['_trackPageview', this.gaPageViewPrefix + '/show']);
            }
            catch (e) { }
        }

        $('popup-newsletter-wrapper').style.display = 'block';
        $('popup-overlay').style.display            = 'block';
        $('popup-newsletter').style.display         = 'block';
    },

    gui_getUrlParams: function () {
        var params = {};
        window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (m, key, value) {
            params[key] = value;
        });
        return params;
    },

    gui_isPopupDisabledByUrl: function () {
        return (this.gui_getUrlParams()["s"] === "nl");
    },

    gui_isHiddenPopup: function () {
        return Mage.Cookies.get('hideNewsletterPopup');
    },

    gui_setPopupFirstShowCookie: function () {
        if (this.gui_isHiddenPopup() != 1) {
            var expireAt = new Date();
            expireAt.setFullYear(expireAt.getFullYear() + 1);
            Mage.Cookies.set('hideNewsletterPopup', 1, expireAt);
            return true;
        }
    }
};