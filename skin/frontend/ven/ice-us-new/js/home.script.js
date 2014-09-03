jQuery(function ($) {
  $('.bxslider').bxSlider();
});
//function InitSlideDrop() {
//    var b = jQuery("#nav"), a = 150;
//    b.find(">li").each(function (g) {
//        var i = jQuery(this), e = i.find(".drop"), h = e.height(), f, d;
//        e.height(0);
//        i.mouseenter(function () {
//            if (f) {
//                clearTimeout(f)
//            }
//            d = setTimeout(function () {
//                c(i);
//                e.show().animate({height: h}, {queue: false, duration: a})
//            }, 300)
//        }).mouseleave(function () {
//            if (f) {
//                clearTimeout(f)
//            }
//            if (d) {
//                clearTimeout(d)
//            }
//            f = setTimeout(function () {
//                e.animate({height: 0}, {queue: false, duration: a, complete: function () {
//                    jQuery(this).hide()
//                }});
//                e.height(0)
//            }, 20)
//        })
//    });
//    function c(f) {
//        drop = f.find(".drop");
//        var e = f.position().left + parseInt(drop.css("left"));
//        var d = b.width() - e - drop.width();
//        if (e < 0) {
//            drop.css("left", parseInt(drop.css("left")) - e)
//        }
//        if (d < 0) {
//            drop.css("left", -(f.position().left + drop.width()) + b.width())
//        }
//    }
//}
//function initAutoScalingNav(d) {
//    if (!d.menuId) {
//        d.menuId = "nav"
//    }
//    if (!d.tag) {
//        d.tag = "a"
//    }
//    if (!d.spacing) {
//        d.spacing = 0
//    }
//    if (!d.constant) {
//        d.constant = 0
//    }
//    if (!d.minPaddings) {
//        d.minPaddings = 0
//    }
//    if (!d.liHovering) {
//        d.liHovering = false
//    }
//    if (!d.sideClasses) {
//        d.sideClasses = false
//    }
//    if (!d.equalLinks) {
//        d.equalLinks = false
//    }
//    if (!d.flexible) {
//        d.flexible = false
//    }
//    var a = document.getElementById(d.menuId);
//    if (a) {
//        a.className += " scaling-active";
//        var n = a.getElementsByTagName("li");
//        var l = [];
//        var e = [];
//        var c = 0;
//        for (var g = 0, f = 0; g < n.length; g++) {
//            if (n[g].parentNode == a) {
//                var m = n[g].getElementsByTagName(d.tag).item(0);
//                l.push(m);
//                l[f++].width = m.offsetWidth;
//                e.push(n[g]);
//                if (c < m.offsetWidth) {
//                    c = m.offsetWidth
//                }
//            }
//            if (d.liHovering) {
//                n[g].onmouseover = function () {
//                    this.className += " hover"
//                };
//                n[g].onmouseout = function () {
//                    this.className = this.className.replace("hover", "")
//                }
//            }
//        }
//        var h = a.clientWidth - l.length * d.spacing - d.constant;
//        if (d.equalLinks && c * l.length < h) {
//            for (var g = 0; g < l.length; g++) {
//                l[g].width = c
//            }
//        }
//        c = b(l);
//        if (c < h) {
//            var k = navigator.userAgent.toLowerCase();
//            for (var g = 0; b(l) < h; g++) {
//                l[g].width++;
//                if (!d.flexible) {
//                    l[g].style.width = l[g].width + "px"
//                }
//                if (g >= l.length - 1) {
//                    g = -1
//                }
//            }
//            if (d.flexible) {
//                for (var g = 0; g < l.length; g++) {
//                    c = (l[g].width - d.spacing - d.constant / l.length) / h * 100;
//                    if (g != l.length - 1) {
//                        e[g].style.width = c + "%"
//                    } else {
//                        if (navigator.appName.indexOf("Microsoft Internet Explorer") == -1 || k.indexOf("msie 8") != -1 || k.indexOf("msie 9") != -1) {
//                            e[g].style.width = c + "%"
//                        }
//                    }
//                }
//            }
//        } else {
//            if (d.minPaddings > 0) {
//                for (var g = 0; g < l.length; g++) {
//                    l[g].style.paddingLeft = d.minPaddings + "px";
//                    l[g].style.paddingRight = d.minPaddings + "px"
//                }
//            }
//        }
//        if (d.sideClasses) {
//            e[0].className += " first-child";
//            e[0].getElementsByTagName(d.tag).item(0).className += " first-child-a";
//            e[e.length - 1].className += " last-child";
//            e[e.length - 1].getElementsByTagName(d.tag).item(0).className += " last-child-a"
//        }
//        a.className += " scaling-ready"
//    }
//    function b(j) {
//        var i = 0;
//        for (var o = 0; o < j.length; o++) {
//            i += j[o].width
//        }
//        return i
//    }
//}
function popup(a) {
    try {
        var c = window.open(a, "popup", "width=470,height=450,scrollbars=1");
        c.focus()
    } catch (b) {
    }
    return false
}
function disableEnterKey(b) {
    var a;
    if (window.event) {
        a = window.event.keyCode
    } else {
        a = b.which
    }
    return(a != 13)
}
function selectPopularTag(a, c) {
    if (c) {
        var b = c.value.strip();
        if (b.indexOf(a) == -1) {
            c.value = b.length != 0 ? b + ", " + a : a
        }
    }
}
function applyModalPanelEffect(c, b, e) {
    if (c && b) {
        var d = jQuery(c);
        if (d && d.component) {
            var a = d.component;
            var f = a.getSizedElement();
            Element.hide(f);
            b.call(this, Object.extend({targetId: f.id}, e || {}))
        }
    }
}
function applyModalPanelEffect(d, b, f, c) {
    if (d && b) {
        var e = $(d);
        if (e && e.component) {
            var a = e.component;
            var g = a.getSizedElement();
            if (c) {
                Element.hide(g)
            }
            b.call(this, Object.extend({targetId: g.id}, f || {}))
        }
    }
}
function showModalPanelWithEffect(b, a, c) {
    applyModalPanelEffect(b, a, c, true)
}
function hideModalPanelWithEffect(b, d, c) {
    var a = c;
    a.afterFinish = function () {
        Richfaces.hideModalPanel(b)
    };
    applyModalPanelEffect(b, d, c, false)
}
function Get_Cookie(b) {
    var g = document.cookie.split(";");
    var c = "";
    var e = "";
    var f = "";
    var d = false;
    var a = "";
    for (a = 0; a < g.length; a++) {
        c = g[a].split("=");
        e = c[0].replace(/^\s+|\s+$/g, "");
        if (e == b) {
            d = true;
            if (c.length > 1) {
                f = unescape(c[1].replace(/^\s+|\s+$/g, ""))
            }
            return f;
            break
        }
        c = null;
        e = ""
    }
    if (!d) {
        return null
    }
}
function Set_Cookie(b, d, a, g, c, f) {
    var e = new Date();
    if (a != "") {
        e.setTime(e.getTime() + a * 1000 * 60 * 60 * 24)
    }
    document.cookie = b + "=" + escape(d) + ((a) ? ";expires=" + e.toGMTString() : "") + ((g) ? ";path=" + g : "") + (
        (c) ? ";domain=" + c : "") + ((f) ? ";secure" : "")
}
function Delete_Cookie(a, c, b) {
    if (Get_Cookie(a)) {
        document.cookie = a + "=" + ((c) ? ";path=" + c : "") + (
            (b) ? ";domain=" + b : "") + ";expires=Thu, 01-Jan-1970 00:00:01 GMT"
    }
}
function testCookieEnabled(a) {
    Set_Cookie("test", "test", "", "/", "", "");
    var b = Get_Cookie("test");
    if (b) {
        Delete_Cookie("test", "/", "")
    } else {
        document.write("We're sorry - to use this site, you need to enable cookies on your browser. Please change your browser settings or upgrade your browser.")
    }
}
function initScrollGallery() {
    jQuery("div.gallery").scrollGallery({sliderHolder: "div.frame", slider: ">ul", slides: ">li", btnPrev: "a.link-prev", btnNext: "a.link-next", activeClass: "active", generatePagination: ".switherHolder", step: 5})
}
jQuery.fn.scrollGallery = function (a) {
    var a = jQuery.extend({sliderHolder: ">div", slider: ">ul", slides: ">li", pagerLinks: "div.pager a", btnPrev: "a.link-prev", btnNext: "a.link-next", activeClass: "active", disabledClass: "disabled", generatePagination: false, curNum: "em.scur-num", allNum: "em.sall-num", circleSlide: true, pauseClass: "gallery-paused", pauseButton: "none", pauseOnHover: true, autoRotation: false, stopAfterClick: false, switchTime: 5000, duration: 1300, easing: "swing", event: "click", splitCount: false, afterInit: false, vertical: false, step: 1},
                          a);
    return this.each(function () {
        var d = jQuery(this);
        var B = jQuery(a.sliderHolder, d);
        var m = jQuery(a.slider, B);
        var l = jQuery(a.slides, m);
        var N = jQuery(a.btnPrev, d);
        var n = jQuery(a.btnNext, d);
        var A = jQuery(a.pagerLinks, d);
        var k = jQuery(a.generatePagination, d);
        var H = jQuery(a.curNum, d);
        var G = jQuery(a.allNum, d);
        var e = jQuery(a.pauseButton, d);
        var t = a.pauseOnHover;
        var C = a.pauseClass;
        var o = a.autoRotation;
        var f = a.activeClass;
        var r = a.disabledClass;
        var L = a.easing;
        var U = a.duration;
        var O = a.switchTime;
        var j = a.event;
        var z = a.step;
        var P = a.vertical;
        var E = a.circleSlide;
        var v = a.stopAfterClick;
        var V = a.afterInit;
        var g = a.splitCount;
        if (!l.length) {
            return
        }
        if (g) {
            var R = 0;
            var c = jQuery("<slide>").addClass("split-slide");
            l.each(function () {
                c.append(this);
                R++;
                if (R > g - 1) {
                    R = 0;
                    m.append(c);
                    c = jQuery("<slide>").addClass("split-slide")
                }
            });
            if (R) {
                m.append(c)
            }
            l = m.children()
        }
        var S = 0;
        var I = 0;
        var D = 0;
        var h = false;
        var y;
        var u;
        var p;
        var T;
        var x;
        l.each(function () {
            I += jQuery(this).outerWidth(true);
            D += jQuery(this).outerHeight(true)
        });
        function K() {
            if (P) {
                if (z) {
                    u = l.eq(S).outerHeight(true);
                    p = Math.ceil((D - B.height()) / u) + 1;
                    T = -u * S
                } else {
                    u = B.height();
                    p = Math.ceil(D / u);
                    T = -u * S;
                    if (T < u - D) {
                        T = u - D
                    }
                }
            } else {
                if (z) {
                    y = l.eq(S).outerWidth(true) * z;
                    p = Math.ceil((I - B.width()) / y) + 1;
                    T = -y * S;
                    if (T < B.width() - I) {
                        T = B.width() - I
                    }
                } else {
                    y = B.width();
                    p = Math.ceil(I / y);
                    T = -y * S;
                    if (T < y - I) {
                        T = y - I
                    }
                }
            }
        }

        if (N.length) {
            N.bind(j, function () {
                if (v) {
                    b()
                }
                w();
                return false
            })
        }
        if (n.length) {
            n.bind(j, function () {
                if (v) {
                    b()
                }
                F();
                return false
            })
        }
        if (k.length) {
            k.empty();
            K();
            var s = jQuery("<ul>");
            for (var M = 0; M < p; M++) {
                jQuery('<li><a href="#">' + (M + 1) + "</a></li>").appendTo(s)
            }
            s.appendTo(k);
            A = s.children()
        }
        if (A.length) {
            A.each(function (i) {
                jQuery(this).bind(j, function () {
                    if (S != i) {
                        if (v) {
                            b()
                        }
                        S = i;
                        q()
                    }
                    return false
                })
            })
        }
        function w() {
            K();
            if (S > 0) {
                S--
            } else {
                if (E) {
                    S = p - 1
                }
            }
            q()
        }

        function F() {
            K();
            if (S < p - 1) {
                S++
            } else {
                if (E) {
                    S = 0
                }
            }
            q()
        }

        function Q() {
            if (A.length) {
                A.removeClass(f).eq(S).addClass(f)
            }
            if (!E) {
                N.removeClass(r);
                n.removeClass(r);
                if (S == 0) {
                    N.addClass(r)
                }
                if (S == p - 1) {
                    n.addClass(r)
                }
            }
            if (H.length) {
                H.text(S + 1)
            }
            if (G.length) {
                G.text(p)
            }
        }

        function q() {
            K();
            if (P) {
                m.animate({marginTop: T}, {duration: U, queue: false, easing: L})
            } else {
                m.animate({marginLeft: T}, {duration: U, queue: false, easing: L})
            }
            Q();
            J()
        }

        function b() {
            if (x) {
                clearTimeout(x)
            }
            o = false
        }

        function J() {
            if (!o || h) {
                return
            }
            if (x) {
                clearTimeout(x)
            }
            x = setTimeout(F, O + U)
        }

        if (t) {
            d.hover(function () {
                h = true;
                if (x) {
                    clearTimeout(x)
                }
            }, function () {
                h = false;
                J()
            })
        }
        K();
        Q();
        J();
        if (e.length) {
            e.click(function () {
                if (d.hasClass(C)) {
                    d.removeClass(C);
                    o = true;
                    J()
                } else {
                    d.addClass(C);
                    b()
                }
                return false
            })
        }
        if (V && typeof V === "function") {
            V(d, l)
        }
    })
};