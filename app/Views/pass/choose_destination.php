<?php include __DIR__ . '/../layouts/header.php'; ?>

<?php
$type = $_GET['type'] ?? '';   // advocate OR litigant
?>

<style>
body {
    padding: 0px !important;
    margin: 0px !important;
    font-family: Arial, sans-serif;
}
.small-box {
    max-width: 650px;
    margin: 40px auto;
    padding: 25px;
    background: white;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}
.btn-choice {
    display: block;
    margin: 12px 0;
    padding: 14px;
    font-size: 18px;
    font-weight: 600;
    background: #2563eb;
    color: white;
    border-radius: 10px;
    text-decoration: none;
}
.btn-choice:hover {
    background: #1d4ed8;
}
.section-title {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #1e3a8a;
}
</style>

<div class="small-box">

    <?php if ($type === 'advocate'): ?>
        <div class="section-title">Select Advocate Pass Type</div>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/courtForm&type=advocate">
            For Court
        </a>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/generateForm&type=advocate&goto=section">
            For Section
        </a>

    <?php elseif ($type === 'litigant'): ?>
        <div class="section-title">Select Litigant Pass Type</div>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/courtForm&type=litigant">
            For Court
        </a>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/generateForm&type=litigant&goto=section">
            For Section
        </a>

        <?php elseif ($type === 'partyinperson'): ?>
        <div class="section-title">Select Party in person Pass Type</div>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/courtForm&type=partyinperson">
            For Court
        </a>

        <a class="btn-choice"
           href="/HC-EPASS-MVC/public/index.php?r=pass/generateForm&type=partyinperson&goto=section">
            For Section
        </a>

    <?php else: ?>
        <div class="section-title">Invalid Type</div>
        <p>Please add <b>?type=advocate</b> or <b>?type=litigant</b> in the URL.</p>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
