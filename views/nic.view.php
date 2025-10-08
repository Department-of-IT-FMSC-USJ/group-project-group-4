<?php $title = 'NIC Application - OneID'; ?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page nic-page">
    <section class="page-intro" aria-labelledby="nic-heading">
        <h1 id="nic-heading">National Identity Card Application</h1>
        <p>Start a new application or update your NIC details using the secure online form.</p>
    </section>

    <section class="flow" data-flow="nic" data-initial-step="notice" data-success-variant="nic" data-order-primary-label="NIC Replacement Fee" data-order-primary-value="LKR 1,500.00">
        <!-- Notice Step -->
        <div class="flow-step" data-step="notice">
            <section class="notice-card" aria-labelledby="notice-card-heading-nic">
                <div class="notice-card__icon" aria-hidden="true">⚠️</div>
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
                        <button class="notice-action" type="button" data-flow-next="application" data-flow-action="next">Proceed</button>
                    </div>
                </div>
            </section>
        </div>

        <!-- Application Step -->
        <section class="flow-step" data-step="application" data-flow-persist="order" hidden>
            <h2>Identity Card Application</h2>
            <p>Please complete all required sections carefully. The information provided will be used to verify your identity.</p>

            <?php require __DIR__ . '/components/nic-form.php'; ?>

            <div class="flow-control">
                <button type="button" class="btn" data-flow-action="next" data-flow-next="pricing">Submit Application</button>
            </div>
        </section>

        <!-- Pricing Step -->
        <section class="flow-step" data-step="pricing" hidden>
            <h2>Pricing</h2>
            <p>The replacement fee for a National Identity Card is fixed.</p>

            <div class="pricing-summary pricing-summary--fixed">
                <div class="pricing-summary__row">
                    <span>NIC Replacement Fee</span>
                    <span data-pricing-field="primary">LKR 1,500.00</span>
                </div>
                <div class="pricing-summary__row">
                    <span>Service Charge</span>
                    <span data-pricing-field="service">LKR 250.00</span>
                </div>
                <div class="pricing-summary__row pricing-summary__total">
                    <span>Total</span>
                    <span data-pricing-field="total">LKR 1,750.00</span>
                </div>
            </div>

            <div class="flow-control">
                <button type="button" class="btn" data-flow-action="next" data-flow-next="payment">Proceed to Payment</button>
            </div>
        </section>

        <!-- Payment Step -->
        <section class="flow-step" data-step="payment" hidden>
            <h2>Payment Information</h2>
            <p>Enter your payment details to complete the NIC replacement request.</p>

            <?php require __DIR__ . '/components/order-summary.php'; ?>
            <?php require __DIR__ . '/components/payment-form.php'; ?>
        </section>

        <!-- Success Step -->
        <section class="flow-step success-step" data-step="success" hidden>
            <?php require __DIR__ . '/components/success-message.php'; ?>
            <div class="flow-control">
                <button type="button" class="btn" data-flow-action="reset">Submit another application</button>
            </div>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>