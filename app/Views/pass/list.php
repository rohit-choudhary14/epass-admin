<?php include __DIR__ . '/../layouts/header.php'; ?>

<!-- DATATABLE CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<style>
/* your same CSS here — NOTHING CHANGED */
body {
    background:#f5f7fb;
    font-family:"Inter","Segoe UI",sans-serif;
}
.pass-container {
    background:#fff;
    padding:25px;
    max-width:1250px;
    margin:30px auto;
    border-radius:12px;
    border:1px solid #e5e7eb;
}
h2 { font-size:26px; font-weight:700; margin-bottom:25px; }
.filters { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:25px; }
.filters label { font-weight:600; font-size:13px; margin-bottom:5px; color:#374151; }
.filters input { width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; }
.apply-btn { background:#2563eb; padding:10px 20px; border:none; color:white; border-radius:6px; font-weight:600; cursor:pointer; margin-top:22px; }
.table-wrap { overflow-x:auto; }
td, th { padding:12px !important; }
</style>

<div class="pass-container">

<h2>Pass Management</h2>

<!-- FILTERS -->
<form method="GET" action="index.php">
    <input type="hidden" name="r" value="pass/list">
    <div class="filters">
        <div>
            <label>From</label>
            <input type="date" name="from" value="<?= $from ?? '' ?>">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="to" value="<?= $to ?? '' ?>">
        </div>
        <div>
            <label>Pass No / CINO</label>
            <input type="text" name="q" value="<?= $q ?? '' ?>">
        </div>
        <div>
            <label>Adv Enroll</label>
            <input type="text" name="adv" value="<?= $adv ?? '' ?>">
        </div>
        <div>
            <button class="apply-btn">Apply</button>
        </div>
    </div>
</form>

<!-- TABLE -->
<div class="table-wrap">
<table id="passTable" class="display nowrap" style="width:100%">
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
        <?php if(!empty($list)): foreach($list as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['pass_no'] ?></td>
                <td><?= $r['entry_dt'] ?></td>
                <td><?= $r['passfor'] ?></td>
                <td><?= $r['adv_enroll'] ?></td>
                <td><?= $r['court_no'] ?>/<?= $r['item_no'] ?></td>
                <td><a class="view-link" href="index.php?r=pass/view&id=<?= $r['id'] ?>">View</a></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="7" style="text-align:center; padding:20px;">No records found</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

</div>

<script>
$(document).ready(function() {
    $('#passTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[0, 'desc']]
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
