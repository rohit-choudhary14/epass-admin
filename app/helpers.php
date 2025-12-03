<?php
// existing view() function — keep it
if (!function_exists('view')) {
    function view($path, $data = []) {
        extract($data);
        $file = __DIR__ . '/Views/' . $path . '.php';
        if (file_exists($file)) include $file;
        else echo "View not found: $file";
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
