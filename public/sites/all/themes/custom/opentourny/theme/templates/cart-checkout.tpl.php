<div class="cart-checkout-header clearfix">
    <div class="col-xs-12 header-text">
        <?= render($header); ?>
    </div>
</div>

<div class="cart-checkout otny-color-anthracite clearfix">

    <div class="col-xs-12">
        <div class="row">
            <?php if (!empty($ticket_table)): ?>
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="cart-checkout-form">
                                <?= render($form); ?>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="cart-checkout-ticket-table">
                                <?= render($ticket_table); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="cart-checkout-extra-info">
                        <div id="cart-checkout-extra-info">
                            <?= render($extra_info); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-xs-12">
                    <div class="checkout-form">
                        <?= render($form); ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>

</div>