<?php
/** @var TicketingEngineTransaction $transaction */
?>
<table style="width:670px; border-top:1px solid #000;border-spacing:0;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <tr>
        <td colspan="2" style="padding-top:10px; padding-bottom:20px;">
            <h1>YOUR E-VOUCHER</h1>

            <p>
                Please print this e-voucher & have it with you for redemption
            </p>
        </td>
    </tr>
    <tr>
        <td width="40%"
            style="padding-bottom: 20px;border-bottom:1px solid #000;">
        </td>
        <td width="60%"
            style="padding-bottom: 20px;border-bottom:1px solid #000;text-align: center;">
            <img src="<?= $transaction->getBarCodeBase64() ?>"><br/>
            <span><?= $transaction->getProviderReference() ?></span>
        </td>
    </tr>
</table>
<table style="width:670px;border-spacing:0;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <tr>
        <td width="50%" style="padding-top:10px;">
            <h2>Order details</h2>
        </td>
        <td width="50%" style="padding-top:10px;">
            &nbsp;
        </td>
    </tr>
    <tr>
        <td width="50%">&nbsp;</td>
        <td width="50%">&nbsp;</td>
    </tr>
    <tr>
        <td width="50%">
            Tour purchased
        </td>
        <td width="50%">
            <strong><?= $transaction->getTourDescription('en') ?></strong>
        </td>
    </tr>
    <tr>
        <td width="50%">
            Number of adults
        </td>
        <td width="50%">
            <strong><?= $transaction->getAdults() ?></strong>
        </td>
    </tr>
    <tr>
        <td width="50%">
            Number of children
        </td>
        <td width="50%">
            <strong><?= $transaction->getChildren() ?></strong>
        </td>
    </tr>
    <tr>
        <td width="50%">
            Purchased by
        </td>
        <td width="50%">
            <strong><?= $transaction->getFirstName() . ' ' . $transaction->getLastName() ?></strong>
        </td>
    </tr>
    <tr>
        <td width="50%">
            Transaction number
        </td>
        <td width="50%">
            <strong><?= $transaction->getProviderReference() ?></strong>
        </td>
    </tr>
    <?php if ($transaction->getOffering(0)->isDated()): ?>
        <tr>
            <td width="50%">
                Date of tour (mm/dd/yyyy)
            </td>
            <td width="50%">
                <strong><?= $transaction->getValidity() ?></strong>
            </td>
        </tr>
    <?php else: ?>
        <tr>
            <td width="50%">
                E-Voucher validity
            </td>
            <td width="50%">
                <strong><?= $transaction->getValidity() ?></strong>
            </td>
        </tr>
        <tr>
            <td width="50%">
                Date of issue (mm/dd/yyyy)
            </td>
            <td width="50%">
                <strong><?= $transaction->getTransactionDate('m/d/Y') ?></strong>
            </td>
        </tr>
    <?php endif ?>
    <?php foreach($transaction->getLineItems() as $line_item):?>
        <?php if($line_item['type'] == 'coupon'):?>
                <tr>
                    <td width="50%">
                        Coupon
                    </td>
                    <td width="50%">
                        <strong><?=$line_item['label']?></strong>
                    </td>
                </tr>
        <?php endif;?>
    <?php endforeach;?>
    <tr>
        <td width="50%">&nbsp;</td>
        <td width="50%">&nbsp;</td>
    </tr>
    <tr>
        <td width="50%"
            style="padding-bottom:10px;border-bottom:1px solid #000;">
            Total price
        </td>
        <td width="50%"
            style="padding-bottom:10px;border-bottom:1px solid #000;">
            <strong><?= $transaction->getTransactionTotalDisplay() ?></strong>
        </td>
    </tr>
</table>
<div style="width:670px;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <div style="float:left;width:320px; padding-top:10px; padding-right:15px; height:420px;">
        <p><strong>Tour Information:</strong></p>
        <?= $transaction->getTourRedeemText('en') ?>
    </div>
    <div style="float:left;width:335px; padding-top:10px; height:420px;">
        <p><strong>Terms & Conditions:</strong></p>
        <?= $transaction->getTermsText() ?>
    </div>
</div>
<div style="width:670px;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <div style="float:left;width:56px;">
        <img width="56" height="56"
             src="<?= file_create_url(drupal_get_path('theme', 'extrapolitan') . '/images/logo-for-voucher.jpg') ?>"
             alt="Extrapolitan logo"/>
    </div>
    <div align="center" style="float:left;width:614px;padding-top:12px;">
        <strong>www.openloop-ny.com</strong>
    </div>
</div>