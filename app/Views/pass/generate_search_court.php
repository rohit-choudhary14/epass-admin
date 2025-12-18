<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        padding: 0px !important;
        margin: 0px !important;
        font-family: Arial, sans-serif;
    }

    .page-container {
        max-width: 900px;
        margin: 30px auto;
        font-family: "Inter", sans-serif;
    }



    .form-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        margin-bottom: 8px;
        color: #555;
    }

    input,
    select {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }

    input:focus,
    select:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    button {
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }

    button:focus {
        outline: none;
    }

    /* Media query for responsiveness */
    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: 1fr;
            /* Single column layout on small screens */
        }

        .form-group {
            margin-bottom: 15px;
        }

        button {
            width: auto;
            margin-top: 20px;
        }
    }

    /* Small screen padding adjustments */
    @media (max-width: 480px) {
        .form-container {
            padding: 15px;
        }

        input,
        select,
        button {
            font-size: 14px;
        }
    }

    #case-result {
        margin-top: 25px;
        padding: 20px;
        display: none;
        border-radius: 12px;
        background: #eef2ff;
        border-left: 5px solid #4f46e5;
    }

    .result-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .generate-btn {
        margin-top: 10px;
        padding: 12px;
        width: auto;
        background: #059669;
        color: white;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
    }

    .generate-btn:hover {
        background: #047857;
    }

    .new-pass-box {
        padding: 30px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        animation: fadeIn 0.3s ease;
    }

    .pass-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e3a8a;
        text-align: center;
        margin-bottom: 25px;
    }
</style>

<div class="page-container">

    <div>
        <h2>Search Court Case</h2>

        <!-- SEARCH FORM -->
        <form id="courtSearchForm" class="form-container">

            <div class="grid-container">

                <div class="form-group">
                    <label for="case_type">Case Type</label>
                    <select name="case_type" id="case_type" required>
                        <option value="">-- Select Case Type --</option>
                        <?php foreach ($caseTypes as $ct): ?>
                            <option value="<?= $ct['case_type'] ?>">
                                <?= htmlspecialchars($ct['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="case_no">Case No</label>
                    <!-- <input type="number" id="case_no" name="case_no" required> -->
                    <input type="text" id="case_no" name="case_no"
                        required maxlength="20"
                        oninput="validateCaseNo(this)">

                </div>

                <div class="form-group">
                    <label for="case_year">Case Year</label>
                    <!-- <input type="number" id="case_year" name="case_year" required> -->
                    <select id="case_year" name="case_year" required></select>

                </div>

                <div class="form-group">
                    <label for="cl_type">Causelist Type</label>
                    <select name="cl_type" id="cl_type" required>
                        <option value="">-- Select Causelist Type --</option>
                        <option value="S">Supplementary</option>
                        <option value="D">Daily</option>
                        <option value="W">Weekly</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cl_date">Causelist Date</label>
                    <input type="date" id="cl_date" name="cl_date" required>
                </div>

                <!-- ⭐ NEW FIELD: Advocate Name -->
                <!-- <div class="form-group">
            <label for="adv_name">Advocate Name (as in Causelist)</label>
            <input type="text" name="adv_name" id="adv_name" placeholder="Enter advocate name" required>
        </div> -->

                <!-- ⭐ NEW FIELD: Party Side -->
                <!-- <div class="form-group">
            <label for="party_side">Party Side</label>
            <select name="party_side" id="party_side" required>
                <option value="">-- Select --</option>
                <option value="1">Petitioner Side</option>
                <option value="2">Respondent Side</option>
            </select>
        </div> -->

            </div>

            <button type="submit" class="submit-button">Search Case</button>
        </form>
        <div id="case-preview" style="display:none; margin-top:10px; font-weight:bold; color:#1e40af;"></div>

        <div id="case-title" style="display:none; margin-top:10px; font-size:16px; font-weight:600; color:#4f46e5;"></div>

    </div>

    <!-- AJAX RESULTS -->
    <div id="case-result"></div>

</div>

<script>
    // -------------------------------
    // CASE YEAR DROPDOWN
    // -------------------------------
    function loadCaseYears() {
        const yearSelect = document.getElementById("case_year");
        const currentYear = new Date().getFullYear();

        for (let y = currentYear + 1; y >= 1950; y--) {
            let opt = document.createElement("option");
            opt.value = y;
            opt.textContent = y;
            yearSelect.appendChild(opt);
        }
    }
    loadCaseYears();

    // -------------------------------
    // CASE NUMBER VALIDATION
    // -------------------------------
    function validateCaseNo(input) {
        input.value = input.value.replace(/[^0-9]/g, '').slice(0, 20);
    }

    // -------------------------------
    // DISABLE WEEKENDS IN DATE PICKER
    // -------------------------------
    document.getElementById("cl_date").addEventListener("change", function() {
        let d = new Date(this.value);
        let day = d.getDay();

        if (day === 0 || day === 6) {
            alert("Weekends are not allowed for Causelist Date.");
            this.value = "";
            return;
        }
    });

    // -------------------------------
    // LIMIT DATE TO TODAY → + 3 DAYS
    // -------------------------------
    function setCauselistMaxDate() {
        let maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 3);

        document.getElementById("cl_date").max =
            maxDate.toISOString().split("T")[0];
    }
    setCauselistMaxDate();

    // -------------------------------
    // AUTO CASE PREVIEW (CW / 1234 / 2025)
    // -------------------------------
    function updateCasePreview() {
        let ct = document.getElementById("case_type").value;
        let no = document.getElementById("case_no").value;
        let yr = document.getElementById("case_year").value;

        if (ct && no && yr) {
            document.getElementById("case-preview").style.display = "block";
            document.getElementById("case-preview").innerHTML =
                `Case: <b>${ct} / ${no} / ${yr}</b>`;
        }
    }
    document.getElementById("case_type").addEventListener("change", updateCasePreview);
    document.getElementById("case_no").addEventListener("input", updateCasePreview);
    document.getElementById("case_year").addEventListener("change", updateCasePreview);

    // -------------------------------
    // SEARCH CASE
    // -------------------------------
    document.getElementById("courtSearchForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const clDate = document.getElementById("cl_date").value;
        const selected = new Date(clDate);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        let maxDate = new Date();
        maxDate.setDate(today.getDate() + 3);
        maxDate.setHours(0, 0, 0, 0);

        // if (selected < today) {
        //     alert("Causelist date cannot be older than today.");
        //     return;
        // }
        if (selected > maxDate) {
            alert("Causelist date cannot be more than 3 days ahead.");
            return;
        }

        const formData = new FormData(this);
        showLoader();
        fetch("/HC-EPASS-MVC/public/index.php?r=pass/searchCourtCase", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                hideLoader();
                let box = document.getElementById("case-result");

                if (data.status === "OK") {

                    // Show Case Title (Petitioner vs Respondent)
                    if (data.pet_name && data.res_name) {
                        document.getElementById("case-title").style.display = "block";
                        document.getElementById("case-title").innerHTML =
                            `${data.pet_name} <span style="color:red;">Vs</span> ${data.res_name}`;
                    }

                    let advOptions = data.advocates.map(a => `
                <option value="${a.name}||${a.side}||${a.mobile}||${a.adv_code}">
                    ${a.name} (${a.side_label})
                </option>
            `).join("");

                    box.style.display = "block";
                    box.innerHTML = `
                <div class="result-title">Case Found</div>
                <p><b>Court Room:</b> ${data.court_no}</p>
                <p><b>Item No:</b> ${data.item_no}</p>

                <label>Select Advocate</label>
                <select id="adv_select">${advOptions}</select>

                <button class='generate-btn'
                    onclick='openPassForm("${encodeURIComponent(JSON.stringify(data))}")'>
                    Continue
                </button>
            `;
                } else {
                    hideLoader();
                    box.style.display = "block";
                    box.innerHTML = `<p style='color:#b91c1c;font-weight:600'>${data.message}</p>`;
                }
            });
    });

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function registerAdvocate(enrollNo) {

        const passType = document.querySelector(
            "#advRegisterForm input[name='passtype']:checked"
        )?.value;

        const mobile = document.getElementById("adv_mobile").value.trim();
        const email = document.getElementById("adv_email").value.trim();
        const address = document.getElementById("adv_address").value.trim();

        if (!passType || !mobile || !address) {
            showError("Please fill all required fields");
            return;
        }

        let fd = new FormData();
        fd.append("enroll_no", safeEncode(enrollNo));
        fd.append("passtype", passType);
        fd.append("mobile", safeEncode(mobile));
        fd.append("email", safeEncode(email));
        fd.append("address", safeEncode(address));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=auth/registerAdvocateAjax", {
                method: "POST",
                body: fd
            })
            .then(res => res.json())
            .then(resp => {
                hideLoader();
                if (resp.status !== "OK") {
                    showError(resp.message);
                    return;
                }
                showSuccess(resp.message);
                setTimeout(() => location.reload(), 1200);
            })
            .catch(() => {
                hideLoader();
                showError("Server error");
            });
    }

    function showAdvocateDetails(data) {

        const enrollInput = document.getElementById("enroll_no");
        enrollInput.readOnly = true;
        enrollInput.style.background = "#f3f4f6";

        const box = document.getElementById("adv-details-box");
        box.style.display = "block";

        // 🔹 check existing values
        const hasMobile = !!data.mobile;
        const hasEmail = !!data.email;
        const hasAddress = !!data.address;

        box.innerHTML = `
        <div class="form-group">
            <label>Advocate Name</label>
            <input type="text" value="${data.adv_name}" readonly>
        </div>

        <div class="form-group">
            <label>Mobile *</label>
            <input type="text"
                   id="adv_mobile"
                   value="${data.mobile || ''}"
                   ${hasMobile ? 'readonly style="background:#f3f4f6"' : ''}>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text"
                   id="adv_email"
                   value="${data.email || ''}"
                   ${hasEmail ? 'readonly style="background:#f3f4f6"' : ''}>
        </div>

        <div class="form-group">
            <label>Address *</label>
            <textarea id="adv_address"
                ${hasAddress ? 'readonly style="background:#f3f4f6"' : ''}>${data.address || ''}</textarea>
        </div>

        <div class="form-group">
            <label><b>Pass Type</b></label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="1"> Sr. Advocate
            </label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="2"> Advocate (For Self and Litigants)
            </label>
            <label style="margin-left:15px">
                <input type="radio" name="passtype" value="3"> Party in Person
            </label>
        </div>
    `;

        const btn = document.querySelector("#advRegisterForm button[type]");
        btn.type = "button";
        btn.innerText = "Confirm & Register Advocate";

        btn.onclick = function(e) {
            e.preventDefault();

            const passTypeEl = document.querySelector(
                "#advRegisterForm input[name='passtype']:checked"
            );
            if (!passTypeEl) {
                showError("Please select Pass Type.");
                return;
            }

            // 🔒 Validate only missing fields
            if (!hasMobile && !document.getElementById("adv_mobile").value.trim()) {
                showError("Mobile number is required");
                return;
            }
            if (!hasAddress && !document.getElementById("adv_address").value.trim()) {
                showError("Address is required");
                return;
            }

            if (!hasEmail) {
                const emailVal = document.getElementById("adv_email").value.trim();
                if (emailVal && !isValidEmail(emailVal)) {
                    showError("Please enter a valid email address");
                    return;
                }
            }
            const ok = confirm(
                `Are you sure you want to register this advocate?\n\n` +
                `Name: ${data.adv_name}\n` +
                `Enrollment No: ${data.adv_reg || data.enroll_no}\n` +
                `Pass Type: ${passTypeEl.nextSibling.textContent.trim()}`
            );

            if (!ok) return;

            registerAdvocate(data.adv_reg || data.enroll_no);
        };
    }





    function checkEnrollAndFetchDetails() {

        const form = document.getElementById("advRegisterForm");
        const enrollNo = form.enroll_no.value.trim();

        if (enrollNo === "") {
            showError("Enrollment number is required.");
            return;
        }

        let fd = new FormData();
        fd.append("enroll_no", safeEncode(enrollNo));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=auth/findAdvDetails", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(resp => {
                hideLoader();

                if (resp.status === "NOT_FOUND") {
                    showError(resp.message || "Advocate not found.");
                    return;
                }

                if (resp.status === "FOUND") {
                    showAdvocateDetails(resp.data);
                }
            })
            .catch(() => {
                hideLoader();
                showError("Server error.");
            });
    }



    function showAdvocateRegisterForm(message) {
        const box = document.getElementById("case-result");

        box.style.display = "block";
        box.innerHTML = `
        <div class="card form-container new-pass-box" style="margin-top:25px;border-left:5px solid #dc2626">
            <h3 class="pass-title" style="color:#dc2626">Advocate Registration Required</h3>

            <p style="font-weight:600;color:#991b1b">
                ${message || 'Advocate is not registered in the Gate Pass system.'}
            </p>

           <form id="advRegisterForm" onsubmit="checkEnrollAndFetchDetails(); return false;">

    <div class="form-group">
        <label>Enrollment Number</label>
        <input type="text" name="enroll_no" id="enroll_no" required>
    </div>

    <div id="adv-details-box" style="display:none"></div>

    <button type="submit" class="generate-btn">
        Search Advocate
    </button>

    <button type="button" class="generate-btn" style="background:#6b7280;margin-left:10px"
        onclick="location.reload()">
        Cancel
    </button>

</form>


        </div>
    `;
    }


    function submitPassData(encodedData, advName, advSide) {

        let data = JSON.parse(decodeURIComponent(encodedData));
        let fd = new FormData();

        // Encrypt everything before sending
        fd.append("cino", safeEncode(data.cino));
        fd.append("adv_type", safeEncode(advSide));
        fd.append("adv_name", safeEncode(advName));
        fd.append("courtno", safeEncode(data.court_no));
        fd.append("itemno", safeEncode(data.item_no));
        fd.append("cldt", safeEncode(data.cl_date));
        fd.append("cltype", safeEncode(data.cl_type));
        fd.append("paddress", safeEncode(document.getElementById("paddress").value));
        fd.append("partyno", safeEncode(document.getElementById("partyno").value));
        fd.append("partynm", safeEncode(document.getElementById("partynm").value));
        fd.append("partymob", safeEncode(document.getElementById("partymob").value));
        fd.append("passfor", safeEncode(document.getElementById("passfor").value));
        fd.append("adv_code", safeEncode(document.getElementById("adv_code").value));

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=pass/generateCourt", {
                method: "POST",
                body: fd
            })
            .then(res => res.json())
            .then(response => {
                hideLoader();
                if (response.status === "ERROR" && response.code === 404) {
                    showAdvocateRegisterForm(response.message);
                    return;
                }
                if (response.status === "ERROR") {
                    showError(response.message || "Something went wrong.");
                    return;
                }
                showSuccess("Pass Generated Successfully! <br> PASS NO: <b>" + response.pass_no + "</b>");

                setTimeout(() => {
                    window.location.href = "/HC-EPASS-MVC/public/index.php?r=pass/myPasses";
                }, 1500);
            })
            .catch(err => {
                hideLoader();
                showError("Unable to connect to server.");
            });

    }



    function openPassForm(encodedData) {

        let data = JSON.parse(decodeURIComponent(encodedData));
        let selected = document.getElementById("adv_select").value;
        // let [advName, advSide, advMobile, adv_code] = selected.split("||");
        let [advName, advSide, advMobile, adv_code] = selected.split("||");

        // sanitize all fields
        advName = (advName && advName !== 'null' && advName !== 'undefined') ? advName : '';
        advSide = (advSide && advSide !== 'null' && advSide !== 'undefined') ? advSide : '';
        advMobile = (advMobile && advMobile !== 'null' && advMobile !== 'undefined') ? advMobile : '';
        adv_code = (adv_code && adv_code !== 'null' && adv_code !== 'undefined') ? adv_code : '';

        if (document.getElementById("adv_code")) {
            document.getElementById("adv_code").value = adv_code;
        }


        const form = `
        <div class="card form-container new-pass-box" style="margin-top:25px">
            <h3 class="pass-title">Generate Court Pass</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label>Advocate Name</label>
                    <input type="text" id="advname" value="${advName}" readonly>
                </div>

                <div class="form-group">
                    <label>Advocate Mobile</label>
                    <input type="text" id="adv_mobile" value="${advMobile ? advMobile : ''}" readonly>

                </div>

                <div class="form-group">
                    <label>Advocate Side</label>
                    <input type="text" id="adv_side_label" value="${advSide == 1 ? 'Petitioner' : 'Respondent'}" readonly>
                </div>

          
                <input type="hidden" id="partyno" value="0">
                <input type="hidden" id="partynm" value="">
                <input type="hidden" id="partymob" value="">
                <input type="hidden" id="paddress" value="">
                <input type="hidden" id="passfor" value="C">
                <input type="hidden" id="adv_code" value="${adv_code}">
            </div>

            <button class="generate-btn-full"
                onclick="submitPassData('${encodeURIComponent(JSON.stringify(data))}', '${advName}', '${advSide}')">
                Generate Pass
            </button>
        </div>
    `;

        document.getElementById("case-result").innerHTML = form;
    }
</script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>