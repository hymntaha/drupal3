(function ($) {
    Drupal.behaviors.coupon_admin = {
        attach: function (context) {

            $('#edit-valid-products-select input[type="checkbox"]', context).bind('change', function(e){
               populate_valid_products();
            });

            function populate_valid_products(){
                var $selected = $('#edit-valid-products-select input[type="checkbox"]:checked');
                var text = '';

                $selected.each(function(index, value){
                   text +=  $(value).val() + "\n";
                });

                $('#edit-field-valid-products-und-0-value').val(text);
            }
        }
    };
})(jQuery);
