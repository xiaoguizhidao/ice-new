/*global $, Selector */
(function ($, window, Selector) {
    "use strict";
    var ACTIVE_TAB_CLASS = "current";
    var IceTabs = window.IceTabs = window.IceTabs || function (element, active_tab_index) {

        element = $(element);

        if (!element) {
            throw new Error("IceTabs: Cannot find root element");
        }

        active_tab_index = active_tab_index || 1;

        var i = 0;
        var maskTabs = "h2";
        var maskContents = "div.std";
        var allTabs = Selector.findChildElements(element, [maskTabs]);
        var allContents = Selector.findChildElements(element, [maskContents]);

        for (i = 0; i < allContents.length; i++) {
            var initClass = (i === active_tab_index - 1) ? ACTIVE_TAB_CLASS : ""; // First init the current tab
            allTabs[i].addClassName("iceau-tab iceau-tab-" + (i + 1) + " " + initClass);
            allContents[i].addClassName("iceau-tab-content iceau-tab-content-" + (i + 1) + " " + initClass);
        }

        allTabs.each(function (el, index) {
            el.observe('click', function () {
                allTabs.each(function (el) {
                    el.removeClassName(ACTIVE_TAB_CLASS);
                });
                allContents.each(function (el) {
                    el.removeClassName(ACTIVE_TAB_CLASS);
                });
                allTabs[index].addClassName(ACTIVE_TAB_CLASS);
                allContents[index].addClassName(ACTIVE_TAB_CLASS);
            });
        });
    };
}($, window, Selector));