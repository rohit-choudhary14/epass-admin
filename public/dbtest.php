<?php
require_once __DIR__ . '/../bootstrap.php';

echo "<pre>";

$stmt = $pdo->query("SELECT COUNT(*) FROM gatepass_details");
echo "Rows in gatepass_details = " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query("SELECT * FROM gatepass_details LIMIT 3");
print_r($stmt->fetchAll());
