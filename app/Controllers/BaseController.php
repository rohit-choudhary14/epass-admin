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
            echo "Forbidden";
            exit();
        }
    }
}
