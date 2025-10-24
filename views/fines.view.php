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
        <!-- Fine Paid/Delivered Step -->
        <div class="flow-step <?= $flowStep === 'done' ? 'is-active' : '' ?>" <?= $flowStep !== 'done' ? 'hidden' : '' ?>>
            <h2><?= $justPaid ? 'Successfully Paid' : 'Fine Already Paid' ?></h2>

            <div class="fine-details">
                <header class="fine-card fine-card--success">
                    <div class="fine-info-list">
                        <div class="fine-card__meta">
                            <span class="fine-card__label">FINE ID</span>
                            <span class="fine-card__value"><?= htmlspecialchars($fine['fine_id'] ?? '—') ?></span>
                        </div>
                        <div class="fine-card__meta">
                            <span class="fine-card__label">STATUS</span>
                            <span class="fine-card__value fine-card__value--success">Completed</span>
                        </div>
                    </div>
                </header>

                <div class="fine-message fine-message--success">
                    <div class="fine-message__icon">✓</div>
                    <div class="fine-message__content">
                        <strong class="fine-message__title">NO FINE TO PAY.</strong>
                        <p class="fine-message__text">
                            <?= $justPaid
                                ? 'Your fine has been successfully paid and the license will be delivered to your doorstep.'
                                : 'Your fine has been paid and the license will be delivered to your doorstep.' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fine Lookup Step -->
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

                <p class="form-hint"><strong>Fine ID:</strong> 1, 2, 3... | <strong>Vehicle:</strong> CAR-1234 | <strong>License:</strong> B5203920</p>

                <button type="submit" class="btn">Check fine</button>
            </form>

            <script>
                (function() {
                    const fineInput = document.getElementById('fineIdInput');
                    if (!fineInput) return;

                    fineInput.addEventListener('input', function(e) {
                        const value = e.target.value.toUpperCase();
                        e.target.value = value; // Auto uppercase

                        // Allow letters, numbers, and hyphens
                        const regex = /^[A-Z0-9\-]*$/;

                        if (!regex.test(value)) {
                            fineInput.setCustomValidity('Only letters, numbers, and hyphens are allowed');
                            fineInput.style.borderColor = 'red';
                        } else if (value.length > 0) {
                            // Check specific formats
                            const isFineId = /^\d+$/.test(value);
                            const isVehicle = /^[A-Z]{3}-[0-9]{4}$/.test(value);
                            const isLicense = /^[A-Z][0-9]{7}$/.test(value);
                            const isPartialVehicle = /^[A-Z]{0,3}(-[0-9]{0,4})?$/.test(value);
                            const isPartialLicense = /^[A-Z]?[0-9]{0,7}$/.test(value);

                            if (isFineId || isVehicle || isLicense || isPartialVehicle || isPartialLicense) {
                                fineInput.setCustomValidity('');
                                fineInput.style.borderColor = '';
                            } else {
                                fineInput.setCustomValidity('Format: Fine ID (number), Vehicle (ABC-1234), or License (B1234567)');
                                fineInput.style.borderColor = 'orange';
                            }
                        } else {
                            fineInput.setCustomValidity('');
                            fineInput.style.borderColor = '';
                        }
                    });

                    // Prevent invalid characters from being typed
                    fineInput.addEventListener('keypress', function(e) {
                        const char = e.key;
                        if (!/[A-Za-z0-9\-]/.test(char)) {
                            e.preventDefault();
                        }
                    });
                })();
            </script>
        </div>


        <section class="flow-step <?= $flowStep === 'details' ? 'is-active' : '' ?>" <?= $flowStep !== 'details' ? 'hidden' : '' ?>>
            <h2>Review Fine Details</h2>
            <p>Confirm the details below before proceeding to payment.</p>

            <?php if ($fine): ?>
                <div class="fine-details">
                    <div class="fine-card">
                        <table class="fine-details-table">
                            <tbody>
                                <tr>
                                    <td class="fine-label">FINE ID</td>
                                    <td class="fine-value"><?= htmlspecialchars($fine['fine_id'] ?? '—') ?></td>
                                </tr>
                                <tr>
                                    <td class="fine-label">DATE</td>
                                    <td class="fine-value"><?= isset($fine['issued_at']) ? date('M d, Y', strtotime($fine['issued_at'])) : '—' ?></td>
                                </tr>
                                <tr>
                                    <td class="fine-label">DRIVER</td>
                                    <td class="fine-value"><?= htmlspecialchars($fine['driver_name'] ?? '—') ?></td>
                                </tr>
                                <tr>
                                    <td class="fine-label">VEHICLE</td>
                                    <td class="fine-value"><?= htmlspecialchars($fine['vehicle_number'] ?? '—') ?></td>
                                </tr>
                                <tr>
                                    <td class="fine-label" colspan="2" style="padding-top: 1rem; padding-bottom: 0.5rem;">
                                        <?= htmlspecialchars($fine['mistake'] ?? 'No violation details') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fine-label" colspan="2" style="padding-bottom: 1rem; color: #6b7280;">
                                        <?= htmlspecialchars($fine['place'] ?? '—') ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fine-label">STATUS</td>
                                    <td class="fine-value"><?= htmlspecialchars($fine['payment_status'] ?? 'Pending') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

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
