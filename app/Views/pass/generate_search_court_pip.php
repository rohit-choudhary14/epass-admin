<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* ⭐ your styles remain untouched ⭐ */
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

    button:hover {
        background: #0056b3;
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

            <div class="form-group">
                <label>Causelist Type</label>
                <select name="cl_type" id="cl_type" required>
                    <option value="">-- Select Causelist Type --</option>
                    <option value="S">Supplementary</option>
                    <option value="D">Daily</option>
                    <option value="W">Weekly</option>
                </select>
            </div>

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
                showLitigantCase(data);
            });
    });

    /* ---------------------- SHOW CASE FOR LITIGANT ---------------------- */
function showLitigantCase(data) {

    let box = document.getElementById("case-result");

    if (data.status !== "OK") {
        box.style.display = "block";
        box.innerHTML = `<p style="color:red;font-weight:bold">${data.message}</p>`;
        return;
    }

    // Build advocate dropdown
    let advOptions = data.advocates.map(a => `
        <option value="${a.name}||${a.adv_code}||${a.side}">
            ${a.name} (${a.side_label})
        </option>
    `).join("");

    // Determine litigant side automatically
    let sides = [...new Set(data.advocates.map(a => a.side))];
    let partySelectHTML = "";

    if (sides.length === 1) {
        // Auto side
        let side = sides[0];
        let label = (side == 1 ? "Petitioner" : "Respondent");

        partySelectHTML = `
            <div class="form-group">
                <label><b>Litigant Side</b></label>
                <input type="text" id="party_side_label" value="${label}" disabled>
                <input type="hidden" id="party_side" value="${side}">
            </div>
        `;
    } else {
        // If both side advocates exist
        partySelectHTML = `
            <label>Select Party (Litigant)</label>
            <select id="party_side">
                <option value="1">Petitioner</option>
                <option value="2">Respondent</option>
            </select>
        `;
    }

    box.style.display = "block";
    box.innerHTML = `
        <div class="result-title">Case Found</div>

        <p><b>Court Room:</b> ${data.court_no}</p>
        <p><b>Item No:</b> ${data.item_no}</p>

        <label><b>Pass Recommended By (Advocate)</b></label>
        <select id="recommended_adv">
            ${advOptions}
        </select>

        ${partySelectHTML}

        <button class="generate-btn"
            onclick='openLitigantForm("${encodeURIComponent(JSON.stringify(data))}")'>
            Continue
        </button>
    `;
}




    /* ---------------------- SHOW LITIGANT FINAL FORM ---------------------- */
  function openLitigantForm(encoded) {

    let data = JSON.parse(decodeURIComponent(encoded));

    // Determine litigant side
    let side;
    if (document.getElementById("party_side")) {
        side = document.getElementById("party_side").value;
    } else {
        // read from advocate
        let recommended = document.getElementById("recommended_adv").value.split("||");
        side = recommended[2];  
    }

    let sideLabel = (side == 1 ? "Petitioner" : "Respondent");

    // Advocate info
    let recommended = document.getElementById("recommended_adv").value.split("||");
    let recommendedName = recommended[0];
    let recommendedCode = recommended[1];

    document.getElementById("case-result").innerHTML = `
        <div class="new-pass-box">
            <h3 class="pass-title">Generate Litigant Pass</h3>

            <p><b>Pass Recommended By:</b> ${recommendedName}</p>
            <p><b>Litigant Side:</b> ${sideLabel}</p>

            <div class="form-group">
                <label>Litigant Name</label>
                <input type="text" id="lit_name" placeholder="Enter Name">
            </div>

            <div class="form-group">
                <label>Mobile Number</label>
                <input type="text" id="lit_mobile" maxlength="10">
            </div>

            <div class="form-group">
                <label>Full Address</label>
                <input type="text" id="lit_address">
            </div>

            <!-- Hidden fields -->
            <input type="hidden" id="partyno" value="0">
            <input type="hidden" id="partynm" value="">
            <input type="hidden" id="partymob" value="">
            <input type="hidden" id="paddress" value="">
            <input type="hidden" id="passfor" value="L">
            <input type="hidden" id="adv_code" value="${recommendedCode}">
            <input type="hidden" id="party_side" value="${side}">

            <button class="generate-btn-full"
                onclick="submitLitigant(
                    '${encodeURIComponent(JSON.stringify(data))}', 
                    '${side}', 
                    '${recommendedName}', 
                    '${recommendedCode}'
                )">
                Generate Pass
            </button>
        </div>
    `;
}


    function isValidMobile(mob) {
        // Must be exactly 10 digits
        if (!/^[0-9]{10}$/.test(mob)) return false;

        // Cannot start with 0-5 (Indian mobile standard)
        if (!/^[6-9]/.test(mob)) return false;

        // Cannot be 0000000000 or repeated pattern
        if (/^(\d)\1+$/.test(mob)) return false;

        return true;
    }

    /* ---------------------- SUBMIT LITIGANT PASS ---------------------- */
    function submitLitigant(encoded, side, recAdvName, recAdvCode) {

        let data = JSON.parse(decodeURIComponent(encoded));

        let name = document.getElementById("lit_name").value.trim();
        let mobile = document.getElementById("lit_mobile").value.trim();
        let address = document.getElementById("lit_address").value.trim();
        let party_side = document.getElementById("party_side").value.trim();

        // VALIDATIONS
        if (name === "") {
            alert("Litigant name cannot be empty.");
            return;
        }

        if (!isValidMobile(mobile)) {
            alert("Enter a valid 10-digit mobile number (should start from 6-9 and cannot be all zeros).");
            return;
        }

        if (address === "") {
            alert("Litigant address cannot be empty.");
            return;
        }

        let fd = new FormData();
        fd.append("cino", data.cino);
        fd.append("lit_name", name);
        fd.append("lit_mobile", mobile);
        fd.append("lit_address", address);
        fd.append("passfor", 'L');
        // Recommended advocate
        fd.append("recommended_by", recAdvName);
        fd.append("recommended_code", recAdvCode);
        fd.append("adv_type", party_side);
        fd.append("courtno", data.court_no);
        fd.append("itemno", data.item_no);
        fd.append("cldt", data.cl_date);
        fd.append("cltype", data.cl_type);

        // showLoader();
        fetch("/HC-EPASS-MVC/public/index.php?r=pass/generateCourtLitigant", {
                method: "POST",
                body: fd
            })
            .then(r => r.json())
            .then(resp => {
                hideLoader();
                alert("Litigant Pass Generated! PASS NO: " + resp.pass_no);
            });
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>