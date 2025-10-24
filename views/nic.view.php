<?php $title = 'NIC Application - OneID'; ?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page nic-page">
    <section class="page-intro" aria-labelledby="nic-heading">
        <h1 id="nic-heading">National Identity Card Application</h1>
        <p>Start a new application or update your NIC details using the secure online form.</p>

        <?php if ($flowStep !== 'notice'): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn-secondary" style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">← Start Over</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="flow">
        <!-- Notice Step -->
        <div class="flow-step <?= $flowStep === 'notice' ? 'is-active' : '' ?>" <?= $flowStep !== 'notice' ? 'hidden' : '' ?>>
            <section class="notice-card" aria-labelledby="notice-card-heading-nic">
                <div class="notice-card__icon" aria-hidden="true">⚠</div>
                <div class="notice-card__content">
                    <h3 id="notice-card-heading-nic" class="notice-header">Important Notice</h3>
                    <p>This service is provided only for applying for a replacement NIC in the event of a lost card.</p>

                    <ul>
                        <li>Applications for new NICs (first-time applications) are not accepted through this website.</li>
                        <li>Applications for amendments, corrections, renewals, or any other changes to an existing NIC are also not available online.</li>
                        <li>For such cases, please visit the Department of Registration of Persons or your respective Divisional Secretariat Office in person to make the necessary request.</li>
                    </ul>

                    <p class="notice-card__footer">Continue only if your NIC has been lost and you wish to apply for a replacement card.</p>

                    <div class="notice-footer">
                        <form method="POST">
                            <input type="hidden" name="action" value="proceed_notice">
                            <button class="notice-action" type="submit">Proceed</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        <!-- Application Step -->
        <section class="flow-step <?= $flowStep === 'application' ? 'is-active' : '' ?>" <?= $flowStep !== 'application' ? 'hidden' : '' ?>>
            <h2>Identity Card Application</h2>
            <p>Please complete all required sections carefully. The information provided will be used to verify your identity.</p>

            <?php if ($flowError && $flowStep === 'application'): ?>
                <p class="error-message"><?= htmlspecialchars($flowError) ?></p>
            <?php endif; ?>

            <?php require __DIR__ . '/components/nic-form.php'; ?>
        </section>

        <!-- Pricing Step -->
        <section class="flow-step <?= $flowStep === 'pricing' ? 'is-active' : '' ?>" <?= $flowStep !== 'pricing' ? 'hidden' : '' ?>>
            <h2>Pricing</h2>
            <p>The replacement fee for a National Identity Card is fixed.</p>

            <?php // Success message moved to payment-success page after completed payment 
            ?>

            <div class="pricing-summary pricing-summary--fixed">
                <div class="pricing-summary__row">
                    <span>NIC Replacement Fee</span>
                    <span>LKR 1,500.00</span>
                </div>
                <div class="pricing-summary__row">
                    <span>Service Charge</span>
                    <span>LKR 250.00</span>
                </div>
                <div class="pricing-summary_row pricing-summary_total">
                    <span>Total</span>
                    <span>LKR 1,750.00</span>
                </div>
            </div>

            <div class="flow-control">
                <form method="POST">
                    <input type="hidden" name="action" value="proceed_to_payment">
                    <button type="submit" class="btn">Proceed to Payment</button>
                </form>
            </div>
        </section>

        <!-- Payment Step -->
        <section class="flow-step <?= $flowStep === 'payment' ? 'is-active' : '' ?>" <?= $flowStep !== 'payment' ? 'hidden' : '' ?>>
            <h2>Payment Information</h2>
            <p>Enter your payment details to complete the NIC replacement request.</p>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <dl class="order-summary__list">
                    <div class="order-summary__row">
                        <dt>NIC Replacement Fee</dt>
                        <dd>LKR 1,500.00</dd>
                    </div>
                    <div class="order-summary__row">
                        <dt>Service Charge</dt>
                        <dd>LKR 250.00</dd>
                    </div>
                    <div class="order-summary__row">
                        <dt>Delivery Fee</dt>
                        <dd>LKR 200.00</dd>
                    </div>
                    <div class="order-summary__row">
                        <dt>Subtotal</dt>
                        <dd>LKR 1,950.00</dd>
                    </div>
                    <div class="order-summary__row">
                        <dt>Tax (18%)</dt>
                        <dd>LKR 351.00</dd>
                    </div>
                    <div class="order-summary__row order-summary__total">
                        <dt>Total</dt>
                        <dd>LKR 2,301.00</dd>
                    </div>
                </dl>
            </div>

            <?php
            $paymentData = [
                'order_total' => 2301.00,
                'success_variant' => 'nic'
            ];
            require __DIR__ . '/components/payment-form.php';
            ?>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
