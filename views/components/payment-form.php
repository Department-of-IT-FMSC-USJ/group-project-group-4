<section class="payment-section" aria-labelledby="payment-information-heading">
    <div class="payment-form-wrapper">
        <form action="#" method="post" class="form-body" novalidate>
            <h3 id="payment-information-heading" class="visually-hidden">Payment Information</h3>

            <label for="cardholdername">Card Holder's Name</label>
            <input type="text" id="cardholdername" class="textbox" name="cardholdername" placeholder="Enter Full Name" autocomplete="cc-name" required>

            <label for="cardnumber">Card Number</label>
            <input type="text" id="cardnumber" class="textbox" name="cardnumber" placeholder="1234 5678 1234 5678" inputmode="numeric" autocomplete="cc-number" required>

            <div class="field-row">
                <div>
                    <label for="expirydate">Expiry Date</label>
                    <input type="text" id="expirydate" class="textbox" name="expirydate" placeholder="MM/YY" inputmode="numeric" autocomplete="cc-exp" required>
                </div>
                <div>
                    <label for="cvv">CVV</label>
                    <input type="password" id="cvv" class="textbox" name="cvv" placeholder="123" inputmode="numeric" autocomplete="cc-csc" required>
                </div>
            </div>

            <label for="payment-email">Contact Email</label>
            <input type="email" id="payment-email" class="textbox" name="email" placeholder="hello@gmail.com" autocomplete="email" required>

            <label for="phonenumber">Phone Number</label>
            <input type="tel" id="phonenumber" class="textbox" name="phonenumber" placeholder="07012345678" inputmode="tel" autocomplete="tel" required>

            <label for="address">Address</label>
            <textarea id="address" name="address" class="textbox" placeholder="Enter Your Home Address" autocomplete="address-line1" required></textarea>
            <p class="msg">Documents will be delivered to this address.</p>

            <div class="warn-msg" role="alert">
                <span class="warn-icon" aria-hidden="true">⚠</span>
                <div>
                    <span class="warn-title">Important Notice</span>
                    <p class="notice">Before proceeding with the payment, ensure that all the details you have entered are accurate and complete. Once the payment is made, we do not offer refunds or cancellations under any circumstance.</p>
                </div>
            </div>

            <button type="submit" name="submit" id="submitBtn" data-flow-action="next" data-flow-next="success">Pay</button>
        </form>
    </div>
</section>