<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        background: #f3f4f6;
    }

    /* PASS CARD */
    .pass-card {
        max-width: 820px;
        margin: 30px auto;
        background: #fff;
        padding: 28px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        font-family: "Inter", "Segoe UI", sans-serif;
    }

    /* TITLE */
    .pass-header {
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 16px;
        margin-bottom: 22px;
    }

    .pass-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: #1e3a8a;
    }

    .pass-header .sub {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }

    /* DETAILS GRID */
    .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 30px;
    }

    .detail-row {
        display: flex;
        gap: 10px;
    }

    .detail-label {
        width: 140px;
        font-size: 13px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        word-break: break-word;
    }

    /* FULL WIDTH ROW */
    .detail-row.full {
        grid-column: 1 / -1;
    }

    /* FOOTER ACTION */
    .pass-footer {
        text-align: center;
        margin-top: 28px;
    }

    .btn-back {
        display: inline-block;
        padding: 10px 22px;
        background: #1e3a8a;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
    }

    .text-highlight {
        position: relative;
        font-weight: 700;
        z-index: 1;
    }

    .text-highlight::before {
        content: "";
        position: absolute;
        left: -4px;
        right: -4px;
        bottom: 2px;
        height: 1em;
        background: #fff59d;
        /* highlighter yellow */
        z-index: -1;
        transform: rotate(-1deg);
        border-radius: 2px;
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

    /* PRINT */
    @media print {
        body {
            background: #fff;
        }

        .btn-back {
            display: none;
        }

        .pass-card {
            box-shadow: none;
            border: none;
            padding: 0;
        }
    }
</style>
<?php
$today = date('Y-m-d');

$passDate = !empty($pass['pass_dt'])
    ? date('Y-m-d', strtotime($pass['pass_dt']))
    : null;

$isExpired = false;
if ($passDate && $passDate < $today) {
    $isExpired = true;
}
?>
<div class="pass-card">

    <?php if (!$pass): ?>

        <h3 style="text-align:center;color:#b91c1c;">Pass not found</h3>

    <?php else: ?>

        <!-- HEADER -->
        <div class="pass-header">
            <h2>Litigant Section Pass</h2>
            <div style="margin-top:10px;">
                <?php if ($isExpired): ?>
                    <span class="status-badge status-expired">EXPIRED</span>
                <?php else: ?>
                    <span class="status-badge status-valid">VALID</span>
                <?php endif; ?>
            </div>

            <div class="sub">Rajasthan High Court · Official Entry Pass</div>
        </div>

        <!-- DETAILS -->
        <div class="details">

            <div class="detail-row">
                <div class="detail-label">Pass No</div>
                <div class="detail-value"><?= htmlspecialchars($pass['pass_no']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Pass Date</div>
                <div class="detail-value"><?= date("d-m-Y", strtotime($pass['pass_dt'])) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Litigant Name</div>
                <div class="detail-value">
                    <span class="text-highlight">
                        <?= htmlspecialchars($pass['litigantname'] ?? '—') ?>
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Mobile</div>
                <div class="detail-value">
                    <span class="text-highlight">

                        <?= htmlspecialchars($pass['litigantmobile'] ?? '—') ?>

                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">R/O</div>
                <div class="detail-value"><?= htmlspecialchars($pass['litigant_address']) ?></div>
            </div>

            <?php if (!empty($pass['adv_name'])): ?>
                <div class="detail-row">
                    <div class="detail-label">Recommended By</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($pass['adv_name']) ?>
                        (<?= htmlspecialchars($pass['enroll_no']) ?>)

                    </div>
                </div>
            <?php endif; ?>

            <div class="detail-row">
                <div class="detail-label">Entry Time</div>
                <div class="detail-value"><?= date("d-m-Y H:i:s", strtotime($pass['entry_dt'])) ?></div>
            </div>

            <?php if (!empty($pass['purpose_items'])): ?>
                <div class="detail-row full">
                    <div class="detail-label">Purpose & Remarks</div>
                    <div class="detail-value">
                        <?php foreach ($pass['purpose_items'] as $item): ?>
                            <div style="margin-bottom:6px;">
                                <strong><?= htmlspecialchars($item['section_name']) ?>:</strong>
                                <?= !empty($item['remark'])
                                    ? htmlspecialchars($item['remark'])
                                    : '—'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>


    <?php endif; ?>

    <!-- FOOTER -->
    <div class="pass-footer">
        <a href="/HC-EPASS-MVC/public/index.php?r=pass/mySectionPasses" class="btn-back">
            ← Back to My Passes
        </a>
    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>