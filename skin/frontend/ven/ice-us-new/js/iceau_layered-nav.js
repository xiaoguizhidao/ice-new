/*global $, $$, Selector, Event */
(function ($, $$, window, Selector, Event) {
    "use strict";

    // functions
    function initMoreLinks() {
        var blockMask    = "dl#narrow-by-list";
        var navMoreLinks = Selector.findChildElements($(blockMask), ["li.more-link a", "li.less-link a"]);

        var disableAllLessLinks = $$(blockMask + " li.less-link");
        disableAllLessLinks.each(function (lessItem) {
            lessItem.hide();
        });

        navMoreLinks.each(function (linkElement) {
            Event.observe(linkElement, 'click', function () {
                var ddBlock          = $(this).up(2);
                var lessLink         = Selector.findChildElements(ddBlock, ["li.less-link a"])[0];
                var moreLink         = Selector.findChildElements(ddBlock, ["li.more-link a"])[0];
                var hiddenAndVisible = Selector.findChildElements(ddBlock, ["li.lnav-hidden", "li.lnav-visible"]);
                var linkType         = $(this).up().className;

                if (linkType === 'more-link') {
                    moreLink.up().hide();
                    lessLink.up().show();
                } else if (linkType === 'less-link') {
                    lessLink.up().hide();
                    moreLink.up().show();
                }

                hiddenAndVisible.each(function(el) {
                    el.toggleClassName('lnav-visible');
                    el.toggleClassName('lnav-hidden');
                });
            });
        });
    }

    var ACTIVE_NAV_CLASS = "active";
    var IceLayeredNav = window.IceLayeredNav = window.IceLayeredNav || function (element_name, active_nav) {

        var element = $(element_name);

        if (!element) {
            return;
        }

        var i = 0;
        var maskNavs = "dt";
        var maskContents = "dd";
        var allNavs = Selector.findChildElements(element, [maskNavs]);
        var allContents = Selector.findChildElements(element, [maskContents]);

        for (i = 0; i < allContents.length; i++) {
            var initClass;

            if (parseInt(active_nav, 10) > 0) {
                initClass = (i === active_nav - 1) ? ACTIVE_NAV_CLASS : "";
            } else {
                initClass = (active_nav) ? ACTIVE_NAV_CLASS : "";
            }

            allNavs[i].addClassName("iceau-nav iceau-nav-item-" + (i + 1) + " " + initClass);
            allContents[i].addClassName("iceau-nav-content iceau-nav-content-" + (i + 1) + " " + initClass);

            // addLastChildElement
            if (i === allContents.length - 1) {
                allNavs[i].addClassName("last-child");
                allContents[i].addClassName("last-child");
            }
        }

        allNavs.each(function (el, index) {
            el.observe('click', function () {
                allNavs[index].toggleClassName(ACTIVE_NAV_CLASS);
                allContents[index].toggleClassName(ACTIVE_NAV_CLASS);
            });
        });
    };

    // init for all pages
    document.observe('dom:loaded', function () {
        var active_nav = true;
        if ($(document.body).hasClassName("cms-page-view")) {
            active_nav = false;
        }
        var nav = new IceLayeredNav("narrow-by-list", active_nav);

        initMoreLinks();
    });
}($, $$, window, Selector, Event));