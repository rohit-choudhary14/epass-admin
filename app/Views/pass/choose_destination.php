<?php include __DIR__ . '/../layouts/header.php'; ?>

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
    display:block;
    margin:12px 0;
    padding:14px;
    font-size:18px;
    font-weight:600;
    background:#2563eb;
    color:white;
    border-radius:10px;
    text-decoration:none;
}
.btn-choice:hover {
    background:#1d4ed8;
}
</style>

<div class="small-box">

    <h2>Select destination for Advocate Pass</h2>

    <a class="btn-choice"
       href="/HC-EPASS-MVC/public/index.php?r=pass/courtForm&type=advocate">
        Advocate Going to Court
    </a>

    <a class="btn-choice"
       href="/HC-EPASS-MVC/public/index.php?r=pass/generateForm&type=advocate&goto=section">
        Advocate Going to Section
    </a>

    <!-- <a class="btn-choice"
       href="/HC-EPASS-MVC/public/index.php?r=pass/generateForm&type=advocate&goto=both">
        Advocate Going to Both (Court + Section)
    </a> -->

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
