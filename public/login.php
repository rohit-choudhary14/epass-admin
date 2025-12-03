<?php
require_once __DIR__ . '/../bootstrap.php';

$ac = new AuthController(); // correct spelling

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ac->loginPost();
    exit();
}

$ac->loginForm(); // correct method
