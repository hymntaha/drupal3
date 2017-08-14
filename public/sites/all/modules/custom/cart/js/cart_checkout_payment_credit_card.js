(function ($) {
    Drupal.behaviors.cart_checkout_payment_credit_card = {
        attach: function (context) {
            $.blockUI.defaults.css = {};

            $.blockUI({
                message: '<span class="block-ui">' + Drupal.settings.checkout.payment_message + '</span>',
                centerX: false,
                centerY: false
            });

            var client = new braintree.api.Client({clientToken: Drupal.settings.braintree.client_token});
            client.tokenizeCard({
                number: Drupal.settings.checkout.card_number,
                cardholderName: Drupal.settings.checkout.name,
                expirationMonth: Drupal.settings.checkout.expiration_date_month,
                expirationYear: Drupal.settings.checkout.expiration_date_year,
                cvv: Drupal.settings.checkout.cvv,
                billingAddress: {
                    postalCode: Drupal.settings.checkout.postal_code
                }
            },function(err, nonce){
                if(err === null){
                    $('input[name="nonce"]',context).val(nonce);
                }
                else{
                    $('input[name="error"]',context).val(err);
                }
                $('#cart-checkout-step-3-form').submit();
            });
        }
    };
})(jQuery);
