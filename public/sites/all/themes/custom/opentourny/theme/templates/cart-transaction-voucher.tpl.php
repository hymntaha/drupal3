<?php
/** @var TicketingEngineTransaction $transaction */
?>
<table style="width:670px; border-top:1px solid #000;border-spacing:0;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <tr>
        <td width="70%"
            style="padding-top:20px;padding-bottom: 20px;border-bottom:1px solid #000;vertical-align: middle;">
            <img style="margin-bottom:10px;"
                 src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/logo-voucher.png', array('absolute' => TRUE)) ?>"
                 alt=""/>
            <h1>E-VOUCHER</h1>
            <p>Please present this e-voucher for redemption.</p>
        </td>
        <td width="30%"
            style="padding-bottom: 20px;border-bottom:1px solid #000;text-align: center;vertical-align: middle;">
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
    <?php foreach ($transaction->getLineItems() as $line_item): ?>
        <?php if ($line_item['type'] == 'coupon'): ?>
            <tr>
                <td width="50%">
                    Coupon
                </td>
                <td width="50%">
                    <strong><?= $line_item['label'] ?></strong>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
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
<table style="width:670px; font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <tr>
        <td style="width:50%;height:500px;padding-top:10px;" valign="top">
            <p><strong>Tour Information:</strong></p><br />
            <?= $transaction->getTourRedeemText('en') ?>
        </td>
        <td style="width:50%;height:500px;padding-top:10px;" valign="top">
            <p><strong>Terms &amp; Conditions:</strong></p><br />
            <?= $transaction->getTermsText() ?>
        </td>
    </tr>
</table>
<table style="width:670px; border-spacing:0;font-family:'Calibre, Arial, Helvetica, sans-serif'">
    <tr>
        <td style="width:25%;">
            <img width="100" height="29"
                 src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/logo-voucher-small.png', array('absolute' => TRUE)) ?>"
                 alt="Openloop logo" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <img width="29" height="29"
                 src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/logo-extrapolitan-voucher.png', array('absolute' => TRUE)) ?>"
                 alt="Extrapolitan logo" />
        </td>
        <td style="width:50%; text-align: center;">
            <strong>info@openloop-ny.com</strong><br />
            212-371-6736
        </td>
        <td style="width:25%;text-align: right;">
            <table>
                <tr>
                    <td>
                        <a href="https://secure.pagemodo.com/m/DYCZWH">
                            <img src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/ico-facebook.png', array('absolute' => TRUE)) ?>"
                                 alt="facebook"/>
                        </a>
                    </td>
                    <td>
                        <a href="https://twitter.com/openloopny">
                            <img src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/ico-twitter.png', array('absolute' => TRUE)) ?>"
                                 alt="twitter"/>
                        </a>
                    </td>
                    <td>
                        <a href="https://instagram.com/openloopny">
                            <img src="<?= url(drupal_get_path('theme', 'opentourny') . '/images/ico-instagram.png', array('absolute' => TRUE)) ?>"
                                 alt="instagram"/>
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>