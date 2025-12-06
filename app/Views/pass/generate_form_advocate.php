<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .form-box {
        max-width: 900px;
        margin: 30px auto;
        padding: 30px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        font-family: "Inter", sans-serif;
    }

    .form-box h2 {
        font-size: 28px;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    input, select {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
    }

    button {
        margin-top: 20px;
        width: 100%;
        padding: 14px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    button:hover {
        background: #1d4ed8;
    }

    .section-only, .court-only {
        display: none;
    }
</style>

<?php
// FIXED — TRIM VALUES
$type = isset($type) ? trim($type) : trim($_GET['type'] ?? 'advocate');
$goto = isset($goto) ? trim($goto) : trim($_GET['goto'] ?? 'court');
?>

<div class="form-box">

    <h2>
        <?= ($type == 'sr_advocate') ? "Senior Advocate Pass" : "Advocate Pass"; ?>
        — <?= ucfirst($goto) ?>
    </h2>

    <form method="post" action="/HC-EPASS-MVC/public/index.php?r=pass/saveAdvocate">

        <div class="grid">

            <div>
                <label>Advocate Name</label>
                <input name="name" required>
            </div>

            <div>
                <label>Enroll No</label>
                <input name="enroll" required>
            </div>

            <div>
                <label>Mobile</label>
                <input name="mobile" required>
            </div>

            <!-- CASE DETAILS -->
            <div>
                <label>CNR / CINO</label>
                <input name="cnr">
            </div>

            <div>
                <label>Case Type</label>
                <input name="case_type">
            </div>

            <div>
                <label>Case No</label>
                <input name="case_no">
            </div>

            <div>
                <label>Case Year</label>
                <input type="number" name="year" min="1900" max="2100">
            </div>

            <!-- COURT FIELDS -->
            <div class="court-only">
                <label>Court No</label>
                <input name="court_no">
            </div>

            <div class="court-only">
                <label>Item No</label>
                <input name="item_no">
            </div>

            <!-- SECTION FIELDS -->
            <div class="section-only">
                <label>Section Name</label>
                <select name="section">
                    <option value="">-- Select --</option>
                    <option>Filing Section</option>
                    <option>Copying Section</option>
                    <option>CR Section</option>
                    <option>Judicial Section</option>
                </select>
            </div>

            <div class="section-only">
                <label>Purpose</label>
                <input name="purpose">
            </div>

        </div>

        <!-- hidden -->
        <input type="hidden" name="goto" value="<?= $goto ?>">
        <input type="hidden" name="type" value="<?= $type ?>">

        <button type="submit">
            Generate <?= ($type == 'sr_advocate') ? "Senior Advocate" : "Advocate"; ?> Pass
        </button>

    </form>
</div>

<script>
    // FIXED — TRIM JS VALUE
    let gotoVal = "<?= trim($goto) ?>".toLowerCase();

    if (gotoVal === "court") {
        document.querySelectorAll('.court-only').forEach(x => x.style.display = 'block');
    }
    if (gotoVal === "section") {
        document.querySelectorAll('.section-only').forEach(x => x.style.display = 'block');
    }
    if (gotoVal === "both") {
        document.querySelectorAll('.court-only').forEach(x => x.style.display = 'block');
        document.querySelectorAll('.section-only').forEach(x => x.style.display = 'block');
    }
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
