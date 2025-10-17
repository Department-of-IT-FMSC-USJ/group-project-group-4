<section class="payment-section" aria-labelledby="payment-information-heading">
    <div class="payment-form-wrapper">
        <form action="/api/payments.php" method="POST" class="form-body">
            <h3 id="payment-information-heading" class="visually-hidden">Payment Information</h3>

            <?php if (isset($paymentData)): ?>
                <?php foreach ($paymentData as $key => $value): ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                <?php endforeach; ?>
            <?php endif; ?>

            <label for="cardholdername">Card Holder's Name</label>
            <input
                type="text"
                id="cardholdername"
                class="textbox"
                name="cardholdername"
                placeholder="Enter Full Name"
                autocomplete="cc-name"
                pattern="^[A-Za-z ]+$"
                maxlength="60"
                aria-describedby="cardholdername-help"
                required>
            <small id="cardholdername-help" class="form-hint">Letters and spaces only (A–Z).</small>

            <label for="cardnumber">Card Number</label>
            <input
                type="text"
                id="cardnumber"
                class="textbox"
                name="cardnumber"
                placeholder="1234 5678 1234 5678"
                inputmode="numeric"
                autocomplete="cc-number"
                maxlength="19"
                pattern="^(?:\d{4}\s){3}\d{4}$"
                required
                aria-describedby="cardnumber-help">
            <small id="cardnumber-help" class="form-hint">16 digits, numbers only. We’ll auto-format as you type.</small>

            <div class="field-row">
                <div>
                    <label for="expirydate">Expiry Date</label>
                    <input type="text" id="expirydate" class="textbox" name="expirydate" placeholder="MM/YY" inputmode="numeric" autocomplete="cc-exp" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" required>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <input type="password" id="cvv" class="textbox" name="cvv" placeholder="123" inputmode="numeric" autocomplete="cc-csc" pattern="^\d{3}$" maxlength="3" required>
                </div>
            </div>

            <label for="payment-email">Contact Email</label>
            <input type="email" id="payment-email" class="textbox" name="email" placeholder="hello@gmail.com" autocomplete="email" required>

            <label for="phonenumber">Phone Number</label>
            <input
                type="tel"
                id="phonenumber"
                class="textbox"
                name="phonenumber"
                placeholder="0701234567"
                inputmode="numeric"
                autocomplete="tel"
                pattern="^\d{10}$"
                maxlength="10"
                aria-describedby="phonenumber-help"
                required>
            <small id="phonenumber-help" class="form-hint">10 digits, numbers only.</small>

            <label for="address">Address</label>
            <textarea id="address" name="address" class="textbox" placeholder="Enter Your Home Address" autocomplete="address-line1" maxlength="200" aria-describedby="address-help" required></textarea>
            <small id="address-help" class="form-hint">Max 200 characters.</small>
            <p class="msg">Documents will be delivered to this address.</p>

            <div class="warn-msg" role="alert">
                <span class="warn-icon" aria-hidden="true">⚠</span>
                <div>
                    <span class="warn-title">Important Notice</span>
                    <p class="notice">Before proceeding with the payment, ensure that all the details you have entered are accurate and complete. Once the payment is made, we do not offer refunds or cancellations under any circumstance.</p>
                </div>
            </div>

            <button type="submit" name="submit" id="submitBtn">Pay</button>
        </form>
    </div>
</section>

<script>
    
    (function() {
        const cardInput = document.getElementById('cardnumber');
        const expInput = document.getElementById('expirydate');
        const cvvInput = document.getElementById('cvv');
        const nameInput = document.getElementById('cardholdername');
        const emailInput = document.getElementById('payment-email');
        const phoneInput = document.getElementById('phonenumber');
        const addressInput = document.getElementById('address');
        const form = cardInput && cardInput.form;

        function formatCard(value) {
            const digits = value.replace(/\D/g, '').slice(0, 16);
            return digits.replace(/(.{4})/g, '$1 ').trim();
        }

        function luhnCheck(number) {
            
            let sum = 0;
            let shouldDouble = false;
            for (let i = number.length - 1; i >= 0; i--) {
                let digit = parseInt(number.charAt(i), 10);
                if (shouldDouble) {
                    digit *= 2;
                    if (digit > 9) digit -= 9;
                }
                sum += digit;
                shouldDouble = !shouldDouble;
            }
            return sum % 10 === 0;
        }

        if (cardInput) {
            cardInput.addEventListener('input', function() {
                const formatted = formatCard(this.value);
                if (this.value !== formatted) this.value = formatted;

                const digits = this.value.replace(/\D/g, '');
                let message = '';
                if (digits.length !== 16) {
                    message = 'Card number must be exactly 16 digits';
                } else if (!luhnCheck(digits)) {
                    message = 'Card number appears invalid (Luhn check)';
                }
                this.setCustomValidity(message);
                if (message) this.reportValidity();
            });
        }

        if (expInput) {
            expInput.addEventListener('input', function() {
                
                let v = this.value.replace(/[^\d]/g, '').slice(0, 4);
                if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
                this.value = v;

                
                const mm = parseInt((v.slice(0, 2) || '0'), 10);
                const validMM = mm >= 1 && mm <= 12;
                const validFmt = /^(0[1-9]|1[0-2])\/\d{2}$/.test(v);
                this.setCustomValidity(validMM && validFmt ? '' : 'Use MM/YY with a valid month');
            });
        }

        if (cvvInput) {
            cvvInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 3);
            });
        }

        if (nameInput) {
            nameInput.addEventListener('input', function() {
                const cleaned = this.value.replace(/[^A-Za-z\s]/g, '');
                if (cleaned !== this.value) this.value = cleaned;
                this.setCustomValidity(/^[A-Za-z ]+$/.test(this.value) ? '' : 'Letters and spaces only');
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
                this.setCustomValidity(/^\d{10}$/.test(this.value) ? '' : 'Phone must have exactly 10 digits');
            });
        }

        if (addressInput) {
            addressInput.addEventListener('input', function() {
                if (this.value.length > 200) this.value = this.value.slice(0, 200);
            });
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                
                const digits = cardInput.value.replace(/\D/g, '');
                if (digits.length !== 16 || !luhnCheck(digits)) {
                    e.preventDefault();
                    cardInput.reportValidity();
                    return false;
                }
                if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expInput.value)) {
                    e.preventDefault();
                    expInput.reportValidity();
                    return false;
                }
                if (!/^\d{3}$/.test(cvvInput.value)) {
                    e.preventDefault();
                    cvvInput.reportValidity();
                    return false;
                }
                if (!/^[A-Za-z ]+$/.test(nameInput.value)) {
                    e.preventDefault();
                    nameInput.reportValidity();
                    return false;
                }
                
                if (emailInput && !/.+@.+\..+/.test(emailInput.value)) {
                    e.preventDefault();
                    emailInput.setCustomValidity('Enter a valid email address');
                    emailInput.reportValidity();
                    return false;
                } else if (emailInput) {
                    emailInput.setCustomValidity('');
                }
                if (!/^\d{10}$/.test(phoneInput.value)) {
                    e.preventDefault();
                    phoneInput.reportValidity();
                    return false;
                }
                if (addressInput.value.length === 0 || addressInput.value.length > 200) {
                    e.preventDefault();
                    addressInput.reportValidity();
                    return false;
                }
                return true;
            });
        }
    })();
</script>