<h4><?= t('Thank you for your purchase') ?></h4>
<div class="divider">
    <p>
        <?= t('Your order confirmation and e-voucher has been emailed to you.') ?>
        <br/>
        <?= t('Order number') ?>: <strong><?= $transaction_number ?></strong>
    </p>
</div>
<div class="divider">
    <p>
        <strong><?= t('Purchase Details') ?>:</strong>
    </p>

    <p>
        <?= $transaction_details ?>
    </p>
</div>
<div>
    <p class="otny-color-link-white">
        <?= l(t('Print My E-Voucher'), 'order/your-voucher.pdf', array(
                'attributes' => array(
                        'target' => '_blank',
                        'class'  => array(
                                'btn',
                                'btn-primary',
                                'otny-bg-blue',
                        ),
                ),
        )) ?>
    </p>

    <p>
        <?= t('Have questions about your purchase? Read our !faq_link.', array('!faq_link' => l(t('FAQ'), 'faq', array('attributes' => array('target' => '_blank'))))) ?>
    </p>
</div>