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
        if (
            isset($_SESSION['admin_user']) &&
            !empty($_SESSION['admin_user']['id'])
        ) {

            $roleId = (int) ($_SESSION['admin_user']['role_id'] ?? 0);
            if ($roleId === 20) {
                // Admin
                header("Location: /HC-EPASS-MVC/public/index.php?r=dashboard/index");
                exit;
            }

            if ($roleId === 10) {
                // Officer
                header("Location: /HC-EPASS-MVC/public/index.php?r=officer/dashboard");
                exit;
            }
            session_destroy();
            header("Location: /HC-EPASS-MVC/public/index.php?r=auth/loginForm");
            exit;
        }
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
                'role_id' => (int)$user['role_id'],
                // 'establishment' => $user['estt']
            ];

            if ($user['role_id'] == 20) {  // Admin
                header("Location: /HC-EPASS-MVC/public/index.php?r=dashboard/index");
                exit();
            }

            if ($user['role_id'] == 10) { // Officer
                header("Location: /HC-EPASS-MVC/public/index.php?r=officer/dashboard");
                exit();
            }
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
        // token from URL → ?token=abcd1234
        $token = $_GET['token'] ?? '';

        // your secure token (store in config or database)
        $validToken = "c472dd7564a5fb4056683aeb67a7f323a194c7cfff6a2bc4223b031e4048e524";

        // token check
        if ($token !== $validToken) {
            http_response_code(403);
            die("Forbidden: Invalid or Missing Token");
        }

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
        if ($data['username'] == '' || $data['name'] == '' || $data['password'] == '') {
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
    public function actionUserList()
    {
        $model = new User();
        $users = $model->getAllUsers();

        $this->render("auth/userList", [
            "users" => $users
        ]);
    }
    public function registerOfficerForm()
    {
        $this->render("auth/registerOfficerForm");
    }
    public function registerOfficerPost()
    {
        $this->requireRole([20]); // admin only

        $username = trim($_POST['username']);
        $name     = trim($_POST['name']);
        $gender   = trim($_POST['gender']);
        $email    = trim($_POST['email']);
        $contact  = trim($_POST['contact']);
        $password = trim($_POST['password']);
        $est = $_POST['establishment'] ?? '';

        if (!in_array($est, ['P', 'B'])) {
            $error = "Invalid establishment selected.";
            // reload form
        }


        if ($this->userModel->findByUsername($username)) {
            $this->render("auth/registerOfficerForm", [
                "error" => "Username already exists!"
            ]);
            return;
        }
        $ok = $this->userModel->createOfficer(
            $username,
            $name,
            $gender,
            $email,
            $contact,
            $password,
            10,
            $est
        );
        if ($ok) {
            $this->render("auth/registerOfficerForm", [
                "success" => "Officer created successfully!"
            ]);
        } else {
            $this->render("auth/registerOfficerForm", [
                "error" => "Error: could not create officer."
            ]);
        }
    }



    public function userList()
    {

        // ensure only admin can view
        if (!isset($_SESSION['admin_user']) || $_SESSION['admin_user']['role_id'] != 20) {
            header("Location: /HC-EPASS-MVC/public/index.php?r=auth/loginForm");
            exit;
        }

        $model = new User();
        $users = $model->getAllUsers();

        $this->render("auth/userList", [
            "users" => $users
        ]);
    }


    public function findByEnroll()
    {
        $this->requireRole([10]);

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Invalid request"
            ]);
            return;
        }

        // 🔐 Decode enroll number (important)
        $enrollEnc = $_POST['enroll_no'] ?? '';
        $enrollNo  = trim($this->decodeField($enrollEnc));

        if ($enrollNo === '') {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Enrollment number required"
            ]);
            return;
        }
        $sql = "
        SELECT *
        FROM advocate_t
        WHERE adv_reg = :enroll
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":enroll" => $enrollNo
        ]);

        $adv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$adv) {
            echo json_encode([
                "status" => "NOT_FOUND",
                "message" => "Advocate not found in High Court records"
            ]);
            return;
        }

        echo json_encode([
            "status" => "FOUND",
            "data" => [
                "adv_code"   => $adv["adv_code"],
                "adv_reg"    => $adv["adv_reg"],
                "adv_name"   => $adv["adv_name"],
                "mobile"     => $adv["adv_mobile"],
                "email"      => $adv["email"],
                "gender"     => $adv["adv_sex"],
                "adv_type"   => $adv["advocate_type"],
                "address"   => $adv["address"]

            ]
        ]);
    }



    public function registerAdvocateAjax()
    {
        $this->requireRole([10]);
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(["status" => "ERROR", "message" => "Invalid request"]);
            return;
        }
        $enrollNo = trim($this->decodeField($_POST['enroll_no'] ?? ''));
        $passtype = $_POST['passtype'] ?? '';
        $mobile   = trim($this->decodeField($_POST['mobile'] ?? ''));
        $email    = trim($this->decodeField($_POST['email'] ?? ''));
        $address  = trim($this->decodeField($_POST['address'] ?? ''));

        // 🔒 Basic validation
        if ($enrollNo === '') {
            echo json_encode(["status" => "ERROR", "message" => "Enrollment required"]);
            return;
        }

        if (!in_array($passtype, ['1', '2', '3'], true)) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid pass type"]);
            return;
        }

        if ($mobile === '') {
            echo json_encode(["status" => "ERROR", "message" => "Mobile number required"]);
            return;
        }

        if ($address === '') {
            echo json_encode(["status" => "ERROR", "message" => "Address required"]);
            return;
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid email format"]);
            return;
        }
        $chk = $this->pdo->prepare(
            "SELECT id FROM gatepass_users WHERE enroll_num = :enroll LIMIT 1"
        );
        $chk->execute([":enroll" => $enrollNo]);
        if ($chk->fetch()) {
            echo json_encode(["status" => "ERROR", "message" => "Advocate already registered"]);
            return;
        }
        $stmt = $this->pdo->prepare(
            "SELECT adv_code, adv_name, adv_sex
         FROM advocate_t
         WHERE adv_reg = :enroll
         LIMIT 1"
        );
        $stmt->execute([":enroll" => $enrollNo]);
        $adv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$adv) {
            echo json_encode(["status" => "ERROR", "message" => "Advocate record not found"]);
            return;
        }

        // Gender mapping
        $gender = ($adv['adv_sex'] == '2') ? 'F' : 'M';

        // 🔐 DEFAULT PASSWORD
        $defaultPassword = 'hcgatepass@1234';
        $hashPwd = password_hash($defaultPassword, PASSWORD_BCRYPT);

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // ✅ INSERT
        $sql = "
        INSERT INTO gatepass_users
        (
            name, email, password, enroll_num,
            gender, contact_num,
            status, ip, created,
            role_id, passtype, adv_code, address
        )
        VALUES
        (
            :name, :email, :password, :enroll,
            :gender, :mobile,
            'A', :ip, NOW(),
            2, :passtype, :adv_code, :address
        )
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":name"     => $adv['adv_name'],
            ":email"    => $email ?: null,
            ":password" => $hashPwd,
            ":enroll"   => $enrollNo,
            ":gender"   => $gender,
            ":mobile"   => $mobile,
            ":ip"       => $ip,
            ":passtype" => $passtype,
            ":adv_code" => $adv['adv_code'],
            ":address"  => $address
        ]);

        echo json_encode([
            "status"  => "OK",
            "message" => "Advocate registered successfully. Default password is hcgatepass@1234"
        ]);
    }
    public function registerPartyAjax()
    {
        $this->requireRole([10]);
        header('Content-Type: application/json');

        $name   = trim($this->decodeField($_POST['party_name']) ?? '');
        $mobile = trim($this->decodeField($_POST['mobile']) ?? '');
        $email  = trim($this->decodeField($_POST['email'] )?? '');
        $est    = trim($this->decodeField($_POST['estt']) ?? '');
        $address  = trim($this->decodeField($_POST['address']) ?? '');
        $password = 'Hcgatepass@123';
        $passtype = 3;
        $roleId   = 2; 
        if ($name === '' || $mobile === '' || $email === '' || $address ==='') {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "All required fields must be filled",
                "code"    => 400
            ]);
            return;
        }
        if (!in_array($est, ['P', 'B'], true)) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Invalid establishment selected",
                "code"    => 400
            ]);
            return;
        }
        if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Invalid mobile number",
                "code"    => 400
            ]);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Invalid email address",
                "code"    => 400
            ]);
            return;
        }
        if ($this->userModel->findPartyByMobile($mobile)) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Party already exists with this mobile number",
                "code"    => 409
            ]);
            return;
        }
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $ok = $this->userModel->createPartyUser(
            $name,
            $mobile,
            $email,
            $passwordHash,
            $roleId,
            $passtype,
            $est,
            $address
        );

        if ($ok) {
            echo json_encode([
                "status"  => "OK",
                "message" => "Party registered successfully"
            ]);
        } else {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Could not register party",
                "code"    => 500
            ]);
        }
    }




    public function sendOtp()
    {
        session_start();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $mobile = trim($data['mobile'] ?? '');

        // 🔒 Basic validation
        if (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Invalid mobile number"
            ]);
            return;
        }

        // 🔥 Generate OTP
        $varcode = rand(100001, 999999);

        // 🔥 SMS CONFIG (AS-IS FROM YOUR ADVOCATE PORTAL)
        $dlt_template_id = '1107160033837759671';
        $message = "OTP for RHC GATE PASS is $varcode";
        $message = urlencode($message);

        $url = "https://smsgw.sms.gov.in/failsafe/HttpLink?"
            . "username=courts-raj.sms"
            . "&pin=A%25%5Eb3%24*z7"
            . "&message=$message"
            . "&mnumber=$mobile"
            . "&signature=RCOURT"
            . "&dlt_entity_id=1101333050000031038"
            . "&dlt_template_id=$dlt_template_id";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($ch);
        curl_close($ch);
        $_SESSION['epass_otp'][$mobile] = [
            'otp'       => $varcode,
            'timestamp' => time()
        ];

        echo json_encode([
            "status"  => "OK",
            "message" => "OTP sent successfully"
        ]);
    }
    public function verifyOtp()
    {
        session_start();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $mobile = trim($data['mobile'] ?? '');
        $otp    = trim($data['otp'] ?? '');

        if (
            empty($mobile) ||
            empty($otp) ||
            !isset($_SESSION['epass_otp'][$mobile])
        ) {
            echo json_encode(["status" => "ERROR"]);
            return;
        }

        $saved = $_SESSION['epass_otp'][$mobile];

        // ⏱ OTP expiry (3 minutes)
        if (time() - $saved['timestamp'] > 180) {
            unset($_SESSION['epass_otp'][$mobile]);
            echo json_encode([
                "status"  => "ERROR",
                "message" => "OTP expired"
            ]);
            return;
        }

        if ($otp != $saved['otp']) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Invalid OTP"
            ]);
            return;
        }

        // ✅ VERIFIED
        $_SESSION['epass_otp'][$mobile]['verified'] = true;

        echo json_encode([
            "status" => "OK"
        ]);
    }
}
