(function ($) {
    Drupal.behaviors.cart_checkout_payment_paypal = {
        attach: function (context) {
            var checkout;

            $.blockUI.defaults.css = {};

            $.blockUI({
                message: '<span class="block-ui">' + Drupal.settings.checkout.payment_message + '</span>' +
                '<p id="paypal-options"><a id="paypal-button" href="#">Click to pay with PayPal</a><a id="paypal-cancel">'+Drupal.settings.checkout.cancel+'</a></p>' +
                '<p id="paypal-loading"></p>',
                centerX: false,
                centerY: false
            });

            braintree.setup(Drupal.settings.braintree.client_token, 'custom', {
                onReady: function (integration) {
                    checkout = integration;
                },
                onPaymentMethodReceived: function (payload) {
                    $('input[name="nonce"]',context).val(payload.nonce);
                    submitCheckout();
                },
                onError: function (error){
                    handleError(error.message);
                },
                onCancelled: function (){
                    handleError('Payment cancelled');
                },
                onUnsupported: function (){
                    handleError('Your browser does not support PayPal');
                },
                paypal: {
                    singleUse: true,
                    amount: Drupal.settings.checkout.total,
                    currency: Drupal.settings.checkout.currency,
                    locale: Drupal.settings.checkout.locale,
                    enableShippingAddress: false,
                    headless: true
                }
            });

            $('#paypal-button').bind('click', function(e){
                e.preventDefault();
                checkout.paypal.initAuthFlow();
            });

            $('#paypal-cancel').bind('click', function(e){
                e.preventDefault();
                handleError('Payment Cancelled');
            });

            function handleError(message){
                $('input[name="error"]',context).val(message);
                submitCheckout();
            }

            function submitCheckout(){
                $('#paypal-options').hide();
                $('#paypal-loading').show();
                $('#cart-checkout-step-3-form').submit();
            }
        }
    };
})(jQuery);
