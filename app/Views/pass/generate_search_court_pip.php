<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php include __DIR__ . '/../layouts/partyreg.php'; ?>
<?php include __DIR__ . '/../layouts/OtpModel.php'; ?>

<style>
    body {
        padding: 0;
        margin: 0;
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
        background: #fff;
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
        background: #007bff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        transition: background-color .3s;
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

    .new-pass-box {
        padding: 30px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .pass-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e3a8a;
        text-align: center;
        margin-bottom: 25px;
    }

    .generate-btn-full {
        padding: 12px;
        width: 100%;
        background: #059669;
        color: #fff;
        border: none;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
    }

    .generate-btn-full:hover {
        background: #047857;
    }
</style>

<div class="page-container">

    <h2>Search Court Case (Party in person)</h2>

    <!-- SEARCH FORM -->
    <form id="courtSearchForm" class="form-container">

        <div class="grid-container">

            <div class="form-group">
                <label>Case Type</label>
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
                <label>Case No</label>
                <input type="text" id="case_no" name="case_no"
                    required maxlength="20" oninput="validateCaseNo(this)">
            </div>

            <div class="form-group">
                <label>Case Year</label>
                <select id="case_year" name="case_year" required></select>
            </div>

            <!-- <div class="form-group">
                <label>Causelist Type</label>
                <select name="cl_type" id="cl_type" required>
                    <option value="">-- Select Causelist Type --</option>
                    <option value="S">Supplementary</option>
                    <option value="D">Daily</option>
                    <option value="W">Weekly</option>
                </select>
            </div> -->

            <div class="form-group">
                <label>Causelist Date</label>
                <input type="date" id="cl_date" name="cl_date" required>
            </div>

        </div>

        <button type="submit">Search Case</button>
    </form>

    <div id="case-preview" style="display:none;margin-top:10px;font-weight:bold;color:#1e40af;"></div>
    <div id="case-title" style="display:none;margin-top:10px;font-size:16px;font-weight:600;color:#4f46e5;"></div>

    <div id="case-result"></div>
    <!-- ================= NOTICE MODAL ================= -->
    <div class="modal fade" id="noticeModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px;">

                <div class="modal-header" style="justify-content:center;">
                    <h4 class="modal-title" style="color:#d00000; font-weight:800; text-align:center;">
                        NOTICE
                    </h4>
                </div>


                <div class="modal-body" style="font-size:20px; color:#d00000; line-height:1.6; text-align:center;">
                    पार्टी-इन-पर्सन के पास के लिए केवल वे पक्षकार ही रजिस्ट्रेशन कर सकते हैं एवं ई-पास बना सकते हैं,
                    जिनका कोई अधिवक्ता नहीं है और उन्हें खुद ही अपने प्रकरण की पैरोवी करनी है।
                    ऐसे पक्षकार जिन्होंने अधिवक्ता के माध्यम से प्रकरण दायर किया है,
                    वे पार्टी-इन-पर्सन में रजिस्ट्रेशन नहीं करें एवं ई-पास नहीं बनायें।
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        style="padding:8px 25px; font-size:18px;">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
    /* ---------------------- YEAR DROPDOWN ---------------------- */
    function loadCaseYears() {
        const sel = document.getElementById("case_year");
        const y = new Date().getFullYear();
        for (let yr = y + 1; yr >= 1950; yr--) {
            let o = document.createElement("option");
            o.value = yr;
            o.textContent = yr;
            sel.appendChild(o);
        }
    }
    loadCaseYears();

    /* ---------------------- VALIDATIONS ---------------------- */
    function validateCaseNo(input) {
        input.value = input.value.replace(/[^0-9]/g, '').slice(0, 20);
    }

    /* limit date to today → +3 days */
    function setMaxDate() {
        let max = new Date();
        max.setDate(max.getDate() + 3);
        document.getElementById("cl_date").max = max.toISOString().split("T")[0];
    }
    setMaxDate();

    /* ---------------------- SEARCH SUBMIT ---------------------- */
    document.getElementById("courtSearchForm").addEventListener("submit", function(e) {
        e.preventDefault();

        let fd = new FormData(this);

        showLoader();
        fetch("/HC-EPASS-MVC/public/index.php?r=pass/searchCourtCase", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                hideLoader();
                showPipCase(data);
            });
    });

    /* ---------------------- SHOW CASE FOR LITIGANT ---------------------- */
    /* ---------------------- SHOW PIP CASE ---------------------- */
    function showPipCase(data) {

        let box = document.getElementById("case-result");

        if (data.status !== "OK") {
            box.style.display = "block";
            box.innerHTML = `<p style="color:red;font-weight:bold">${data.message}</p>`;
            return;
        }

        box.style.display = "block";
        box.innerHTML = `
        <div class="result-title">Case Found</div>

        <p><b>Court Room:</b> ${data.court_no}</p>
        <p><b>Item No:</b> ${data.item_no}</p>
        <p><b>Cause List Type:</b> ${data.case_type_text}</p>

        <!-- Party-in-Person Declaration -->
        <div style="margin:15px 0; padding:12px; background:#fff; border-radius:8px;">
            <label style="font-size:16px; cursor:pointer;">
                <input type="checkbox" id="pip_declaration" style="margin-right:6px;">
                <b>I declare that I am appearing as Party-in-Person in this case
                and I am not represented by any Advocate.</b>
            </label>
        </div>

        <button class="generate-btn"
            onclick='validatePIP("${encodeURIComponent(JSON.stringify(data))}")'>
            Continue
        </button>
    `;
    }

    /* ---------------------- CHECK DECLARATION ---------------------- */
    function validatePIP(encoded) {
        let cb = document.getElementById("pip_declaration");

        if (!cb.checked) {
            alert("Please confirm that you are appearing as Party-in-Person.");
            return;
        }

        openPIPForm(encoded);
    }

    /* ---------------------- OPEN PIP FORM ---------------------- */
    function openPIPForm(encoded) {

        let data = JSON.parse(decodeURIComponent(encoded));
        let advOptions = data.advocates.map(a => `
                <option 
                value="${a.name}"
                data-side="${a.side}"
                data-mobile="${a.mobile || ''}"
                data-advcode="${a.adv_code || ''}"
            >
                ${a.name}
            </option>
        `).join("");
        document.getElementById("case-result").innerHTML = `
        <div class="new-pass-box">
            <h3 class="pass-title">Generate Party-in-Person Pass</h3>

            <div class="form-group">
             <label>Select Party</label>
                <select id="pip_name">${advOptions}</select>
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" id="pip_mobile" maxlength="10">
            </div>

            <div class="form-group">
                <label>Full Address</label>
                <input type="text" id="pip_address" placeholder="Enter Full Address">
            </div>
             <div class="form-group">
               
                 <input type="hidden" id="cl_type" value="${data.case_type}" >
            </div>

            <button class="generate-btn-full"
                onclick="submitPIP('${encodeURIComponent(JSON.stringify(data))}')">
                Generate Pass
            </button>
        </div>
    `;
    }

    function isValidMobile(mob) {
        return /^[6-9][0-9]{9}$/.test(mob);
    }

    function submitPIPAfterOtp(fd, name) {

        showLoader();

        fetch("/HC-EPASS-MVC/public/index.php?r=pass/generateCourtPIP", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(resp => {

                hideLoader();

                if (resp.status === "ERROR" && resp.code == 404) {
                    showPartyRegisterForm(resp.message, name);
                    return;
                }

                if (resp.status === "ERROR") {
                    showError(resp.message || "Unable to generate PIP pass");
                    return;
                }

                showSuccess(
                    "PIP Pass Generated Successfully!<br>PASS NO: <b>" + resp.pass_no + "</b>"
                );

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            })
            .catch(() => {
                hideLoader();
                showError("Something went wrong! Please try again.");
            });
    }

    function submitPIP(encoded) {

        let data = JSON.parse(decodeURIComponent(encoded));

        let name = document.getElementById("pip_name").value.trim();
        let mobile = document.getElementById("pip_mobile").value.trim();
        let address = document.getElementById("pip_address").value.trim();

        // VALIDATION
        if (name === "") return showError("Name cannot be empty.");
        if (!isValidMobile(mobile))
            return showError("Enter a valid Indian mobile number.");
        if (address === "") return showError("Address cannot be empty.");

        // Prepare encrypted FormData
        let fd = new FormData();

        fd.append("cino", safeEncode(data.cino));
        fd.append("cldt", safeEncode(data.cl_date));
        fd.append("courtno", safeEncode(data.court_no));
        fd.append("itemno", safeEncode(data.item_no));

        fd.append("partynm", safeEncode(name));
        fd.append("partymob", safeEncode(mobile));
        fd.append("paddress", safeEncode(address));

        fd.append("partyno", safeEncode("0"));
        fd.append("passfor", safeEncode("P")); 
        fd.append("cltype", safeEncode(data.case_type));

        // 🔥 OTP FLOW STARTS HERE 🔥
        initOtpFlow({
            mobile: mobile, // OTP goes to PIP mobile
            purpose: "PIP_PASS",
            role: "PIP",
            payload: {
                fd,
                name
            }, // name needed for error handling
            onSuccess: function(payload) {
                submitPIPAfterOtp(payload.fd, payload.name);
            }
        });
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var noticeModal = new bootstrap.Modal(document.getElementById('noticeModal'));
        noticeModal.show();
    });
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>