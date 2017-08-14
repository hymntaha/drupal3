(function ($) {
    Drupal.behaviors.cart = {
        attach: function (context) {
            var $package_form = $('form[id^="cart-step-1-form"]', context);
            var ticket_id = $('[name="dated"]', $package_form).val();

            $('[name="package"]', $package_form).bind('change', function () {
                refresh_ticket_table($(this).val());
            });

            $('[name="dated"]', $package_form).bind('change', function(){
               refresh_tour_description($(this).val());
            });

            if(ticket_id != undefined){
                refresh_tour_description(ticket_id);
            }

            function refresh_ticket_table(nid){
                $.ajax({
                    url: Drupal.settings.cart.ajax_ticket_table_path+nid
                }).done(function(response){
                    $('.cart-checkout-ticket-table').html(response);
                });
            }

            function refresh_tour_description(ticket_id){
                $.ajax({
                    url: Drupal.settings.cart.ajax_extra_info_path+ticket_id
                }).done(function(response){
                    $('#cart-checkout-extra-info').html(response);
                });
            }
        }
    };
})(jQuery);
