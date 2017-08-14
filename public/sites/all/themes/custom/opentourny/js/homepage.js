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

   Drupal.behaviors.homepage = {
      attach: function (context, settings) {
         var toggled = false;
         var grid = Drupal.opentourny.get_current_bootstrap_grid();
         var $featured_items = $('.featured-content .field-name-field-slideshow, .featured-content .node-package.view-mode-homepage_teaser');

         if (grid != 'xs') {
            $(document).scroll(function () {
               if ($(this).scrollTop() >= $('.main-container').offset().top - $('.navbar-wrapper').height()) {
                  if (!toggled) {
                     toggle_buy_tickets();
                     toggled = true;
                  }
               }
               else {
                  if (toggled) {
                     toggle_buy_tickets();
                     toggled = false;
                  }
               }
            });
         }

         var $homepage_video = $('.node-homepage.view-mode-full_screen_content video', context);
         if ($homepage_video.length > 0) {
            videojs($homepage_video.attr('id')).ready(function () {
               this.play();
            });

            $(window).resize(function(){
               set_video_offset();
            })
         }

         $('#homepage-cc-buy-now', context).prepend('<span class="glyphicon glyphicon-shopping-cart"></span> ');
         $('#homepage-cc-buy-now', context).bind('click', function (e) {
            $('#buy-tickets-header-mobile').trigger('click');
         });

          $featured_items.imagesLoaded(function(){
              Drupal.opentourny.set_max_height($featured_items);

              $(window).resize(function () {
                  Drupal.opentourny.set_max_height($featured_items);
              })
          });


         function toggle_buy_tickets() {
              $('body', context).toggleClass('scroll-top');
         }

         function set_video_offset(){
            var video_height = $homepage_video.height();
            var content_height = $('.full-screen-content-wrapper').height();
            var height_diff = video_height - content_height;

            if(height_diff > 0){
               $('.field-name-field-video').css('margin-top', height_diff * -1).css('visibility','visible');
            }
         }


      }
   };


})(jQuery, Drupal, this, this.document);
