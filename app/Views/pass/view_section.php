<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .pass-box {
        max-width: 800px;
        margin: 25px auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        font-family: "Inter", sans-serif;
    }

    .pass-title {
        font-size: 26px;
        font-weight: 700;
        text-align: center;
        color: #1e40af;
        margin-bottom: 20px;
    }

    table.pass-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    table.pass-table th,
    table.pass-table td {
        border: 1px solid #d1d5db;
        padding: 10px;
        font-size: 16px;
    }

    table.pass-table th {
        background: #f3f4f6;
        font-weight: 600;
    }

    .btn-back {
        display: inline-block;
        padding: 10px 20px;
        background: #2563eb;
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
    }
</style>

<div class="pass-box">
    
    <h2 class="pass-title">Advocate Section Pass</h2>

    <?php if (!$pass): ?>
        <h3 style="color:red;text-align:center;">Pass not found!</h3>
    <?php else: ?>

        <table class="pass-table">
            <tr>
                <th>Pass No</th>
                <td><?= htmlspecialchars($pass['pass_no']) ?></td>
            </tr>

            <tr>
                <th>Pass Date</th>
                <td><?= htmlspecialchars(date("d-m-Y", strtotime($pass['pass_dt']))) ?></td>
            </tr>

            <tr>
                <th>Advocate Name</th>
                <td><?= htmlspecialchars($pass['adv_name']) ?></td>
            </tr>

            <tr>
                <th>Enrollment No</th>
                <td><?= htmlspecialchars($pass['adv_enroll']) ?></td>
            </tr>

            <tr>
                <th>Mobile</th>
                <td><?= htmlspecialchars($pass['mobile']) ?></td>
            </tr>

            <tr>
                <th>Purpose of Visit</th>
                <td><?= htmlspecialchars($pass['purpose_of_visit']) ?></td>
            </tr>

            <?php if (!empty($pass['purposermks'])): ?>
            <tr>
                <th>Remarks</th>
                <td><?= nl2br(htmlspecialchars($pass['purposermks'])) ?></td>
            </tr>
            <?php endif; ?>

            <tr>
                <th>Entry Time</th>
                <td><?= date("d-m-Y H:i:s", strtotime($pass['entry_dt'])); ?></td>
            </tr>

        </table>

    <?php endif; ?>

    <div style="text-align:center;">
        <a href="/HC-EPASS-MVC/public/index.php?r=pass/myPasses" class="btn-back">← Back to My Passes</a>
    </div>

</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
