<?php
$title = 'Fine Settlement - OneID';
?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page fines-page">
    <section class="page-intro" aria-labelledby="fines-heading">
        <h1 id="fines-heading">Fine Settlement</h1>
        <p>Review the details of your traffic fines and complete payments securely.</p>

        <?php if ($flowStep !== 'lookup'): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn-secondary" style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">← Start Over</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="flow">
      
        <div class="flow-step <?= $flowStep === 'lookup' ? 'is-active' : '' ?>" <?= $flowStep !== 'lookup' ? 'hidden' : '' ?>>
            <h2>Enter Fine ID</h2>
            <p>Provide the Fine ID, Vehicle Number, or License Number to look up your fine.</p>

            <form class="fine-form" method="POST">
                <input type="hidden" name="action" value="lookup_fine">

                <label for="fineIdInput">Fine ID / Vehicle Number / License Number</label>
                <input type="text" id="fineIdInput" name="fineId" placeholder="Enter Fine ID, Vehicle No, or License No" autocomplete="off" required>

                <?php if ($flowError && $flowStep === 'lookup'): ?>
                    <p class="error-message"><?= htmlspecialchars($flowError) ?></p>
                <?php endif; ?>

                <p class="form-hint"><strong>CAR-1234</strong> (Vehicle), or <strong>DL123456</strong> (License)</p>

                <button type="submit" class="btn">Check fine</button>
            </form>
        </div>

       
        <section class="flow-step <?= $flowStep === 'details' ? 'is-active' : '' ?>" <?= $flowStep !== 'details' ? 'hidden' : '' ?>>
            <h2>Review Fine Details</h2>
            <p>Confirm the details below before proceeding to payment.</p>

            <?php if ($fine): ?>
                <div class="fine-details">
                    <header class="fine-card">
                        <div class="fine-card__meta">
                            <span class="fine-card__label">Fine ID</span>
                            <span class="fine-card__value"><?= htmlspecialchars($fine['fine_id'] ?? '—') ?></span>
                        </div>
                        <div class="fine-card__meta">
                            <span class="fine-card__label">Date</span>
                            <span class="fine-card__value"><?= isset($fine['issued_at']) ? date('M d, Y', strtotime($fine['issued_at'])) : '—' ?></span>
                        </div>
                        <div class="fine-card__meta">
                            <span class="fine-card__label">Driver</span>
                            <span class="fine-card__value"><?= htmlspecialchars($fine['driver_name'] ?? '—') ?></span>
                        </div>
                        <div class="fine-card__meta">
                            <span class="fine-card__label">Vehicle</span>
                            <span class="fine-card__value"><?= htmlspecialchars($fine['vehicle_number'] ?? '—') ?></span>
                        </div>
                        <p class="fine-card__violation"><?= htmlspecialchars($fine['mistake'] ?? 'No violation details') ?></p>
                        <p class="fine-card__location"><?= htmlspecialchars($fine['place'] ?? '—') ?></p>
                        <div class="fine-card__meta">
                            <span class="fine-card__label">Status</span>
                            <span class="fine-card__value"><?= htmlspecialchars($fine['payment_status'] ?? 'Pending') ?></span>
                        </div>
                    </header>

                    <div class="fine-total">
                        <span class="fine-total__label">Amount Due</span>
                        <span class="fine-total__value">LKR <?= number_format($order['fineAmount'] ?? 0, 2) ?></span>
                    </div>
                </div>

                <dl class="fine-breakdown">
                    <div class="fine-breakdown__row">
                        <dt>Fine Amount</dt>
                        <dd>LKR <?= number_format($order['fineAmount'] ?? 0, 2) ?></dd>
                    </div>
                    <div class="fine-breakdown__row">
                        <dt>Service Charge</dt>
                        <dd>LKR <?= number_format($order['serviceFee'] ?? 0, 2) ?></dd>
                    </div>
                    <div class="fine-breakdown__row">
                        <dt>Postal Charge</dt>
                        <dd>LKR <?= number_format($order['postalFee'] ?? 0, 2) ?></dd>
                    </div>
                    <div class="fine-breakdown__row">
                        <dt>Subtotal</dt>
                        <dd>LKR <?= number_format($order['subtotal'] ?? 0, 2) ?></dd>
                    </div>
                    <div class="fine-breakdown__row">
                        <dt>Tax (18%)</dt>
                        <dd>LKR <?= number_format($order['tax'] ?? 0, 2) ?></dd>
                    </div>
                    <div class="fine-breakdown__row fine-breakdown__row--total">
                        <dt>Total Due</dt>
                        <dd>LKR <?= number_format($order['total'] ?? 0, 2) ?></dd>
                    </div>
                </dl>
            <?php endif; ?>

            <div class="flow-control">
                <form method="POST">
                    <input type="hidden" name="action" value="proceed_to_payment">
                    <button type="submit" class="btn">Proceed to Payment</button>
                </form>
            </div>
        </section>

        <section class="flow-step <?= $flowStep === 'payment' ? 'is-active' : '' ?>" <?= $flowStep !== 'payment' ? 'hidden' : '' ?>>
            <h2>Payment Information</h2>
            <p>Settle the outstanding fine using a secure payment method.</p>

            <?php if ($order): ?>
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <dl class="order-summary__list">
                        <div class="order-summary__row">
                            <dt>Fine</dt>
                            <dd>LKR <?= number_format($order['fineAmount'], 2) ?></dd>
                        </div>
                        <div class="order-summary__row">
                            <dt>Service Charge</dt>
                            <dd>LKR <?= number_format($order['serviceFee'], 2) ?></dd>
                        </div>
                        <div class="order-summary__row">
                            <dt>Postal Charge</dt>
                            <dd>LKR <?= number_format($order['postalFee'], 2) ?></dd>
                        </div>
                        <div class="order-summary__row">
                            <dt>Subtotal</dt>
                            <dd>LKR <?= number_format($order['subtotal'], 2) ?></dd>
                        </div>
                        <div class="order-summary__row">
                            <dt>Tax (18%)</dt>
                            <dd>LKR <?= number_format($order['tax'], 2) ?></dd>
                        </div>
                        <div class="order-summary__row order-summary__total">
                            <dt>Total</dt>
                            <dd>LKR <?= number_format($order['total'], 2) ?></dd>
                        </div>
                    </dl>
                </div>
            <?php endif; ?>

            <?php
            $paymentData = [
                'fine_id' => $fine['fine_id'] ?? '',
                'order_total' => $order['total'] ?? 0,
                'success_variant' => 'fine'
            ];
            require __DIR__ . '/components/payment-form.php';
            ?>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>