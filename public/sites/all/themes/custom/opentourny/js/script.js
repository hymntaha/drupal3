/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - http://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.opentourny = {
        attach: function (context, settings) {

            var $content_containers = $('.field-name-field-content-containers > .field-items > .field-item .entity-content-container');
            var $small_content_containers = $('.field-name-field-small-content-containers > .field-items > .field-item .entity-content-container');
            var $sidebar_content_containers = $('.node-type-package .region-sidebar-second .content-container-content-container > .content');
            var $poi_content_containers = $('.node-poi .content-container-content-container > .content');
            var $all_content_containers = $('.content-container-content-container');
            var $all_slideshows = $('.slideshow-slideshow');

            var $attractions_containers = $('.attractions-section .container .row > div > div');



            if ($content_containers.length > 1 || $small_content_containers.length > 1) {
                $(window).resize(function () {
                    Drupal.opentourny.set_max_height($content_containers);
                    Drupal.opentourny.set_max_height($small_content_containers);
                });

                Drupal.opentourny.set_max_height($content_containers);
                Drupal.opentourny.set_max_height($small_content_containers);
            }
            if ($attractions_containers.length > 1) {
                Drupal.opentourny.set_max_height($attractions_containers);
                $(window).resize(function () {
                    Drupal.opentourny.set_max_height($attractions_containers);
                });

                $('.node-how-it-works .attractions-section .field-name-field-poi-body').css('padding-bottom','40px');
                $('.node-how-it-works .attractions-section .checkmarks').css('padding-top','12px');
            }

            if ($sidebar_content_containers.length > 1) {
                Drupal.opentourny.set_max_height($sidebar_content_containers);
                $(window).resize(function () {
                    Drupal.opentourny.set_max_height($sidebar_content_containers);
                });
            }

            if ($poi_content_containers.length > 1) {
                Drupal.opentourny.set_max_height($poi_content_containers);
            }

            if ($all_content_containers.length > 0) {
                $all_content_containers.bind('click', function (e) {
                    if ($(this).attr('data-href') !== undefined && e.target.nodeName != 'A' && e.target.nodeName != 'BUTTON') {
                        window.location.href = $(this).attr('data-href');
                    }
                });
            }

            if ($all_slideshows.length > 0) {
                var $clickable_slides = $('.carousel-inner > .item[data-href^="http"]', $all_slideshows);

                $clickable_slides.bind('click', function (e) {
                    if ($(this).attr('data-href') !== undefined && e.target.nodeName != 'A' && e.target.nodeName != 'BUTTON') {
                        window.location.href = $(this).attr('data-href');
                    }
                });

                $('#homepage-slide-overlays', $all_slideshows).bind('click', function (e) {
                    var active_slide_link = $('.carousel-inner > .item.active[data-href^="http"]', $all_slideshows).attr('data-href');
                    if ($('.carousel-inner > .item.active[data-href^="http"]', $all_slideshows).attr('data-href') !== undefined && e.target.nodeName != 'A' && e.target.nodeName != 'BUTTON') {
                        window.location.href = active_slide_link;
                    }
                });
            }

            if($('#package-listing').length > 0){
                $('#package-listing').imagesLoaded(function(){
                    $('#package-listing > .term').each(function(){
                        var $package_teasers = $('.node-package.node-teaser', this);
                        Drupal.opentourny.set_max_height($package_teasers);
                        $(window).resize(function () {
                            Drupal.opentourny.set_max_height($package_teasers);
                        });
                    });
                });
            }

            var $dropdown_links = $('.expanded.dropdown > a');
            if ($('html').hasClass('no-touch') && !Drupal.opentourny.isMobile.any()) {
                $dropdown_links.attr('data-hover', 'dropdown');
                $('.dropdown-menu').dropdownHover({
                    delay: 100
                });
            }

            $('#trigger-cc-link-click').bind('click', function (e) {
                e.preventDefault();
                $('.field-name-field-cc-link a', $(this).parents('.entity-content-container'))[0].click();
            });


            $('.trigger-buy-tickets').bind('click', function (e) {
                e.preventDefault();
                $('#hudson-buy-tickets-form').submit();
            });

            mobile_button_resize_handlers();
            mobile_button_click_handlers();

            if ($('body').hasClass('page-checkout')) {
                $('iframe').load(function () {
                    setTimeout(scroll_to_top, 1000);
                });
            }

            $('.page-header-wrapper').affix({
                offset: {
                    top: function () {
                        return (this.top = $('.navbar-wrapper').outerHeight(true))
                    }
                }
            });

            //Scroll anchor links correctly
            if ($('a[href^="#"]').length > 1) {
                $('a[href^="#"]').smoothScroll({
                    offset: get_scroll_offset()
                });

                $('.page-header-wrapper').on('affixed.bs.affix', function () {
                    $('a[href^="#"]').smoothScroll({
                        offset: get_scroll_offset()
                    });
                });

                $('.page-header-wrapper').on('affixed-top.bs.affix', function () {
                    $('a[href^="#"]').smoothScroll({
                        offset: get_scroll_offset()
                    });
                });
            }

            if(Drupal.opentourny.get_current_bootstrap_grid() == 'xs'){
                $('#block-block-1').after($('#block-footer-footer-social').toggleClass('hidden-xs').detach());
            }

            function mobile_button_click_handlers() {
                 $('#buy-tickets-header-mobile').bind('click', function (e) {
                      e.preventDefault();
                      $(this).removeClass('active');
                      $('.page-header-wrapper').addClass('active');
                      $('#mobile-close-menu').addClass('hidden');
                      if ($('.navbar-collapse').hasClass('in')) {
                           $('.navbar-collapse').collapse('hide');
                      }
                 });

                $('#mobile-close-buy-tickets').bind('click', function (e) {
                    e.preventDefault();
                    $('.page-header-wrapper').removeClass('active');
                    $('#buy-tickets-header-mobile').addClass('active');
                });

                $('.navbar-collapse').on('show.bs.collapse', function () {
                    $('.page-header-wrapper').removeClass('active');
                    $('.navbar-toggle').removeClass('active');
                    $('#buy-tickets-header-mobile').addClass('active');
                    $('#mobile-close-menu').removeClass('hidden');
                });

                $('.navbar-collapse').on('hide.bs.collapse', function () {
                    $('.navbar-toggle').addClass('active');
                });

                $('#mobile-close-menu').bind('click', function () {
                    $('#mobile-close-menu').addClass('hidden');
                    $('.navbar-toggle').addClass('active');
                    $('.navbar-collapse').collapse('hide');
                });
            }

            function mobile_button_resize_handlers() {
                $('.close-tab').width($('.navbar-toggle-wrapper').width());

                $(window).resize(function () {
                    $('.close-tab').width($('.navbar-toggle-wrapper').width());
                });

                if (Drupal.opentourny.get_current_bootstrap_grid() == 'xs') {
                    if ($('body').hasClass('map-page')) {
                        $('body').css('padding-top', 0);
                        $('.page-header-wrapper, .navbar-wrapper').css('position', 'static');
                    }
                }
                $(window).resize(function () {
                    if (Drupal.opentourny.get_current_bootstrap_grid() == 'xs') {
                        if ($('body').hasClass('map-page')) {
                            $('body').css('padding-top', 0);
                            $('.page-header-wrapper, .navbar-wrapper').css('position', 'static');
                        }
                    }
                    else {
                        $('.page-header-wrapper, .navbar-wrapper').css('position', '');
                    }
                });
            }

            function scroll_to_top() {
                window.scrollTo(0, 0);
            }

            function get_scroll_offset() {
                if (Drupal.opentourny.get_current_bootstrap_grid() == 'xs') {
                    return -62;
                }
                else {
                    if ($('.page-header-wrapper').hasClass('affix')) {
                        return -60;
                    }
                    else {
                        return -120;
                    }
                }
            }

        }
    };

    Drupal.opentourny = {
        set_max_height: function ($elements) {
            var max_height = 0;
            $elements.css('height', '');

            $elements.each(function () {
                var height = parseInt($(this).css('height'), 10);
                if (height > max_height) {
                    max_height = height;
                }
            });
            $elements.css('height', max_height);
        },

        get_current_bootstrap_grid: function () {
            if (viewportSize.getWidth() < 768) {
                return 'xs';
            }
            else if (viewportSize.getWidth() < 992) {
                return 'sm';
            }
            else if (viewportSize.getWidth() < 1200) {
                return 'md';
            }
            else {
                return 'lg';
            }
        },

        strpos: function (haystack, needle, offset) {
            //  discuss at: http://phpjs.org/functions/strpos/
            // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // improved by: Onno Marsman
            // improved by: Brett Zamir (http://brett-zamir.me)
            // bugfixed by: Daniel Esteban
            //   example 1: strpos('Kevin van Zonneveld', 'e', 5);
            //   returns 1: 14

            var i = (haystack + '')
                .indexOf(needle, (offset || 0));
            return i === -1 ? false : i;
        }
    };

    Drupal.opentourny.isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i) ? true : false;
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i) ? true : false;
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i) ? true : false;
        },
        any: function () {
            return (this.Android() || this.BlackBerry() || this.iOS() || this.Windows());
        }
    };

})(jQuery, Drupal, this, this.document);
