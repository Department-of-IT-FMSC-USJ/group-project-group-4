<form id="identity-form" method="post" action="#" class="nic-application nic-form-frame" novalidate>
    <!-- Personal Information -->
    <section class="nic-section" aria-labelledby="personal-info">
        <header class="nic-section__header">
            <h3 id="personal-info">Personal Information</h3>
            <p class="nic-section__hint">Please fill out all required information accurately.</p>
        </header>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="familyName">Family Name <span>*</span></label>
                <input type="text" id="familyName" name="familyName" placeholder="Family Name" required>
            </div>
            <div class="nic-field">
                <label for="givenName">Name <span>*</span></label>
                <input type="text" id="givenName" name="givenName" placeholder="Name" required>
            </div>
            <div class="nic-field">
                <label for="surname">Surname <span>*</span></label>
                <input type="text" id="surname" name="surname" placeholder="Surname" required>
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="idFamilyName">ID Card Family Name</label>
                <input type="text" id="idFamilyName" name="idFamilyName" placeholder="ID Card Family Name">
            </div>
            <div class="nic-field">
                <label for="idGivenName">ID Card Name</label>
                <input type="text" id="idGivenName" name="idGivenName" placeholder="ID Card Name">
            </div>
            <div class="nic-field">
                <label for="idSurname">ID Card Surname</label>
                <input type="text" id="idSurname" name="idSurname" placeholder="ID Card Surname">
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field nic-field--select">
                <label for="sex">Sex <span>*</span></label>
                <select name="sex" id="sex" required>
                    <option value="">Select Sex</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="nic-field nic-field--select">
                <label for="civilStatus">Civil Status <span>*</span></label>
                <select name="civilStatus" id="civilStatus" required>
                    <option value="">Select Civil Status</option>
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widowed</option>
                    <option>Divorced</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="profession">Profession <span>*</span></label>
                <input type="text" id="profession" name="profession" placeholder="Profession" required>
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="dob">Date of Birth <span>*</span></label>
                <input type="date" id="dob" name="dob" placeholder="yyyy-mm-dd" required>
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
                <input type="text" id="birthCertNo" name="birthCertNo" placeholder="Birth certificate number" required>
            </div>
            <div class="nic-field">
                <label for="placeOfBirth">Place of Birth <span>*</span></label>
                <input type="text" id="placeOfBirth" name="placeOfBirth" placeholder="Place of Birth" required>
            </div>
        </div>

        <div class="nic-grid nic-grid--three">
            <div class="nic-field">
                <label for="birthDivision">Birth Division <span>*</span></label>
                <input type="text" id="birthDivision" name="birthDivision" placeholder="Birth Division" required>
            </div>
            <div class="nic-field">
                <label for="birthDistrict">Birth District <span>*</span></label>
                <input type="text" id="birthDistrict" name="birthDistrict" placeholder="Birth District" required>
            </div>
            <div class="nic-field">
                <label for="countryOfBirth">Country of Birth</label>
                <input type="text" id="countryOfBirth" name="countryOfBirth" placeholder="Sri Lanka" value="Sri Lanka">
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="cityOfBirth">City of Birth</label>
                <input type="text" id="cityOfBirth" name="cityOfBirth" placeholder="City of Birth">
            </div>
            <div class="nic-field">
                <label for="citizenshipCertificate">Citizenship Certificate Number</label>
                <input type="text" id="citizenshipCertificate" name="citizenshipCertificate" placeholder="Certificate number">
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
                <input type="text" id="district" name="district" placeholder="District" required>
            </div>
            <div class="nic-field">
                <label for="divSecretariat">Divisional Secretariat Division <span>*</span></label>
                <input type="text" id="divSecretariat" name="divSecretariat" placeholder="Division" required>
            </div>
            <div class="nic-field">
                <label for="gramaNiladhari">Grama Niladari Number & Division <span>*</span></label>
                <input type="text" id="gramaNiladhari" name="gramaNiladhari" placeholder="GN number / division" required>
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
                <input type="text" id="permHouse" name="permHouse" placeholder="House name/number" required>
            </div>
            <div class="nic-field nic-field--select">
                <label for="permBuildingType">Building Type <span>*</span></label>
                <select id="permBuildingType" name="permBuildingType" required>
                    <option value="">Select</option>
                    <option>House</option>
                    <option>Apartment</option>
                    <option>Boarding</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="permStreet">Road/Street <span>*</span></label>
                <input type="text" id="permStreet" name="permStreet" placeholder="Road/Street" required>
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="permCity">Village/City <span>*</span></label>
                <input type="text" id="permCity" name="permCity" placeholder="Village/City" required>
            </div>
            <div class="nic-field">
                <label for="permPostal">Postal Code <span>*</span></label>
                <input type="text" inputmode="numeric" id="permPostal" name="permPostal" placeholder="Postal Code" required>
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
                <input type="text" id="postHouse" name="postHouse" placeholder="House name/number" required>
            </div>
            <div class="nic-field nic-field--select">
                <label for="postBuildingType">Building Type <span>*</span></label>
                <select id="postBuildingType" name="postBuildingType" required>
                    <option value="">Select</option>
                    <option>House</option>
                    <option>Apartment</option>
                    <option>Boarding</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="nic-field">
                <label for="postStreet">Road/Street <span>*</span></label>
                <input type="text" id="postStreet" name="postStreet" placeholder="Road/Street" required>
            </div>
        </div>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field">
                <label for="postCity">Village/City <span>*</span></label>
                <input type="text" id="postCity" name="postCity" placeholder="Village/City" required>
            </div>
            <div class="nic-field">
                <label for="postPostal">Postal Code <span>*</span></label>
                <input type="text" inputmode="numeric" id="postPostal" name="postPostal" placeholder="Postal Code" required>
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
                <label for="resPhone">Residence Phone</label>
                <input type="tel" id="resPhone" name="resPhone" placeholder="Residence phone">
            </div>
            <div class="nic-field">
                <label for="mobile_phone">Mobile Phone</label>
                <input type="tel" id="mobile_phone" name="mobile_phone" placeholder="Mobile phone">
            </div>
            <div class="nic-field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com">
            </div>
        </div>
    </section>

    <!-- Application Details -->
    <section class="nic-section" aria-labelledby="application-details">
        <header class="nic-section__header">
            <h3 id="application-details">Application Details</h3>
        </header>

        <div class="nic-grid nic-grid--two">
            <div class="nic-field nic-field--select">
                <label for="applicationPurpose">Application Purpose <span>*</span></label>
                <select id="applicationPurpose" name="applicationPurpose" required>
                    <option value="">Select Purpose</option>
                    <option>Lost NIC Replacement</option>
                    <option>Change of Address</option>
                    <option>Correction</option>
                </select>
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

            <label class="nic-file" for="photoPdf">
                <span>Photo (PDF) <span>*</span></span>
                <span>Choose Photo PDF</span>
                <input type="file" id="photoPdf" name="photoPdf" accept="application/pdf" required>
            </label>
        </div>

        <div class="nic-grid nic-grid--one">
            <div class="nic-field">
                <label for="photoLink">Photo Link <span>*</span></label>
                <input type="url" id="photoLink" name="photoLink" placeholder="https://example.com/photo.jpg" required>
            </div>
        </div>

        <div class="nic-notice" role="note">
            <strong>Important Notice</strong>
            Before proceeding with the application, ensure that all the details you have entered are accurate and complete. Once the application is submitted, we do not offer refunds or cancellations under any circumstances. Please verify all information carefully before submission.
        </div>
    </section>
</form>