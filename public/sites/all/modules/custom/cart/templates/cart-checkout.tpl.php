<div class="cart-checkout">
  <div class="cart-checkout-header">
    <?=render($header);?>
  </div>
  <div class="cart-checkout-form">
    <?=render($form);?>
  </div>
    <div class="cart-checkout-ticket-table">
        <?=render($ticket_table);?>
    </div>
    <div class="cart-checkout-extra-info">
        <div id="cart-checkout-extra-info">
            <?= render($extra_info); ?>
        </div>
    </div>
</div>