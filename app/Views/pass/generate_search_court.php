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

    .card {
        background: #fff;
        padding: 28px;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .card h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 22px;
        color: #1f2937;
        text-align: center;
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

    <div class="card">
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
                    <input type="number" id="case_no" name="case_no" required>
                </div>

                <div class="form-group">
                    <label for="case_year">Case Year</label>
                    <input type="number" id="case_year" name="case_year" required>
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

    </div>

    <!-- AJAX RESULTS -->
    <div id="case-result"></div>

</div>

<script>
    // AJAX SEARCH CALL
    document.getElementById("courtSearchForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("/HC-EPASS-MVC/public/index.php?r=pass/searchCourtCase", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                let box = document.getElementById("case-result");

                if (data.status === "OK") {
                    box.style.display = "block";
                    let advOptions = data.advocates
                        .map(a => `<option value="${a.name}||${a.side}||${a.mobile} || ${a.adv_code}">
             ${a.name} (${a.side_label})
           </option>`)

                        .join("");


                    box.innerHTML = `
    <div class="result-title">Case Found</div>
    <p><b>Court Room:</b> ${data.court_no}</p>
    <p><b>Item No:</b> ${data.item_no}</p>

    <label>Select Advocate</label>
    <select id="adv_select">${advOptions}</select>

    <button class='generate-btn'
        onclick='openPassForm("${encodeURIComponent(JSON.stringify(data))}")'
>
        Continue
    </button>
`;;
                } else {
                    box.style.display = "block";
                    box.innerHTML = `
                <p style='color:#b91c1c;font-weight:600'>${data.message}</p>
            `;
                }
            });
    });

    function submitPassData(encodedData, advName, advSide) {

        console.log("🔍 submitPassData() called");
        console.log("Raw Encoded Data:", encodedData);
        console.log("Adv Name:", advName);
        console.log("Adv Side (RAW):", advSide);

        let data = JSON.parse(decodeURIComponent(encodedData));

        // DEBUG: Log parsed case data
        console.log("Parsed Case Data:", data);

        // DEBUG: If advSide is too long → ERROR FOUND
        if (advSide.length > 4) {
            console.error("❌ ERROR: advSide is too long for DB:", advSide);
        }

        let fd = new FormData();

        fd.append("cino", data.cino);
        fd.append("adv_type", advSide);
        fd.append("adv_name", advName);
        fd.append("courtno", data.court_no);
        fd.append("itemno", data.item_no);
        fd.append("cldt", data.cl_date);
        fd.append("cltype", data.cl_type);
        fd.append("paddress", document.getElementById("paddress").value);
        fd.append("partyno", document.getElementById("partyno").value);
        fd.append("partynm", document.getElementById("partynm").value);
        fd.append("partymob", document.getElementById("partymob").value);
        fd.append("passfor", document.getElementById("passfor").value);
        fd.append("adv_code", document.getElementById("adv_code").value);

        // Print the entire form data
        console.log("📤 FormData being sent:");
        for (let pair of fd.entries()) {
            console.log(pair[0] + ":", pair[1]);
        }

        fetch("/HC-EPASS-MVC/public/index.php?r=pass/generateCourt", {
                method: "POST",
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                console.log("🎯 Server Response:", data);
                alert("Pass Generated Successfully! PASS NO: " + data.pass_no);
            })
            .catch(err => {
                console.error("❌ Fetch Error:", err);
                alert("Error generating pass");
            });
    }


    function openPassForm(encodedData) {

        let data = JSON.parse(decodeURIComponent(encodedData));

        let selected = document.getElementById("adv_select").value;
        let [advName, advSide, advMobile,adv_code] = selected.split("||");


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
            <input type="text" id="adv_mobile" value="${advMobile}" readonly>
        </div>

        <div class="form-group">
            <label>Advocate Side</label>
            <input type="text" id="adv_side_label" value="${advSide == 1 ? 'Petitioner' : 'Respondent'}" readonly>
        </div>

        <!-- HIDDEN FIELDS (but still included for submitPassData) -->
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




        document.getElementById("case-result").innerHTML += form;
    }






    function formatDMY(dateStr) {
        const d = new Date(dateStr);
        let day = String(d.getDate()).padStart(2, '0');
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>