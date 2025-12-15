<?php include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        background: #f3f4f6;
        font-family: "Inter", "Segoe UI", sans-serif;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Main container */
    .pass-container {
        background: #fff;
        padding: 25px;
        max-width: 1250px;
        margin: 30px auto;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
    }

    h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 22px;
        color: #111827;
    }

    /* Responsive Filter Grid (replace your previous .filter-grid block) */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        align-items: end;
        /* align items so button sits at bottom */
    }

    /* Label + input styles */
    .filter-grid label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }

    .filter-grid input,
    .filter-grid select,
    .filter-grid textarea {
        width: 100%;
        padding: 10px 12px;
        font-size: 15px;
        border-radius: 8px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        box-sizing: border-box;
    }

    /* Make the button cell align nicely */
    .filter-grid>div:last-child {
        display: flex;
        align-items: flex-end;
        /* anchor button to bottom of grid cell */
        justify-content: flex-start;
    }

    /* Apply button */
    .apply-btn {
        background: #2563eb;
        color: white;
        border: none;
        padding: 11px 14px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: .15s ease;
        width: 160px;
        /* default width on larger screens */
    }

    .apply-btn:hover {
        background: #1d4ed8;
    }

    /* Make the apply button full-width on small screens */
    @media (max-width: 700px) {
        .filter-grid {
            grid-template-columns: 1fr;
            /* single column stack */
            gap: 12px;
            align-items: stretch;
        }

        .filter-grid>div:last-child {
            justify-content: stretch;
            align-items: stretch;
        }

        .apply-btn {
            width: 100%;
            padding: 12px;
        }
    }

    /* Extra polish: reduce vertical spacing for compact screens */
    @media (max-width: 420px) {
        .filter-grid label {
            font-size: 13px;
        }

        .filter-grid input {
            padding: 9px 10px;
            font-size: 14px;
        }

        .apply-btn {
            font-size: 15px;
            padding: 10px;
        }
    }

    .apply-btn:hover {
        background: #1d4ed8;
    }

    /* TABLE WRAPPER */
    .table-wrap {
        margin-top: 35px;
    }

    #passTable {
        width: 100% !important;
    }

    /* Remove Datatables default search */
    .dataTables_filter {
        display: none !important;
    }

    .view-link {
        color: #2563eb;
        font-weight: 600;
    }
</style>


<div class="pass-container">

    <h2>Pass Management</h2>

    <!-- FILTER BAR -->
    <div class="filter-grid">

        <div>
            <label>From Date</label>
            <input type="date" id="filter_from">
        </div>

        <div>
            <label>To Date</label>
            <input type="date" id="filter_to">
        </div>

        <div>
            <label>Pass No / CINO</label>
            <input type="text" id="filter_pass" placeholder="Type to search…">
        </div>

        <div>
            <label>Adv Enroll</label>
            <input type="text" id="filter_adv" placeholder="Type to search…">
        </div>

        <div>
            <button id="applyFilters" class="apply-btn">Apply Filters</button>
        </div>
    </div>


    <!-- TABLE -->
    <div class="table-wrap">
        <table id="passTable" class="display nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pass No</th>
                    <th>Entry Date</th>
                    <th>Pass For</th>
                    <th>Adv Enroll</th>
                    <th>Court/Item</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($list)): foreach ($list as $r): ?>
                        <tr>
                            <td><?= $r['id'] ?></td>
                            <td><?= $r['pass_no'] ?></td>
                            <td><?= $r['entry_dt'] ?></td>
                            <td>
                                <?php
                                switch (trim($r['passfor']) ?? '') {
                                    case 'P':
                                        echo 'Party in Person';
                                        break;
                                    case 'C':
                                        echo 'Advocate';
                                        break;
                                    case 'L':
                                        echo 'Litigant';
                                        break;
                                    default:
                                        echo '—';
                                }
                                ?>
                            </td>

                            <td><?= htmlspecialchars($r['adv_enroll'] ?? '—') ?></td>
                            <td>
                                <?= htmlspecialchars($r['court_no'] ?? '—') ?>/<?= htmlspecialchars($r['item_no'] ?? '—') ?>
                            </td>
                            <td><a class="view-link" href="index.php?r=pass/view&id=<?= $r['id'] ?>">View</a></td>
                        </tr>

                <?php endforeach;
                endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    let table;

    $(document).ready(function() {

        // Initialize DataTable
        table = $('#passTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [0, 'desc']
            ]
        });

        // 🔥 LIVE SEARCH — PASS NO
        $('#filter_pass').on("keyup", function() {
            table.column(1).search(this.value).draw();
        });

        // 🔥 LIVE SEARCH — ADV ENROLL
        $('#filter_adv').on("keyup", function() {
            table.column(4).search(this.value).draw();
        });

        // 🔥 DATE FILTER — PAGE RELOAD
        $('#applyFilters').click(function() {
            let params = new URLSearchParams({
                r: "pass/list",
                from: $('#filter_from').val(),
                to: $('#filter_to').val(),
                q: $('#filter_pass').val(),
                adv: $('#filter_adv').val()
            });

            window.location.href = "index.php?" + params.toString();
        });
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>