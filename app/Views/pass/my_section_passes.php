<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
    * {
        box-sizing: border-box;
    }

    .page-wrapper {
        max-width: 1250px;
        margin: 32px auto;
        padding: 0 18px;
        font-family: "Inter", sans-serif;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 22px;
    }

    .card-box {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.07);
        border: 1px solid #e2e8f0;
    }

    /* DATATABLE HEADER */
    table.dataTable thead th {
        background-color: #1e3a8a !important;
        color: white !important;
        padding: 14px !important;
        font-size: 14px;
        text-transform: uppercase;
    }

    /* ROW STYLE */
    table.dataTable tbody td {
        vertical-align: middle !important;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    table.dataTable tbody tr {
        background-color: #ffffff !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    table.dataTable tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* BUTTONS */
    .btn-view,
    .btn-pdf {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        font-weight: 600;
        color: #fff !important;
    }

    .btn-view {
        background: #2563eb;
    }

    .btn-view:hover {
        background: #1d4ed8;
    }

    .btn-pdf {
        background: #dc2626;
    }

    .btn-pdf:hover {
        background: #b91c1c;
    }

    /* MOBILE RESPONSIVE */
    @media (max-width:768px) {
        #sectionPassTable thead {
            display: none !important;
        }

        #sectionPassTable tbody tr {
            display: block;
            margin-bottom: 16px;
            padding: 15px;
            border-radius: 12px;
            background: #f8fafc !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        #sectionPassTable tbody td {
            display: flex;
            justify-content: space-between;
            padding: 10px 6px !important;
            border: none !important;
            font-size: 14px;
        }

        #sectionPassTable tbody td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            font-size: 11px;
            flex-basis: 40%;
        }
    }
</style>


<div class="page-wrapper">

    <h2 class="page-title">Section Passes</h2>

    <div class="card-box">

        <div class="table-wrap">
            <table id="sectionPassTable" class="table table-bordered nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Pass No</th>
                        <th>Advocate</th>
                        <th>Enrollment No</th>
                        <th>Visit Date</th>
                        <th>Purpose</th>
                        <th>Pass For</th>
                        <th>View</th>
                        <th>PDF</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($passes as $p): ?>
                        <tr>
                            <td data-label="Pass No"><?= htmlspecialchars($p['pass_no']) ?></td>
                            <td data-label="Advocate"><?= htmlspecialchars($p['name']) ?></td>
                            <td data-label="Enrollment No"><?= htmlspecialchars($p['enroll_no']) ?></td>
                            <td data-label="Visit Date"><?= date("d-m-Y", strtotime($p['pass_dt'])) ?></td>

                            <td data-label="Purpose">
                                <?php
                                $remarks = json_decode($p['purposermks'], true);
                                if ($remarks && is_array($remarks)) {
                                    echo "<ul style='margin:0;padding-left:18px'>";
                                    foreach ($remarks as $r) {
                                        echo "<li>" . htmlspecialchars($r['remark']) . "</li>";
                                    }
                                    echo "</ul>";
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                            <?php $passForLabel = '-';

                            if (!empty($p['passfor'])) {
                                switch (trim($p['passfor'])) {
                                    case 'LS':
                                        $passForLabel = 'Litigant Section';
                                        break;
                                    case 'PS':
                                        $passForLabel = 'PIP Section';
                                        break;
                                    case 'S':
                                        $passForLabel = 'Advocate Section';
                                        break;
                                }
                            }
                            ?>
                            <td data-label="Pass for"><?= htmlspecialchars($passForLabel) ?></td>

                            <!-- VIEW button in its own column -->
                            <td data-label="View">
                                <a class="btn-view"
                                    href="/HC-EPASS-MVC/public/index.php?r=pass/viewSection&id=<?= $p['id'] ?>">
                                    View
                                </a>
                            </td>

                            <!-- PDF button in its own column -->
                            <td data-label="PDF">
                                <a class="btn-pdf"
                                    href="/HC-EPASS-MVC/public/index.php?r=pass/printSection&id=<?= $p['id'] ?>">
                                    PDF
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {

        $('#sectionPassTable').DataTable({
            order: [
                [3, 'desc']
            ],
            pageLength: 10,
            responsive: true,
            scrollX: true,
            autoWidth: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
            }
        });

    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>