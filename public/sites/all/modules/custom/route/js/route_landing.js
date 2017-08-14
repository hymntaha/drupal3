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

  Drupal.behaviors.route_landing = {
    attach: function (context, settings) {

      var $route_teasers = $('.node-route.node-teaser',context);

      if($route_teasers.length > 1){
        $(window).resize(function(){
          Drupal.opentourny.set_max_height($route_teasers);
        });
        
        Drupal.opentourny.set_max_height($route_teasers);

        $route_teasers.bind('click',function(e){
          if(e.target.nodeName != 'A'){
            window.location.href = $(this).attr('data-href');
          }
        });
      }

    }
  };



})(jQuery, Drupal, this, this.document);