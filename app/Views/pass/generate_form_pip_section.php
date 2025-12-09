<?php include __DIR__ . '/../layouts/header.php'; ?>

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

    .form-card h2 {
        font-size: 30px;
        font-weight: 800;
        margin-bottom: 25px;
        color: #1e293b;
    }

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

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
        color: #111827;
    }

    .input-field {
        width: 100%;
        padding: 13px 14px;
        border-radius: 10px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        font-size: 15px;
    }

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
    /* Match Select2 box with input-field styling */
.select2-container--default .select2-selection--multiple {
    min-height: 48px !important;
    border-radius: 10px !important;
    border: 1px solid #d1d5db !important;
    background: #f9fafb !important;
    padding: 6px 8px !important;
    font-size: 15px;
}

</style>

<div class="form-wrapper">
    <div class="form-card">
        <h2>Party In Person Section Pass</h2>
        <div id="form-message"></div>

        <form id="sectionPassForm">

            <!-- PARTY IN PERSON DETAILS -->
            <div class="grid">
                <div>
                    <label>Party-in-Person Name</label>
                    <input type="text" class="input-field" name="pip_name" required>
                </div>

                <div>
                    <label>Mobile Number</label>
                    <input type="text" class="input-field" name="pip_mobile" maxlength="10" required>
                </div>
            </div>

            <div style="margin-top:20px;">
                <label>Full Address</label>
                <input type="text" class="input-field" name="pip_address" required>
            </div>

            <hr style="margin:25px 0;">

            <!-- VISIT DATE + SECTIONS -->
            <div class="grid">
                <div>
                    <label>Date of Visit</label>
                    <input type="date" class="input-field" name="visit_date" required>
                </div>

                <div>
                    <label style="font-weight:700;font-size:16px;">Select Sections</label>
                   <select id="sections" name="sections[]" class="input-field" multiple style="width:100%;">

                        <?php foreach ($purposeList as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['purpose']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="purpose-wrapper"></div>

            <button type="submit" class="submit-btn">Generate Section Pass</button>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {

        // init select2
        $('#sections').select2({
            placeholder: "Select Sections",
            allowClear: true,
            closeOnSelect: false
        });

        // dynamic purpose fields
        $('#sections').on('change', function() {
            let ids = $(this).val();
            let wrapper = $("#purpose-wrapper");
            wrapper.html("");

            if (!ids) return;

            ids.forEach(id => {
                let label = $("#sections option[value='" + id + "']").text();
                wrapper.append(`
                <div class="purpose-box">
                    <label>Purpose for <b>${label}</b></label>
                    <input name="purpose[${id}]" placeholder="Enter purpose">
                </div>
            `);
            });
        });

        // submit form
        $("#sectionPassForm").on("submit", function(e) {
            e.preventDefault();

            $("#form-message").html("");

            let name = $("input[name='pip_name']").val().trim();
            let mobile = $("input[name='pip_mobile']").val().trim();
            let address = $("input[name='pip_address']").val().trim();
            let visit = $("input[name='visit_date']").val().trim();
            let sections = $("#sections").val();

            // REQUIRED VALIDATION
            if (name === "" || mobile === "" || address === "" || visit === "") {
                $("#form-message").html(`<div class="msg-error">Please fill all required fields.</div>`);
                return;
            }

            // ✅ OLD MOBILE VALIDATION (RESTORED)
            if (!/^[0-9]{10}$/.test(mobile)) {
                $("#form-message").html(`<div class="msg-error">Invalid mobile number.</div>`);
                return;
            }
            if (!/^[6-9]/.test(mobile)) {
                $("#form-message").html(`<div class="msg-error">Mobile must start with 6–9.</div>`);
                return;
            }
            if (/^(\d)\1+$/.test(mobile)) {
                $("#form-message").html(`<div class="msg-error">Invalid mobile number pattern.</div>`);
                return;
            }

            // SECTION VALIDATION (UNCHANGED — AS BEFORE)
            if (!sections || sections.length === 0) {
                $("#form-message").html(`<div class='msg-error'>Please select at least one section.</div>`);
                return;
            }

            for (let id of sections) {
                let purpose = $(`input[name='purpose[${id}]']`).val()?.trim();
                if (!purpose || purpose === "") {
                    $("#form-message").html(`<div class='msg-error'>Purpose missing for selected section.</div>`);
                    return;
                }
            }

            let formData = $("#sectionPassForm").serialize();
            showLoader();

            $.ajax({
                url: "/HC-EPASS-MVC/public/index.php?r=pass/savePartyInPsersonSection",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(res) {
                    hideLoader();

                    if (res.status === "ERROR") {
                        $("#form-message").html(`<div class='msg-error'>${res.message}</div>`);
                        return;
                    }

                    $("#form-message").html(`<div class='msg-success'>Pass Generated Successfully! Redirecting...</div>`);

                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 1200);
                }
            });

        });


    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>