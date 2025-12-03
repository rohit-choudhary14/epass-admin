<?php include __DIR__ . '/../layouts/header.php'; ?>
<h2>Pass #<?php echo htmlspecialchars($p['pass_no']); ?></h2>
<dl>
    <dt>Entry date</dt><dd><?php echo htmlspecialchars($p['entry_dt_str']); ?></dd>
    <dt>CINO</dt><dd><?php echo htmlspecialchars($p['cino']); ?></dd>
    <dt>Advocate</dt><dd><?php echo htmlspecialchars($p['adv_enroll']); ?></dd>
    <dt>Pass for</dt><dd><?php echo htmlspecialchars($p['passfor']); ?></dd>
    <dt>Court / Item</dt><dd><?php echo htmlspecialchars($p['court_no']).' / '.htmlspecialchars($p['item_no']); ?></dd>
    <dt>Address</dt><dd><?php echo htmlspecialchars($p['paddress']); ?></dd>
</dl>
<p><a href="/HC-EPASS-MVC/public/index.php?r=pass/list">Back to list</a></p>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
