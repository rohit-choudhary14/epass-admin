<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* WRAPPER */
    body{
        padding: 0px !important ;
        margin: 0px !important ;
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

</div>

<script>
    $(document).ready(function() {

        // INIT SELECT2
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

        $("#sectionPassForm").on("submit", function(e) {
            e.preventDefault();

            
            let enroll = $("input[name='enroll']").val().trim();
            let sections = $("#sections").val();

            $("#form-message").html(""); // reset

            // ==========================
            // FRONTEND VALIDATION
            // ==========================

            if (enroll === "") {
                $("#form-message").html(`
                <div class='msg-error'>Please fill all required fields.</div>
            `);
                return;
            }

            if (!sections || sections.length === 0) {
                $("#form-message").html(`
                <div class='msg-error'>Please select at least one section.</div>
            `);
                return;
            }

            // CHECK purpose for each selected section
            for (let id of sections) {
                let purpose = $(`input[name='purpose[${id}]']`).val()?.trim();

                if (!purpose || purpose === "") {
                    $("#form-message").html(`
                    <div class='msg-error'>
                        Please enter purpose for selected section.
                    </div>
                `);
                    return;
                }
            }

            // ==========================
            // ALL GOOD → SEND AJAX
            // ==========================

            let formData = $("#sectionPassForm").serialize();
             showLoader();
            $.ajax({
                url: "/HC-EPASS-MVC/public/index.php?r=pass/saveAdvocateSection",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(res) {
                        hideLoader();
                    if (res.status === "ERROR") {
                        $("#form-message").html(`
                        <div class='msg-error'>${res.message}</div>
                    `);
                        return;
                    }

                    if (res.status === "OK") {
                        $("#form-message").html(`
                        <div class='msg-success'>Pass Generated Successfully! Redirecting...</div>
                    `);

                        setTimeout(() => {
                            window.location.href = res.redirect;
                        }, 1200);
                    }
                },

                error: function(xhr) {
                    hideLoader();
                    $("#form-message").html(`
                    <div class='msg-error'>
                        Server Error: ${xhr.responseText}
                    </div>
                `);
                }
            });

        });

    });
</script>


<?php include __DIR__ . '/../layouts/footer.php'; ?>