<?php 
use chillerlan\QRCode\QRCode;
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pass Print</title>

    <style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: #f3f4f6;
        margin: 0;
        padding: 25px;
    }

    .print-box {
        max-width: 600px;
        margin: auto;
        background: white;
        border-radius: 12px;
        border: 2px solid #1e3a8a;
        padding: 25px;
    }

    .title {
        text-align: center;
        margin-bottom: 20px;
    }

    .title h2 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
    }

    .subtitle {
        text-align:center;
        margin-bottom: 25px;
        color:#444;
        font-size:14px;
    }

    .row {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }

    .row b {
        width: 40%;
    }

    .qr-box {
        text-align: center;
        margin-top: 25px;
    }

    @media print {
        body { background: none; }
        .print-box { box-shadow: none; border: 2px solid #000; }
    }
    </style>

</head>
<body>

<div class="print-box">

    <div class="title">
        <h2>Rajasthan High Court</h2>
    </div>

    <div class="subtitle">Entry Pass / QR Verification Slip</div>

    <?php foreach ($pass as $key => $value): ?>
        <?php if ($key != 'id'): ?>
            <div class="row">
                <b><?= ucfirst(str_replace('_',' ', $key)) ?>:</b>
                <span><?= htmlspecialchars($value) ?></span>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="qr-box">
        <?php
            $data = "PASS-ID: ".$pass['id'];
            echo '<img src="data:image/png;base64,'.base64_encode((new QRCode)->render($data)).'" />';
        ?>
        <div style="margin-top:8px; font-size:12px;">Scan to verify</div>
    </div>

</div>

<script>
window.print();
</script>

</body>
</html>
