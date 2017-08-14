(function ($) {
    Drupal.behaviors.package = {
        attach: function(context) {
            if($('.date-picker',context).length){
                $('.date-picker',context).datepicker({
                    showOn: "both",
                    buttonImage: Drupal.settings.package.icon_path,
                    minDate: new Date(),
                    dateFormat: 'mm/dd/yy'
                });
            }
        }
    };
})(jQuery);
