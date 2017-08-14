<?php /** @var TicketingEngineTransaction $transaction */ ?>

<script type="text/javascript">
    ga('require', 'ecommerce');

    ga('ecommerce:addTransaction', {
        'id': '<?=$transaction->getProviderReference()?>',
        'affiliation': 'Open Loop NY',
        'revenue': '<?=$transaction->getTransactionTotal()?>',
        'shipping': '0',
        'tax': '0'
    });

    <?php if($transaction->getAdults()):?>
    ga('ecommerce:addItem', {
        'id': '<?=$transaction->getProviderReference()?>',
        'name': '<?=$transaction->getTourName()?>',
        'sku': '<?=$transaction->getProductCode()?>',
        'category': '<?=$transaction->getOffering()->getPackage()?>',
        'price': '<?=$transaction->getPurchasePrices()['adult']?>',
        'quantity': '<?=$transaction->getAdults()?>'
    });
    <?php endif;?>

    <?php if($transaction->getChildren()):?>
    ga('ecommerce:addItem', {
        'id': '<?=$transaction->getProviderReference()?>',
        'name': '<?=$transaction->getTourName()?>',
        'sku': '<?=$transaction->getProductCode()?>',
        'category': '<?=$transaction->getOffering()->getPackage()?>',
        'price': '<?=$transaction->getPurchasePrices()['child']?>',
        'quantity': '<?=$transaction->getChildren()?>'
    });
    <?php endif;?>

    <?php foreach($transaction->getLineItems() as $line_item):?>
        <?php if($line_item['type'] == 'coupon'):?>
            ga('ecommerce:addItem', {
                'id': '<?=$transaction->getProviderReference()?>',
                'name': '<?=$line_item['label']?>',
                'sku': '<?=$line_item['data']['coupon_code']?>',
                'category': 'COUPON',
                'price': '<?=$line_item['amount']?>',
                'quantity': '1'
            });
        <?php endif;?>
    <?php endforeach;?>

    ga('ecommerce:send');
</script>