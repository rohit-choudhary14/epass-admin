<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/advreg.php'; ?>

<style>
    /* WRAPPER */
    body {
        padding: 0px !important;
        margin: 0px !important;
    }

    .form-wrapper {
        max-width: 900px;
        margin: 32px auto;
        padding: 10px;
        font-family: "Inter", sans-serif;
    }

    /* CARD */
    .form-card {
        background: #ffffff;
        padding: 32px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    /* TITLE */
    .form-card h2 {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 25px;
        color: #1e293b;
    }

    /* STEP INDICATOR */
    .step-indicator {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }

    .step-indicator div {
        padding: 10px 16px;
        background: #eef2ff;
        border-radius: 8px;
        font-weight: 600;
        color: #1e40af;
    }

    /* FIXED GRID */
    .grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media(max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }

    /* LABEL */
    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
        color: #111827;
    }

    /* INPUTS — FIXED HEIGHT & SPACING */
    .input-field {
        width: 100%;
        padding: 13px 14px;
        border-radius: 10px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        font-size: 15px;
    }

    /* Remove icon space issues */
    .input-field::-webkit-calendar-picker-indicator {
        padding: 0 !important;
        margin: 0 !important;
    }

    /* SELECT2 height fix */
    .select2-container--default .select2-selection--multiple {
        min-height: 48px !important;
        border-radius: 10px !important;
        border: 1px solid #d1d5db !important;
        background: #f9fafb !important;
        padding: 6px !important;
    }

    /* PURPOSE BOX */
    .purpose-box {
        background: #eef2ff;
        border: 1px solid #c7d2fe;
        padding: 16px;
        margin-top: 18px;
        border-radius: 10px;
    }

    .purpose-box input {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
    }

    /* BUTTON */
    .submit-btn {
        width: 100%;
        padding: 16px;
        background: #2563eb;
        color: white;
        border: none;
        font-size: 18px;
        margin-top: 25px;
        border-radius: 10px;
        font-weight: 700;
    }

    /* SUCCESS / ERROR */
    .msg-error,
    .msg-success {
        padding: 13px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-weight: 600;
    }

    .msg-error {
        background: #fee2e2;
        color: #b91c1c;
    }

    .msg-success {
        background: #dcfce7;
        color: #166534;
    }

    /* FIX DATE INPUT WIDTH + RADIUS */
    .input-field {
        width: 100%;
        padding: 13px 14px;
        border-radius: 10px !important;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        font-size: 15px;
        box-sizing: border-box;
    }

    /* Remove weird padding from date’s native icon */
    input[type="date"] {
        appearance: none;
        -webkit-appearance: none;
    }

    /* Calendar icon size fix */
    input[type="date"]::-webkit-calendar-picker-indicator {
        width: 22px;
        height: 22px;
        cursor: pointer;
        margin-right: 6px;
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
    #enroll_no{
        width: 100%;
    }
</style>
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


<div class="form-wrapper">

    <div class="form-card">

        <div class="step-indicator">
            <div>Step 1 — Advocate Details</div>
            <div>Step 2 — Select Sections</div>
            <div>Step 3 — Add Purpose</div>
        </div>

        <h2>Advocate Section Pass</h2>

        <div id="form-message"></div>

        <form id="sectionPassForm">

            <!-- FIXED GRID -->
            <div class="grid">

                <div>
                    <label>Enrollment Number</label>
                    <input class="input-field" id="enroll" name="enroll" required>
                </div>

                <div>
                    <label>Date of Visit</label>
                    <input type="date" class="input-field" name="visit_date" required>
                </div>

            </div>

            <div style="margin-top:25px; grid-column:1 / -1;">
                <label style="font-weight:700;font-size:16px;">Select Sections</label>
                <select id="sections" name="sections[]" multiple style="width:100%;">
                    <?php foreach ($purposeList as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['purpose']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div id="purpose-wrapper"></div>

            <button type="submit" class="submit-btn">Generate Section Pass</button>

        </form>

    </div>
    <div id="case-result"></div>

</div>
  <!-- AJAX RESULTS -->
    
<script>
    $(document).ready(function() {
        $('#sections').select2({
            placeholder: "Select Sections",
            allowClear: true,
            closeOnSelect: false
        });

        // CREATE purpose inputs dynamically
        $('#sections').on('change', function() {
            let selectedIds = $(this).val();
            let wrapper = $("#purpose-wrapper");
            wrapper.html("");

            if (!selectedIds) return;

            selectedIds.forEach(id => {
                let label = $("#sections option[value='" + id + "']").text();
                wrapper.append(`
                <div class="purpose-box">
                    <label>Purpose for <b>${label}</b></label>
                    <input name="purpose[${id}]" placeholder="Enter purpose">
                </div>
            `);
            });
        });



    });
</script>


<div id="form-message"></div>

<script>
    $(document).ready(function() {
    
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
        $("#sectionPassForm").on("submit", function(e) {
            e.preventDefault();

            $("#form-message").html("");

            let enroll = $("input[name='enroll']").val().trim();
            let sections = $("#sections").val();

            if (enroll === "") {
                showError("Please fill all required fields.");
                return;
            }

            if (!sections || sections.length === 0) {
                showError("Please select at least one section.");
                return;
            }

            for (let id of sections) {
                let purpose = $(`input[name='purpose[${id}]']`).val()?.trim();
                if (!purpose || purpose === "") {
                    showError("Please enter purpose for selected section.");
                    return;
                }
            }

            // ---------- 🔐 ENCRYPT ALL VALUES BEFORE SUBMITTING ----------
            let encryptedData = {};

            encryptedData["enroll"] = safeEncode(enroll);
            encryptedData["visit_date"] = safeEncode($("input[name='visit_date']").val());

            // encrypt sections array as JSON
            encryptedData["sections"] = safeEncode(JSON.stringify(sections));

            // encrypt purpose remarks
            let purposes = {};
            sections.forEach(id => {
                purposes[id] = $(`input[name='purpose[${id}]']`).val().trim();
            });

            encryptedData["purpose"] = safeEncode(JSON.stringify(purposes));

            showLoader();

            $.ajax({
                url: "/HC-EPASS-MVC/public/index.php?r=pass/saveAdvocateSection",
                type: "POST",
                data: encryptedData,
                dataType: "json",

                success: function(res) {
                    hideLoader();


                    if (res.status === "ERROR" && res.code === 404) {
                        showAdvocateRegisterForm(res.message);
                        return;
                    }
                    if (res.status === "ERROR") {
                        showError(res.message || "Something went wrong.");
                        return;
                    }

                    showSuccess("Pass Generated Successfully! Redirecting...");

                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 1500);
                },

                error: function(xhr) {
                    hideLoader();
                    showError("Server Error: " + (xhr.responseText || "Unable to connect."));
                }
            });
        });


    });
</script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>