<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    .result-wrapper {
        max-width: 1100px;
        margin: 30px auto;
        padding: 10px;
        font-family: "Inter", sans-serif;
    }

    .result-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .result-card h2 {
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 20px;
        color: #1e293b;
    }

    .search-info {
        font-size: 14px;
        margin-bottom: 15px;
        color: #475569;
    }

    .msg-error {
        padding: 14px;
        border-radius: 10px;
        background: #fee2e2;
        color: #b91c1c;
        font-weight: 600;
        margin-bottom: 20px;
    }

    /* TABLE */
    .result-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .result-table th,
    .result-table td {
        padding: 14px 12px;
        border: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
    }

    .result-table th {
        background: #f1f5f9;
        font-weight: 700;
        color: #0f172a;
    }

    .result-table tr:nth-child(even) {
        background: #fafafa;
    }

    .result-table tr:hover {
        background: #eef2ff;
    }

    /* EMPTY STATE */
    .no-result {
        text-align: center;
        padding: 40px 20px;
        color: #475569;
        font-size: 15px;
    }

    .no-result span {
        display: block;
        font-size: 32px;
        margin-bottom: 10px;
    }

    /* BACK BUTTON */
    .back-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 18px;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
    }

    .back-btn:hover {
        background: #1e40af;
    }
</style>

<div class="result-wrapper">
    <div class="result-card">

        <h2>Search Results</h2>

        <?php if (!empty($query)): ?>
            <div class="search-info">
                Showing results for:
                <b><?= htmlspecialchars($query) ?></b>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="msg-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($results)): ?>
            <table class="result-table">
                <thead>
                    <tr>
                        <th>Pass No</th>
                        <th>CINO</th>
                        <th>Party Name</th>
                        <th>Court / Item</th>
                        <th>Generated On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['pass_no']) ?></td>
                            <td><?= htmlspecialchars($r['cino']) ?></td>
                            <td><?= htmlspecialchars($r['party_name']) ?></td>
                            <td><?= htmlspecialchars($r['court_no']) ?> / <?= htmlspecialchars($r['item_no']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($r['entry_dt'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-result">
                <span>🔍</span>
                No passes found for the given search.
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
