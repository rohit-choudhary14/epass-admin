<?php
class BaseController
{
    protected $pdo;
    public function __construct()
    {
        $this->pdo = $GLOBALS['pdo'];
    }

    protected function requireAuth()
    {
        if (!isset($_SESSION['admin_user']) || empty($_SESSION['admin_user']['id'])) {
            header('Location: /HC-EPASS-MVC/public/index.php?r=auth/login');
            exit();
        }
    }

    protected function requireRole(array $roles)
    {
        $this->requireAuth();
        $rid = $_SESSION['admin_user']['role_id'];

        if (!in_array($rid, $roles)) {
            http_response_code(403);
            include __DIR__ . '/../Views/errors/403.php';
            exit();
        }
    }
    protected function render($view, $data = [])
    {
        extract($data);

        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: " . $viewFile);
        }

        include $viewFile;
    }
    function decodeField($value)
    {
        return urldecode(base64_decode($value));
    }
    function decodeFieldJson($v)
    {
        return json_decode(base64_decode($v), true) ?: base64_decode($v);
    }
    function getEncryptValue($input)
    {
        $password = 'Hcraj@123';
        $method = 'AES-256-CBC';
        $password = substr(hash('SHA256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        return base64_encode(openssl_encrypt($input, $method, $password, OPENSSL_RAW_DATA, $iv));
    }
    function getDecryptValue($input)
    {
        $password = 'Hcraj@123';
        $method = 'AES-256-CBC';
        $password = substr(hash('SHA256', $password, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        return openssl_decrypt(base64_decode($input), $method, $password, OPENSSL_RAW_DATA, $iv);
    }
}
