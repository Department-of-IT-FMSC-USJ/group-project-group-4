<?php
$title = 'Birth Certificate - OneID';
?>
<?php require __DIR__ . '/partials/head.php'; ?>
<?php require __DIR__ . '/partials/nav.php'; ?>

<main class="page birthcertificate-page">
    <section class="page-intro" aria-labelledby="birthcertificate-heading">
        <h1 id="birthcertificate-heading">Birth Certificate Services</h1>
        <p>Follow the guided steps to request certified copies of your birth certificate.</p>

        <?php if ($flowStep !== 'notice'): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="reset">
                <button type="submit" class="btn-secondary" style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">← Start Over</button>
            </form>
        <?php endif; ?>
    </section>

    <section class="flow">

        <div class="flow-step <?= $flowStep === 'notice' ? 'is-active' : '' ?>" <?= $flowStep !== 'notice' ? 'hidden' : '' ?>>
            <section class="notice-card" aria-labelledby="notice-card-heading-birth">
                <div class="notice-card__icon" aria-hidden="true">⚠️</div>
                <div class="notice-card__content">
                    <h3 id="notice-card-heading-birth" class="notice-header">Important Notice</h3>
                    <p>We issue copies of birth certificates only when a valid birth certificate number is provided. If you do not have the number, we are unable to process your request through this service.</p>

                    <ul>
                        <li>If you already have a NIC (National Identity Card), you can obtain a copy of your birth certificate directly from your Divisional Secretariat Office.</li>
                        <li>If you do not have a NIC, you may also visit the Divisional Secretariat Office and provide the required information to request your birth certificate.</li>
                    </ul>

                    <p class="notice-card__footer">Please have your birth certificate number ready before you proceed.</p>

                    <div class="notice-footer">
                        <form method="POST">
                            <input type="hidden" name="action" value="proceed_notice">
                            <button class="notice-action" type="submit">Proceed</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>


        <form class="flow-step <?= $flowStep === 'certificate' ? 'is-active' : '' ?>" <?= $flowStep !== 'certificate' ? 'hidden' : '' ?> method="POST">
            <input type="hidden" name="action" value="lookup_certificate">
            <h2>Enter Birth Certificate Number</h2>
            <p>Enter the certificate number exactly as stored in records (e.g., BC001).</p>

            <label for="birthCertificateNumber">Birth Certificate Number</label>
            <input type="text" id="birthCertificateNumber" name="birthCertificateNumber" placeholder="e.g., BC001" autocomplete="off" maxlength="10" pattern="^BC\d+$" title="Must start with BC followed by numbers (e.g., BC001)" required>

            <?php if ($flowError && $flowStep === 'certificate'): ?>
                <p class="error-message"><?= htmlspecialchars($flowError) ?></p>
            <?php endif; ?>

            <p class="form-hint">We will validate these details with government records in the next steps.</p>

            <button type="submit" class="btn">Proceed</button>
        </form>

        <script>
            (function() {
                const bcInput = document.getElementById('birthCertificateNumber');
                if (!bcInput) return;

                bcInput.addEventListener('input', function(e) {
                    let value = e.target.value.toUpperCase();
                    e.target.value = value; // Auto uppercase

                    // Must start with BC followed by numbers only
                    const regex = /^BC\d*$/;

                    if (!regex.test(value)) {
                        if (value.length === 0) {
                            bcInput.setCustomValidity('');
                            bcInput.style.borderColor = '';
                        } else if (!value.startsWith('BC')) {
                            bcInput.setCustomValidity('Must start with BC');
                            bcInput.style.borderColor = 'red';
                        } else {
                            bcInput.setCustomValidity('Only numbers allowed after BC');
                            bcInput.style.borderColor = 'red';
                        }
                    } else if (value.length > 0 && value.length < 3) {
                        bcInput.setCustomValidity('Must be at least BC followed by numbers (e.g., BC001)');
                        bcInput.style.borderColor = 'red';
                    } else {
                        bcInput.setCustomValidity('');
                        bcInput.style.borderColor = '';
                    }
                });

                // Prevent invalid characters from being typed
                bcInput.addEventListener('keypress', function(e) {
                    const char = e.key;
                    const currentValue = bcInput.value.toUpperCase();

                    // First two characters must be B and C
                    if (currentValue.length === 0 && char.toUpperCase() !== 'B') {
                        e.preventDefault();
                    } else if (currentValue.length === 1 && char.toUpperCase() !== 'C') {
                        e.preventDefault();
                    } else if (currentValue.length >= 2 && !/\d/.test(char)) {
                        // After BC, only digits allowed
                        e.preventDefault();
                    }
                });
            })();
        </script>


        <form class="flow-step <?= $flowStep === 'verification' ? 'is-active' : '' ?>" <?= $flowStep !== 'verification' ? 'hidden' : '' ?> method="POST">
            <input type="hidden" name="action" value="verify_identity">
            <h2>Security Verification</h2>
            <p>Provide details that match the birth record.</p>

            <label for="placeOfBirth">Place of Birth (from record)</label>
            <input type="text" id="placeOfBirth" name="placeOfBirth" placeholder="Enter the place of birth from your certificate" autocomplete="off" required>

            <label for="fatherPlaceOfBirth">Father's Place of Birth (from record)</label>
            <input type="text" id="fatherPlaceOfBirth" name="fatherPlaceOfBirth" placeholder="Enter father's place of birth from your certificate" autocomplete="off" required>

            <label for="motherPlaceOfBirth">Mother's Place of Birth (from record)</label>
            <input type="text" id="motherPlaceOfBirth" name="motherPlaceOfBirth" placeholder="Enter mother's place of birth from your certificate" autocomplete="off" required>

            <?php if ($flowError && $flowStep === 'verification'): ?>
                <p class="error-message"><?= htmlspecialchars($flowError) ?></p>
            <?php endif; ?>

            <button type="submit" class="btn">Verify Identity</button>
        </form>


        <section class="flow-step copies-step <?= $flowStep === 'copies' ? 'is-active' : '' ?>" <?= $flowStep !== 'copies' ? 'hidden' : '' ?>>
            <h2>Select Number of Copies</h2>
            <p>Choose the number of certified copies you require. Fees are calculated automatically.</p>

            <form method="POST" style="margin-bottom: 2rem;">
                <input type="hidden" name="action" value="select_copies">

                <div style="display: flex; flex-direction: column; gap: 0.5rem; max-width: 500px;">
                    <label for="copyQuantity" style="font-weight: 600; font-size: 1rem;">Number of Copies</label>
                    <select id="copyQuantity" name="copyQuantity" required onchange="this.form.submit()"
                        style="padding: 12px 16px; font-size: 1rem; border: 1px solid #ddd; border-radius: 8px; background: white; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%278%27%3e%3cpath fill=%27%23333%27 d=%27M0 0l6 8 6-8z%27/%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 12px center;">
                        <option value="" disabled selected>Select copies</option>
                        <option value="1">1 Copy</option>
                        <option value="2">2 Copies</option>
                        <option value="3">3 Copies</option>
                        <option value="4">4 Copies</option>
                    </select>
                </div>
            </form>

            <?php if ($order): ?>
                <div class="pricing-summary">
                    <div class="pricing-summary__row">
                        <span>Copies</span>
                        <span><?= $order['quantity'] ?> <?= $order['quantity'] === 1 ? 'Copy' : 'Copies' ?></span>
                    </div>
                    <div class="pricing-summary__row">
                        <span>Copies Charge</span>
                        <span>LKR <?= number_format($order['copiesCost'], 2) ?></span>
                    </div>
                    <div class="pricing-summary__row">
                        <span>Service Charge</span>
                        <span>LKR <?= number_format($order['serviceFee'], 2) ?></span>
                    </div>
                    <div class="pricing-summary__row">
                        <span>Postal Charge</span>
                        <span>LKR <?= number_format($order['postalFee'], 2) ?></span>
                    </div>
                    <div class="pricing-summary__row">
                        <span>Subtotal</span>
                        <span>LKR <?= number_format($order['subtotal'], 2) ?></span>
                    </div>
                    <div class="pricing-summary__row">
                        <span>Tax (18%)</span>
                        <span>LKR <?= number_format($order['tax'], 2) ?></span>
                    </div>
                    <div class="pricing-summary__row pricing-summary__total">
                        <span>Total</span>
                        <span>LKR <?= number_format($order['total'], 2) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </section>


        <section class="flow-step <?= $flowStep === 'payment' ? 'is-active' : '' ?>" <?= $flowStep !== 'payment' ? 'hidden' : '' ?>>
            <h2>Payment Information</h2>
            <p>Enter your payment details to complete the request.</p>

            <?php if ($order): ?>
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <dl class="order-summary__list">
                        <div class="order-summary__row">
                            <dt>Copies</dt>
                            <dd><?= $order['quantity'] ?> <?= $order['quantity'] === 1 ? 'Copy' : 'Copies' ?></dd>
                        </div>
                        <div class="order-summary__row">
                            <dt>Copies Charge</dt>
                            <dd>LKR <?= number_format($order['copiesCost'], 2) ?></dd>
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
                'birth_certificate_number' => $certificate['birth_certificate_number'] ?? '',
                'order_total' => $order['total'] ?? 0,
                'success_variant' => 'birth'
            ];
            require __DIR__ . '/components/payment-form.php';
            ?>
        </section>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
