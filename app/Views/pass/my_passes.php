<?php include __DIR__ . '/../layouts/header.php'; ?>
<style>
* { box-sizing: border-box; }

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
    box-shadow: 0 8px 30px rgba(0,0,0,0.07);
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

table.dataTable tbody tr:hover td {
    background-color: #f8fafc !important;
}

/* BUTTONS */
.btn-view, .btn-pdf {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
    font-weight: 600;
    color: #fff !important;
}

.btn-view { background: #2563eb; }
.btn-view:hover { background: #1d4ed8; }

.btn-pdf { background: #dc2626; }
.btn-pdf:hover { background: #b91c1c; }

/* MOBILE RESPONSIVE */
@media (max-width:768px) {

    #generatedPassTable thead {
        display: none !important;
    }

    #generatedPassTable tbody tr {
        display: block;
        margin-bottom: 16px;
        padding: 15px;
        border-radius: 12px;
        background: #f8fafc !important;
        box-shadow: 0 4px 18px rgba(0,0,0,0.06);
    }

    #generatedPassTable tbody td {
        display: flex;
        justify-content: space-between;
        padding: 10px 6px !important;
        border: none !important;
        font-size: 14px;
    }

    #generatedPassTable tbody td:before {
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

    <h2 class="page-title">Court Passes</h2>

    <div class="card-box">

        <div class="table-wrap">
            <table id="generatedPassTable" class="table table-bordered nowrap" style="width:100%">

                <thead>
                    <tr>
                        <th>Pass No</th>
                        <th>CINO</th>
                        <th>Court No</th>
                        <th>Item No</th>
                        <th>Generated</th>
                        <th>Party Name</th>
                        <th>View</th>
                        <th>PDF</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($passes as $p): ?>
                    <tr>
                        <td data-label="Pass No"><?= htmlspecialchars($p['pass_no']) ?></td>
                        <td data-label="CINO"><?= htmlspecialchars($p['cino']) ?></td>
                        <td data-label="Court No"><?= htmlspecialchars($p['court_no']) ?></td>
                        <td data-label="Item No"><?= htmlspecialchars($p['item_no']) ?></td>
                        <td data-label="Generated"><?= date("d-m-Y H:i", strtotime($p['entry_dt'])) ?></td>
                        <td data-label="Party Name"><?= htmlspecialchars($p['party_name'] ?: '-') ?></td>

                        <!-- View Button -->
                        <td data-label="View">
                            <a class="btn-view" href="/HC-EPASS-MVC/public/index.php?r=pass/view&id=<?= $p['id'] ?>">
                                View
                            </a>
                        </td>

                        <!-- PDF Button -->
                        <td data-label="PDF">
                            <a class="btn-pdf" href="/HC-EPASS-MVC/public/index.php?r=pass/downloadPdf&id=<?= $p['id'] ?>">
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
$(document).ready(function () {

    $('#generatedPassTable').DataTable({
        order: [[4, 'desc']], 
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
