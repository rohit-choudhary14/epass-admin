<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        background: #f3f4f6;
        font-family: "Inter", "Segoe UI", sans-serif;
    }

    .pass-box {
        max-width: 820px;
        margin: 30px auto;
        background: #fff;
        padding: 24px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    .pass-title {
        text-align: center;
        font-size: 22px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 18px;
    }

    .pass-sub {
        text-align: center;
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 24px;
    }

    .detail-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        width: 35%;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
    }

    .detail-value {
        width: 65%;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .text-highlight {
        background: #fff59d;
        padding: 2px 6px;
        border-radius: 3px;
    }

    .back-btn {
        text-align: center;
        margin-top: 24px;
    }

    /* STATUS BADGE */
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .status-valid {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .status-expired {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>
<?php
$today = date('Y-m-d');

$passDate = !empty($p['causelist_dt'])
    ? date('Y-m-d', strtotime($p['causelist_dt']))
    : null;

$isExpired = false;
if ($passDate && $passDate < $today) {
    $isExpired = true;
}
?>
<div class="pass-box">

    <div class="pass-title">Party-in-Person Court Pass</div>
    <div style="margin-top:10px; display:flex; justify-content:center">
        <?php if ($isExpired): ?>
            <span class="status-badge status-expired">EXPIRED</span>
        <?php else: ?>
            <span class="status-badge status-valid">VALID</span>
        <?php endif; ?>
    </div>

    <div class="pass-sub" style="margin-top:10px;">Rajasthan High Court · Official Entry Pass</div>

    <div class="detail-row">
        <div class="detail-label">Pass No</div>
        <div class="detail-value"><?= htmlspecialchars($p['pass_no'] ?? '—') ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Party Name</div>
        <div class="detail-value">
            <span class="text-highlight">
                <?= htmlspecialchars($p['party_name'] ?? $p['name'] ?? '—') ?>
            </span>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">CINO</div>
        <div class="detail-value"><?= htmlspecialchars($p['cino'] ?? '—') ?></div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Court / Item</div>
        <div class="detail-value">
            Court <?= htmlspecialchars($p['court_no'] ?? '—') ?> /
            Item <?= htmlspecialchars($p['item_no'] ?? '—') ?>
        </div>
    </div>

    <div class="detail-row">
        <div class="detail-label">Hearing Date</div>
        <div class="detail-value">
            <?php
            if (!empty($p['causelist_dt'])) {
                $dt = DateTime::createFromFormat('Y-m-d', $p['causelist_dt']);
                echo $dt ? $dt->format('d-m-Y') : '—';
            } else {
                echo '—';
            }
            ?>
        </div>
    </div>

    <div class="back-btn">
        <a href="/HC-EPASS-MVC/public/index.php?r=pass/myPasses"
            class="btn btn-outline-primary btn-sm">
            ← Back
        </a>
    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>