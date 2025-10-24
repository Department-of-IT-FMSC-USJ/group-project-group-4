<?php $old = $_SESSION['nic_form_data'] ?? []; ?>
<form id="identity-form" method="post" action="/nic.php" enctype="multipart/form-data" class="nic-application nic-form-frame" novalidate>
    <input type="hidden" name="action" value="submit_application">
    <!-- Personal Information -->
    <section class="nic-section" aria-labelledby="personal-info">
        <header class="nic-section__header">
            <h3 id="personal-info">Personal Information</h3>
            <p class="nic-section__hint">Please fill out all required information accurately.</p>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="familyName">Family Name <span>*</span></label>
                <input type="text" id="familyName" name="familyName" placeholder="Family Name" required value="<?= htmlspecialchars($old['familyName'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="givenName">Name <span>*</span></label>
                <input type="text" id="givenName" name="givenName" placeholder="Name" required value="<?= htmlspecialchars($old['givenName'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="surname">Surname <span>*</span></label>
                <input type="text" id="surname" name="surname" placeholder="Surname" required value="<?= htmlspecialchars($old['surname'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="idFamilyName">ID Card Family Name</label>
                <input type="text" id="idFamilyName" name="idFamilyName" placeholder="ID Card Family Name" value="<?= htmlspecialchars($old['idFamilyName'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="idGivenName">ID Card Name</label>
                <input type="text" id="idGivenName" name="idGivenName" placeholder="ID Card Name" value="<?= htmlspecialchars($old['idGivenName'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="idSurname">ID Card Surname</label>
                <input type="text" id="idSurname" name="idSurname" placeholder="ID Card Surname" value="<?= htmlspecialchars($old['idSurname'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field nic-field--select">
                <label for="sex">Sex <span>*</span></label>
                <select name="sex" id="sex" required>
                    <option value="">Select Sex</option>
                    <option <?= (isset($old['sex']) && $old['sex'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option <?= (isset($old['sex']) && $old['sex'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option <?= (isset($old['sex']) && $old['sex'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="nic-field nic-field--select">
                <label for="civilStatus">Civil Status <span>*</span></label>
                <select name="civilStatus" id="civilStatus" required>
                    <option value="">Select Civil Status</option>
                    <option <?= (isset($old['civilStatus']) && $old['civilStatus'] === 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option <?= (isset($old['civilStatus']) && $old['civilStatus'] === 'Married') ? 'selected' : ''; ?>>Married</option>
                    <option <?= (isset($old['civilStatus']) && $old['civilStatus'] === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                    <option <?= (isset($old['civilStatus']) && $old['civilStatus'] === 'Divorced') ? 'selected' : ''; ?>>Divorced</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="profession">Profession <span>*</span></label>
                <input type="text" id="profession" name="profession" placeholder="Profession" required value="<?= htmlspecialchars($old['profession'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="dob">Date of Birth <span>*</span></label>
                <input type="date" id="dob" name="dob" placeholder="yyyy-mm-dd" min="1900-01-01" max="<?= date('Y-m-d') ?>" pattern="\d{4}-\d{2}-\d{2}" required value="<?= htmlspecialchars($old['dob'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Birth Information -->
    <section class="nic-section" aria-labelledby="birth-info">
        <header class="nic-section__header">
            <h3 id="birth-info">Birth Information</h3>
        </header>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="birthCertNo">Birth Certificate Number <span>*</span></label>
                <input type="text" id="birthCertNo" name="birthCertNo" placeholder="e.g., BC001" autocomplete="off" maxlength="10" pattern="^BC\d+$" title="Must start with BC followed by numbers (e.g., BC001)" required value="<?= htmlspecialchars($old['birthCertNo'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="placeOfBirth">Place of Birth <span>*</span></label>
                <input type="text" id="placeOfBirth" name="placeOfBirth" placeholder="Place of Birth" required value="<?= htmlspecialchars($old['placeOfBirth'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="birthDivision">Birth Division <span>*</span></label>
                <input type="text" id="birthDivision" name="birthDivision" placeholder="Birth Division" required value="<?= htmlspecialchars($old['birthDivision'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="birthDistrict">Birth District <span>*</span></label>
                <input type="text" id="birthDistrict" name="birthDistrict" placeholder="Birth District" required value="<?= htmlspecialchars($old['birthDistrict'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="countryOfBirth">Country of Birth</label>
                <input type="text" id="countryOfBirth" name="countryOfBirth" placeholder="Sri Lanka" value="<?= htmlspecialchars($old['countryOfBirth'] ?? 'Sri Lanka') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="cityOfBirth">City of Birth</label>
                <input type="text" id="cityOfBirth" name="cityOfBirth" placeholder="City of Birth" value="<?= htmlspecialchars($old['cityOfBirth'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="citizenshipCertificate">Citizenship Certificate Number</label>
                <input type="text" id="citizenshipCertificate" name="citizenshipCertificate" placeholder="Certificate number" inputmode="numeric" pattern="^\d+$" title="Enter numbers only" value="<?= htmlspecialchars($old['citizenshipCertificate'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Administrative Information -->
    <section class="nic-section" aria-labelledby="admin-info">
        <header class="nic-section__header">
            <h3 id="admin-info">Administrative Information</h3>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="district">District <span>*</span></label>
                <input type="text" id="district" name="district" placeholder="District" required value="<?= htmlspecialchars($old['district'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="divSecretariat">Divisional Secretariat Division <span>*</span></label>
                <input type="text" id="divSecretariat" name="divSecretariat" placeholder="Division" required value="<?= htmlspecialchars($old['divSecretariat'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="gramaNiladhari">Grama Niladari Number & Division <span>*</span></label>
                <input type="text" id="gramaNiladhari" name="gramaNiladhari" placeholder="GN number / division" required value="<?= htmlspecialchars($old['gramaNiladhari'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Permanent Address -->
    <section class="nic-section" aria-labelledby="perm-address">
        <header class="nic-section__header">
            <h3 id="perm-address">Permanent Address</h3>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="permHouse">House Name/Number <span>*</span></label>
                <input type="text" id="permHouse" name="permHouse" placeholder="House name/number" required value="<?= htmlspecialchars($old['permHouse'] ?? '') ?>">
            </div>
            <div class="nic-field nic-field--select">
                <label for="permBuildingType">Building Type <span>*</span></label>
                <select id="permBuildingType" name="permBuildingType" required>
                    <option value="">Select</option>
                    <option <?= (isset($old['permBuildingType']) && $old['permBuildingType'] === 'House') ? 'selected' : ''; ?>>House</option>
                    <option <?= (isset($old['permBuildingType']) && $old['permBuildingType'] === 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                    <option <?= (isset($old['permBuildingType']) && $old['permBuildingType'] === 'Boarding') ? 'selected' : ''; ?>>Boarding</option>
                    <option <?= (isset($old['permBuildingType']) && $old['permBuildingType'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="permStreet">Road/Street <span>*</span></label>
                <input type="text" id="permStreet" name="permStreet" placeholder="Road/Street" required value="<?= htmlspecialchars($old['permStreet'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="permCity">Village/City <span>*</span></label>
                <input type="text" id="permCity" name="permCity" placeholder="Village/City" required value="<?= htmlspecialchars($old['permCity'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="permPostal">Postal Code <span>*</span></label>
                <input type="text" inputmode="numeric" pattern="^\d{3,10}$" title="Enter 3 to 10 digits only" id="permPostal" name="permPostal" placeholder="Postal Code" required value="<?= htmlspecialchars($old['permPostal'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Postal Address -->
    <section class="nic-section" aria-labelledby="postal-address">
        <header class="nic-section__header">
            <h3 id="postal-address">Postal Address</h3>
            <label class="nic-inline-check">
                <input type="checkbox" id="sameAsPermanent" name="sameAsPermanent">
                <span>Same as permanent address</span>
            </label>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="postHouse">House Name/Number <span>*</span></label>
                <input type="text" id="postHouse" name="postHouse" placeholder="House name/number" required value="<?= htmlspecialchars($old['postHouse'] ?? '') ?>">
            </div>
            <div class="nic-field nic-field--select">
                <label for="postBuildingType">Building Type <span>*</span></label>
                <select id="postBuildingType" name="postBuildingType" required>
                    <option value="">Select</option>
                    <option <?= (isset($old['postBuildingType']) && $old['postBuildingType'] === 'House') ? 'selected' : ''; ?>>House</option>
                    <option <?= (isset($old['postBuildingType']) && $old['postBuildingType'] === 'Apartment') ? 'selected' : ''; ?>>Apartment</option>
                    <option <?= (isset($old['postBuildingType']) && $old['postBuildingType'] === 'Boarding') ? 'selected' : ''; ?>>Boarding</option>
                    <option <?= (isset($old['postBuildingType']) && $old['postBuildingType'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="postStreet">Road/Street <span>*</span></label>
                <input type="text" id="postStreet" name="postStreet" placeholder="Road/Street" required value="<?= htmlspecialchars($old['postStreet'] ?? '') ?>">
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="postCity">Village/City <span>*</span></label>
                <input type="text" id="postCity" name="postCity" placeholder="Village/City" required value="<?= htmlspecialchars($old['postCity'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="postPostal">Postal Code <span>*</span></label>
                <input type="text" inputmode="numeric" pattern="^\d{3,10}$" title="Enter 3 to 10 digits only" id="postPostal" name="postPostal" placeholder="Postal Code" required value="<?= htmlspecialchars($old['postPostal'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="nic-section" aria-labelledby="contact-info">
        <header class="nic-section__header">
            <h3 id="contact-info">Contact Information</h3>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="resPhone">Residence Phone <span>*</span></label>
                <input type="tel" id="resPhone" name="resPhone" placeholder="Residence phone" inputmode="numeric" pattern="^\d{10}$" title="Enter a 10-digit phone number" required value="<?= htmlspecialchars($old['resPhone'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="mobile_phone">Mobile Phone <span>*</span></label>
                <input type="tel" id="mobile_phone" name="mobile_phone" placeholder="Mobile phone" inputmode="numeric" pattern="^\d{10}$" title="Enter a 10-digit phone number" required value="<?= htmlspecialchars($old['mobile_phone'] ?? '') ?>">
            </div>
            <div class="nic-field">
                <label for="email">Email Address <span>*</span></label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
        </div>
    </section>

    <!-- Required Documents -->
    <section class="nic-section" aria-labelledby="required-docs">
        <header class="nic-section__header">
            <h3 id="required-docs">Required Documents</h3>
        </header>

        <div class="nic-file-row">
            <label class="nic-file" for="birthCertPdf">
                <span>Birth Certificate (PDF) <span>*</span></span>
                <span>Choose Birth Certificate PDF</span>
                <input type="file" id="birthCertPdf" name="birthCertPdf" accept="application/pdf" required>
            </label>

            <label class="nic-file" for="policeReportPdf">
                <span>Police Report (PDF) <span>*</span></span>
                <span>Choose Police Report PDF</span>
                <input type="file" id="policeReportPdf" name="policeReportPdf" accept="application/pdf" required>
            </label>
        </div>

        <div class="nic-grid nic-grid--one">
            <div class="nic-field">
                <label for="photoLink">Photo Link (Google Drive) <span>*</span></label>
                <input type="url" id="photoLink" name="photoLink" placeholder="https://drive.google.com/..." required value="<?= htmlspecialchars($old['photoLink'] ?? '') ?>">
                <small style="color: #666; font-size: 0.875rem; display: block; margin-top: 0.25rem;">Upload your photo to Google Drive and paste the shareable link here</small>
            </div>
        </div>

        <div class="nic-notice" role="note">
            <strong>Important Notice</strong>
            Before proceeding with the application, ensure that all the details you have entered are accurate and complete. Once the application is submitted, we do not offer refunds or cancellations under any circumstances. Please verify all information carefully before submission.
        </div>
    </section>

    <!-- Form Actions -->
    <div class="flow-control" style="margin-top: 1rem;">
        <button id="submitBtn" type="submit" class="btn">Submit Application</button>
    </div>
</form>

<script>
    (function() {
        const sameCB = document.getElementById('sameAsPermanent');
        if (!sameCB) return;

        const pairs = [
            ['permHouse', 'postHouse'],
            ['permBuildingType', 'postBuildingType'],
            ['permStreet', 'postStreet'],
            ['permCity', 'postCity'],
            ['permPostal', 'postPostal'],
        ];

        const getEl = (id) => document.getElementById(id);

        function copyValues() {
            pairs.forEach(([permId, postId]) => {
                const p = getEl(permId);
                const t = getEl(postId);
                if (p && t) {
                    t.value = p.value;
                }
            });
        }


        function bindSync(enabled) {
            pairs.forEach(([permId, postId]) => {
                const p = getEl(permId);
                if (!p) return;
                const handler = () => {
                    if (sameCB.checked) copyValues();
                };

                if (enabled) {
                    p.addEventListener('input', handler);
                    p.addEventListener('change', handler);
                } else {
                    p.removeEventListener('input', handler);
                    p.removeEventListener('change', handler);
                }
            });
        }

        sameCB.addEventListener('change', () => {
            if (sameCB.checked) {
                copyValues();
                bindSync(true);
            } else {
                bindSync(false);
            }
        });


        if (sameCB.checked) {
            copyValues();
            bindSync(true);
        }

        // Live validation for numeric fields
        function validateNumericInput(inputId, pattern, errorMessage) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('input', function(e) {
                const value = e.target.value;
                const regex = new RegExp(pattern);

                if (value && !regex.test(value)) {
                    input.setCustomValidity(errorMessage);
                    input.style.borderColor = 'red';
                } else {
                    input.setCustomValidity('');
                    input.style.borderColor = '';
                }
            });
        }

        // Validate phone numbers (10 digits only)
        validateNumericInput('resPhone', '^\\d{0,10}$', 'Residence phone must be exactly 10 digits');
        validateNumericInput('mobile_phone', '^\\d{0,10}$', 'Mobile phone must be exactly 10 digits');

        // Validate postal codes (3-10 digits)
        validateNumericInput('permPostal', '^\\d{0,10}$', 'Postal code must be 3-10 digits');
        validateNumericInput('postPostal', '^\\d{0,10}$', 'Postal code must be 3-10 digits');

        // Validate Citizenship Certificate (numbers only)
        validateNumericInput('citizenshipCertificate', '^\\d*$', 'Only numbers allowed');

        // Validate Birth Certificate Number (BC format)
        const bcInput = document.getElementById('birthCertNo');
        if (bcInput) {
            bcInput.addEventListener('input', function(e) {
                let value = e.target.value.toUpperCase();
                e.target.value = value;

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

            bcInput.addEventListener('keypress', function(e) {
                const char = e.key;
                const currentValue = bcInput.value.toUpperCase();

                if (currentValue.length === 0 && char.toUpperCase() !== 'B') {
                    e.preventDefault();
                } else if (currentValue.length === 1 && char.toUpperCase() !== 'C') {
                    e.preventDefault();
                } else if (currentValue.length >= 2 && !/\d/.test(char)) {
                    e.preventDefault();
                }
            });
        }

        // Validate Google Drive link
        const photoLink = document.getElementById('photoLink');
        if (photoLink) {
            photoLink.addEventListener('input', function(e) {
                const value = e.target.value;
                const isDriveLink = value.includes('drive.google.com') || value.includes('docs.google.com');

                if (value && !isDriveLink) {
                    photoLink.setCustomValidity('Please provide a valid Google Drive link');
                    photoLink.style.borderColor = 'red';
                } else {
                    photoLink.setCustomValidity('');
                    photoLink.style.borderColor = '';
                }
            });
        }

        // Prevent non-numeric input for numeric fields
        function allowOnlyNumbers(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('keypress', function(e) {
                if (e.key && !/^\d$/.test(e.key)) {
                    e.preventDefault();
                }
            });
        }

        allowOnlyNumbers('resPhone');
        allowOnlyNumbers('mobile_phone');
        allowOnlyNumbers('permPostal');
        allowOnlyNumbers('postPostal');
        allowOnlyNumbers('citizenshipCertificate');
    })();
</script>
