<?php
$orderData = $orderData ?? [
    'primary' => ['label' => 'Government Fee', 'value' => 'LKR 1,000.00'],
    'service' => ['label' => 'Service Charge', 'value' => 'LKR 100.00'],
    'postal' => ['label' => 'Postal Charge', 'value' => 'LKR 60.00'],
    'subtotal' => ['label' => 'Subtotal', 'value' => 'LKR 1,160.00'],
    'tax' => ['label' => 'Tax (18%)', 'value' => 'LKR 208.80'],
    'total' => ['label' => 'Total', 'value' => 'LKR 1,368.80'],
];
?>

<div class="order-summary" data-order-summary>
    <h3>Order Summary</h3>
    <dl class="order-summary__list">
        <div class="order-summary__row" data-order-row="primary">
            <dt data-order-label="primary"><?= htmlspecialchars($orderData['primary']['label']) ?></dt>
            <dd data-order-value="primary"><?= htmlspecialchars($orderData['primary']['value']) ?></dd>
        </div>
        <div class="order-summary__row" data-order-row="service">
            <dt data-order-label="service"><?= htmlspecialchars($orderData['service']['label']) ?></dt>
            <dd data-order-value="service"><?= htmlspecialchars($orderData['service']['value']) ?></dd>
        </div>
        <div class="order-summary__row" data-order-row="postal">
            <dt data-order-label="postal"><?= htmlspecialchars($orderData['postal']['label']) ?></dt>
            <dd data-order-value="postal"><?= htmlspecialchars($orderData['postal']['value']) ?></dd>
        </div>
        <div class="order-summary__row" data-order-row="subtotal">
            <dt data-order-label="subtotal"><?= htmlspecialchars($orderData['subtotal']['label']) ?></dt>
            <dd data-order-value="subtotal"><?= htmlspecialchars($orderData['subtotal']['value']) ?></dd>
        </div>
        <div class="order-summary__row" data-order-row="tax">
            <dt data-order-label="tax"><?= htmlspecialchars($orderData['tax']['label']) ?></dt>
            <dd data-order-value="tax"><?= htmlspecialchars($orderData['tax']['value']) ?></dd>
        </div>
        <div class="order-summary__row order-summary__total" data-order-row="total">
            <dt data-order-label="total"><?= htmlspecialchars($orderData['total']['label']) ?></dt>
            <dd data-order-value="total"><?= htmlspecialchars($orderData['total']['value']) ?></dd>
        </div>
    </dl>
</div>