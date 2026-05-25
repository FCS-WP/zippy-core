<?php

use Zippy_Core\Utils\Zippy_String_Helpers;

?>

<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .logo img {
            width: 100px;
            max-height: 100px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            font-weight: normal;
            font-size: 45px;
            margin: 0;
        }

        .info-table {
            border-top: 2px solid #dbdbdb;
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px;
            vertical-align: top;
        }

        .info-table .invoice-info {
            text-align: right;
        }

        .info-table .invoice-info table {
            margin-left: auto;
            border-collapse: collapse;
        }

        .amount-due {
            background-color: #dcd9d9;
            padding: 4px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }

        .product-table th {
            background: #f7f7f7;
            text-align: left;
            padding: 6px;
        }

        .product-table td {
            padding: 6px;
        }

        .product-table th:nth-child(4),
        .product-table td:nth-child(4) {
            text-align: right;
            width: 15%;
        }

        .totals-wrapper {
            width: 100%;
            margin-top: 15px;
        }

        .totals {
            width: 30%;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 12px;
        }

        .totals td {
            padding: 3px 0;
        }

        .totals td:last-child {
            text-align: right;
        }

        .notes {
            margin-top: 40px;
            font-size: 12px;
            line-height: 1.4em;
        }

        .line {
            border-top: 2px solid #dbdbdb;
            margin: 20px 0;
        }

        .line-row {
            border-top: 2px solid #dbdbdb;
            margin: 10px 0;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="logo" width="40%">
                <img src="<?= $data['store_logo'] ?>" />
            </td>
            <td class="invoice-title" width="60%">
                <h1>TAX INVOICE</h1>
                <p style="color: #888">GST Reg. No. <?= $data['gst_reg'] ?></p>
                <p>
                    <strong><?= $data['store_name'] ?></strong><br />
                    <?
                    foreach ($header_options as $key => $header_option): ?>
                        <?= Zippy_String_Helpers::convert_slug_to_name($key) . ': ' . $header_option ?> <br />
                    <? endforeach; ?>
                </p>
            </td>
        </tr>
    </table>

    <!-- BILL TO -->
    <table class="info-table">
        <tr>
            <td width="50%">
                <p style="color: #888">BILL TO</p>
                <p><b><?= $data['bill_to'] ?></b></p>
            </td>
            <td width="50%" class="invoice-info">
                <table>
                    <tr>
                        <td><b>Invoice Number:</b></td>
                        <td><?= $data['invoice_number'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Invoice Date:</b></td>
                        <td><?= $data['invoice_date'] ?></td>
                    </tr>
                    <tr>
                        <td><b>Payment Due:</b></td>
                        <td><?= $data['payment_due'] ?></td>
                    </tr>
                    <tr class="amount-due">
                        <td colspan="2"><strong>Amount Due (SGD):</strong> $<?= number_format($data['total'], 2) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PRODUCTS -->
    <table class="product-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['items'] as $item): ?>
                <tr>
                    <td class="product-name"><?= $item['name'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price_per_item'] + $item['tax_per_item'], 2) ?></td>
                    <td>$<?= number_format($item['price_total'] + $item['tax_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="line"></div>

    <!-- TOTALS -->
    <div class="totals-wrapper">
        <table class="totals">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>$<?= number_format($data['subtotal'], 2) ?></td>
            </tr>
            <?php if (isset($data['coupons']) && floatval($data['coupons']) > 0): ?>
                <tr>
                    <td><strong>Discount:</strong></td>
                    <td>-$<?= number_format($data['coupons'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <?php if (isset($data['shipping']) && floatval($data['shipping']) > 0): ?>
                <tr>
                    <td><strong>Shipping:</strong></td>
                    <td>$<?= number_format($data['shipping'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <?php if (isset($data['fees']) && floatval($data['fees']) > 0): ?>
                <tr>
                    <td><strong>Fees:</strong></td>
                    <td>$<?= number_format($data['fees'], 2) ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="2">
                    <div class="line-row"></div>
                </td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td><strong>$<?= number_format($data['total'], 2) ?></strong></td>
            </tr>
            <tr>
                <td><strong>Inclusive 9% GST</strong></td>
                <td>$<?= number_format($data['gst'], 2) ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="line-row"></div>
                </td>
            </tr>
            <tr>
                <td><strong>Amount Due (SGD):</strong></td>
                <td><strong>$<?= number_format($data['total'], 2) ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- NOTES -->
    <div class="notes">
        <strong>Terms & Conditions</strong><br />
        • Please retain this receipt as <strong>proof of purchase</strong>.<br />
        • Kindly refer to our <a href="https://www.thenursery.sg/shipping-returns/"><strong>Return & Exchange Policy</strong></a> on <strong>The Nursery</strong> website for full terms and conditions.<br />
        • All returns and exchanges are subject to the sole discretion of <strong>The Nursery</strong>.<br />
        • This receipt must be presented for any exchange or refund requests.<br /><br />

        Should you require any assistance regarding your purchase, please contact us via WhatsApp at <strong>+65 9278 0728</strong> or email us at <strong>orders@thenursery.sg</strong>.
    </div>
</body>

</html>