<?php
require_once __DIR__ . '/../Models/User.php';

class AuthController extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function loginForm()
    {
        include __DIR__ . '/../Views/auth/login.php';
    }

public function loginPost()
{
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = "Missing credentials";
        include __DIR__ . '/../Views/auth/login.php';
        return;
    }

    $user = $this->userModel->findByUsername($username);

    if (!$user) {
        $error = "Invalid credentials";
        include __DIR__ . '/../Views/auth/login.php';
        return;
    }

    // correct call
    $enc = $this->userModel->encryptData($password);

    if ($enc === $user['password']) {
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role_id' => (int)$user['role_id']
        ];
        header('Location: /hc-epass-mvc/public/index.php?r=dashboard/index');
        exit();
    } else {
        $error = "Invalid credentials";
        include __DIR__ . '/../Views/auth/login.php';
        return;
    }
}



    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /HC-EPASS-MVC/public/index.php?r=auth/login');
        exit();
    }

    public function registerForm()
    {
        include __DIR__ . '/../Views/auth/register.php';
    }

    public function registerPost()
    {
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'contact' => trim($_POST['contact'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'password' => $_POST['password'] ?? ''
        ];

        // basic validation
        if ($data['username']=='' || $data['name']=='' || $data['password']=='') {
            $error = "Username, name, password required";
            include __DIR__ . '/../Views/auth/register.php';
            return;
        }

        // check existing
        if ($this->userModel->findByUsername($data['username'])) {
            $error = "Username exists";
            include __DIR__ . '/../Views/auth/register.php';
            return;
        }

        $id = $this->userModel->createAdmin($data);
        if ($id) {
            $success = "Admin created (ID: $id). You can login now.";
            include __DIR__ . '/../Views/auth/register.php';
        } else {
            $error = "Database error";
            include __DIR__ . '/../Views/auth/register.php';
        }
    }
}
