<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/advreg.php'; ?>
<?php include __DIR__ . '/../layouts/OtpModel.php'; ?>

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
            <div>Step 4 — Litigant Details</div>
        </div>

        <h2>Litigant Section Pass</h2>

        <div id="form-message"></div>

        <form id="sectionPassForm">

            <!-- STEP 1: ADVOCATE DETAILS -->
            <div class="grid">

                <div>
                    <label>Enrollment Number (Recommended by)</label>
                    <input class="input-field" id="enroll" name="enroll" required>
                </div>

                <div>
                    <label>Date of Visit</label>
                    <input type="date" class="input-field" name="visit_date" required>
                </div>

            </div>
            <div style="margin-top:25px;">

                <div class="grid">

                    <div>
                        <label>Litigant Name</label>
                        <input class="input-field" id="lit_name" name="lit_name" required placeholder="Enter litigant name">
                    </div>

                    <div>
                        <label>Mobile Number</label>
                        <input class="input-field" id="lit_mobile" name="lit_mobile" maxlength="10"
                            required placeholder="10-digit mobile">
                    </div>

                </div>

                <label>Full Address</label>
                <input class="input-field" id="lit_address" name="lit_address" required placeholder="Enter full address">
            </div>


            <!-- STEP 2: SECTION SELECTION -->
            <div style="margin-top:25px; grid-column:1 / -1;">
                <label style="font-weight:700;font-size:16px;">Select Sections</label>
                <select id="sections" name="sections[]" multiple style="width:100%;">
                    <?php foreach ($purposeList as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['purpose']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- STEP 3: PURPOSE -->
            <div id="purpose-wrapper"></div>

            <button type="submit" class="submit-btn">Generate Section Pass</button>

        </form>

        <div id="case-result"></div>
    </div>

</div>

<script>
    $(document).ready(function() {
        $('#sections').select2({
            placeholder: "Select Sections",
            allowClear: true,
            closeOnSelect: false
        });
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
    function isValidMobile(m) {
        if (!/^[0-9]{10}$/.test(m)) return false; // must be 10 digits
        if (!/^[6-9]/.test(m)) return false; // must start with 6–9
        if (/^(\d)\1+$/.test(m)) return false; // cannot be all same digits
        return true;
    }

    function submitLitigantSectionAfterOtp(fd) {

        showLoader();

        $.ajax({
            url: "/HC-EPASS-MVC/public/index.php?r=pass/saveLitigantSection",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
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
                showError(
                    "Server Error: " + (xhr.responseText || "Unable to connect")
                );
            }
        });
    }

    $(document).ready(function() {

        $("#sectionPassForm").on("submit", function(e) {
            e.preventDefault();

            $("#form-message").html("");

            let enroll = $("#enroll").val().trim();
            let visit = $("input[name='visit_date']").val().trim();
            let litName = $("#lit_name").val().trim();
            let litMobile = $("#lit_mobile").val().trim();
            let litAddress = $("#lit_address").val().trim();
            let sections = $("#sections").val();

            // VALIDATIONS
            if (enroll === "" || visit === "" || litName === "" || litAddress === "") {
                showError("Please fill all required fields.");
                return;
            }

            if (!isValidMobile(litMobile)) {
                showError("Invalid mobile number.");
                return;
            }

            if (!sections || sections.length === 0) {
                showError("Please select at least one section.");
                return;
            }

            for (let id of sections) {
                let purpose = $(`input[name='purpose[${id}]']`).val()?.trim();
                if (!purpose) {
                    showError("Purpose missing for selected section.");
                    return;
                }
            }

            // 🔐 BUILD ENCRYPTED FORM DATA
            let fd = new FormData();

            fd.append("enroll", safeEncode(enroll));
            fd.append("visit_date", safeEncode(visit));
            fd.append("lit_name", safeEncode(litName));
            fd.append("lit_mobile", safeEncode(litMobile));
            fd.append("lit_address", safeEncode(litAddress));

            // SECTIONS
            fd.append("sections", safeEncode(JSON.stringify(sections)));

            // PURPOSES
            let purposes = {};
            sections.forEach(id => {
                purposes[id] = $(`input[name='purpose[${id}]']`).val().trim();
            });
            fd.append("purpose", safeEncode(JSON.stringify(purposes)));

            // 🔥 OTP FLOW STARTS HERE 🔥
            initOtpFlow({
                mobile: litMobile, // OTP goes to litigant mobile
                purpose: "LITIGANT_SECTION_PASS",
                role: "LITIGANT",
                payload: fd,
                onSuccess: function(payload) {
                    submitLitigantSectionAfterOtp(payload);
                }
            });
        });

    });
</script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>