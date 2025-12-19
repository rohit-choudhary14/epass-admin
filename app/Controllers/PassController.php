<?php
require_once __DIR__ . "/../libraries/tcpdf/tcpdf.php";
class PassController extends BaseController
{
    protected $passModel;

    public function __construct()
    {
        parent::__construct();
        $this->passModel = new Pass();
    }

    public function list()
    {
        $this->requireAuth();
        $this->requireRole([20]);

        // FIX: Show ALL passes by default
        $from = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : '2000-01-01';
        $to   = isset($_GET['to'])   && $_GET['to']   !== '' ? $_GET['to']   : date('Y-m-d');

        $passes = $this->passModel->list($from, $to, 10000);

        view('pass/list', [
            'list' => $passes,
            'from' => $from,
            'to'   => $to
        ]);
    }

    public function view()
    {
        $this->requireAuth();
        // $this->requireRole([20]);

        if (!isset($_GET['id'])) {
            echo "Missing ID";
            exit;
        }

        $id = (int) $_GET['id'];
        $p = $this->passModel->find($id);

        if (!$p) {
            echo "Pass not found";
            exit;
        }


        switch (trim($p['passfor'])) {

            case 'C':   // Advocate
                view('pass/view', ['p' => $p]);
                break;

            case 'L':   // Litigant
                view('pass/viewLitigant', ['p' => $p]);
                break;

            case 'P':   // Party in Person
                view('pass/viewParty', ['p' => $p]);
                break;

            default:
                echo "Invalid pass type";
                exit;
        }
    }
    public function ajaxList()
    {
        $this->requireAuth();
        $this->requireRole([20]);

        $opts = [
            'from'    => $_GET['from'] ?? null,
            'to'      => $_GET['to'] ?? null,
            'q'       => $_GET['q'] ?? null,
            'adv'     => $_GET['adv'] ?? null,
            'sort'    => $_GET['sort'] ?? 'entry_dt',
            'dir'     => $_GET['dir'] ?? 'DESC',
            'page'    => $_GET['page'] ?? 1,
            'perPage' => $_GET['perPage'] ?? 50
        ];

        $passModel = new Pass();
        $data = $passModel->listPaginated($opts);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    public function exportCsv()
    {
        $this->requireAuth();
        $this->requireRole([20]);

        $opts = [
            'from'    => $_GET['from'] ?? null,
            'to'      => $_GET['to'] ?? null,
            'q'       => $_GET['q'] ?? null,
            'adv'     => $_GET['adv'] ?? null,
            'sort'    => $_GET['sort'] ?? 'entry_dt',
            'dir'     => $_GET['dir'] ?? 'DESC',
            'page'    => 1,
            'perPage' => 50000 // export all
        ];

        $passModel = new Pass();
        $data = $passModel->listPaginated($opts);

        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=passes.csv");

        $out = fopen("php://output", "w");

        fputcsv($out, ['ID', 'Pass No', 'Date', 'For', 'Adv Enroll', 'Court', 'Item', 'CINO']);

        foreach ($data['rows'] as $r) {
            fputcsv($out, [
                $r['id'],
                $r['pass_no'],
                $r['entry_dt'],
                $r['passfor'],
                $r['adv_enroll'],
                $r['court_no'],
                $r['item_no'],
                $r['cino'] ?? ''
            ]);
        }

        fclose($out);
        exit;
    }
    public function revoke()
    {
        $this->requireAuth();
        $this->requireRole(['admin']); // only admin

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['ok' => false]);
            exit;
        }

        $id = (int) $_POST['id'];
        $passModel = new Pass();

        $ok = $passModel->revoke($id, $_SESSION['admin_user']['id']);

        echo json_encode(['ok' => $ok]);
        exit;
    }
    public function generate()
    {
        $this->requireRole([10]); // officer only
        $type = $_GET['type'] ?? '';

        // STEP 1: Show destination chooser for some types
        if (in_array($type, ['advocate', 'partyinperson', 'litigant', 'vendor'])) {
            // show choose destination screen
            $this->render("pass/choose_destination");
            return;
        }

        // // STEP 2: These types do NOT need destination chooser
        // if ($type == 'court') {
        //     $this->render("pass/generate_court");
        //     return;
        // }
        // if ($type == 'section') {
        //     $this->render("pass/generate_section");
        //     return;
        // }

        echo "Invalid pass type";
    }
    public function actionGenerateForm()
    {
        $this->requireRole([10]); // Officer only

        $type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
        $goto = isset($_GET['goto']) ? strtolower(trim($_GET['goto'])) : '';

        // Allowed types
        $validTypes = ['advocate', 'litigant', 'partyinperson'];

        // Allowed goto modes
        $validGoto = ['court', 'section'];

        // Validate type
        if (!in_array($type, $validTypes)) {
            die("<h2 style='color:red;text-align:center'>Invalid Pass Type</h2>");
        }

        // Validate goto
        if (!in_array($goto, $validGoto)) {
            die("<h2 style='color:red;text-align:center'>Invalid Destination Option</h2>");
        }
        if ($type === 'advocate' && $goto === 'court') {
            $this->render("pass/generate_court_search", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }
        if ($type === 'litigant' && $goto === 'section') {
            $model = new Pass();
            $purposeList = $model->getPurposeOfVisit();
            $this->render("pass/generate_form_advocate_litigant", [
                "type" => $type,
                "goto" => $goto,
                "purposeList" => $purposeList
            ]);
            return;
        }
        if ($type === 'advocate' && $goto === 'section') {
            $model = new Pass();
            $purposeList = $model->getPurposeOfVisit();
            $this->render("pass/generate_form_advocate", [
                "type" => $type,
                "goto" => $goto,
                "purposeList" => $purposeList
            ]);
            return;
        }

        if ($type === 'partyinperson' && $goto === 'section') {
            $model = new Pass();
            $purposeList = $model->getPurposeOfVisit();
            $this->render("pass/generate_form_pip_section", [
                "type" => $type,
                "goto" => $goto,
                "purposeList" => $purposeList
            ]);
            return;
        }
    }
    public function searchCourtCase()
    {
        $this->requireRole([10]);  // officer only

        $case_type = $_GET['case_type'] ?? '';
        $case_no   = $_GET['case_no'] ?? '';
        $case_year = $_GET['case_year'] ?? '';
        $cl_type   = $_GET['cl_type'] ?? '';
        $cl_date   = $_GET['cl_date'] ?? '';

        $model = new Pass();

        $result = $model->fetchCourtCase($case_type, $case_no, $case_year, $cl_type, $cl_date);

        if ($result === null) {
            $error = "No record found in causelist!";
            include __DIR__ . '/../Views/pass/generate_court_search.php';
            return;
        }

        include __DIR__ . '/../Views/pass/generate_court_result.php';
    }

    public function actionCourtForm()
    {
        $this->requireRole([10]);


        $type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
        $goto = isset($_GET['goto']) ? strtolower(trim($_GET['goto'])) : '';

        require_once __DIR__ . '/../Models/Court.php';
        $court = new Court();

        $caseTypes = $court->getCaseTypes();
        if ($type === 'advocate') {
            $this->render("pass/generate_search_court", [
                "type" => $type,
                "goto" => $goto,
                "caseTypes" => $caseTypes,
            ]);
            return;
        }
        // Load different views based on type
        if ($type === 'litigant') {

            // Load litigant version of the form
            $this->render("pass/generate_search_court_litigant", [
                "caseTypes" => $caseTypes,
                "type"      => $type
            ]);
        } else  if ($type === 'partyinperson') {

            // Load litigant version of the form
            $this->render("pass/generate_search_court_pip", [
                "caseTypes" => $caseTypes,
                "type"      => $type
            ]);
        }
    }


    public function actionSearchCourtCase()
    {
        $this->requireRole([10]);

        // Read POST
        $case_type  = $_POST['case_type'] ?? '';
        $case_no    = $_POST['case_no'] ?? '';
        $case_year  = $_POST['case_year'] ?? '';
        $cl_type    = $_POST['cl_type'] ?? '';
        $cl_date    = $_POST['cl_date'] ?? '';
        $cldt = date("d-m-Y", strtotime($cl_date));
        $cl_dt = date("d-m-Y", strtotime($cl_date));

        // Validate
        if (
            $case_type == '' || $case_no == '' || $case_year == '' ||
            $cl_type == '' || $cl_date == ''
        ) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Missing required parameters"
            ]);
            return;
        }

        // ------------------------------
        // BACKEND VALIDATION FOR CAUSELIST DATE
        // ------------------------------
        $today = new DateTime();
        $today->setTime(0, 0);

        $selected = new DateTime($cl_date);
        $selected->setTime(0, 0);

        $maxDate = clone $today;
        $maxDate->modify('+3 days');

        // Check: Cannot be past date
        // if ($selected <b $today) {
        //     echo json_encode([
        //         "status" => "ERROR",
        //         "message" => "Causelist date cannot be older than today."
        //     ]);
        //     return;
        // }

        // Check: Cannot be more than +3 days ahead
        if ($selected > $maxDate) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Causelist date cannot be more than 3 days ahead."
            ]);
            return;
        }












        require_once __DIR__ . '/../Models/Court.php';
        $court = new Court();

        // Search case using converted dates
        $result = $court->findCourtCase($case_type, $case_no, $case_year, $cl_type, $cldt);


        if (!$result || $result["status"] != "OK") {
            echo json_encode([
                "status" => "NOT_FOUND",
                "message" => $result["message"] ?? "Case not found in causelist"
            ]);
            return;
        }
        echo json_encode($result);
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

    private function getAdvocateDetails($adv_code)
    {
        $sql = "SELECT 
                contact_num,
                enroll_num,
                adv_code,
                address,
                name
            FROM gatepass_users
            WHERE adv_code = :adv_code
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":adv_code" => trim($adv_code)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [
                "mobile"      => null,
                "enroll_num"  => null,
                "adv_code"    => $adv_code,
                "address"    => null,
                "name"       => null
            ];
        }

        return [
            "mobile"      => $this->getEncryptValue($row["contact_num"]),
            "enroll_num"  => $row["enroll_num"],
            "adv_code"    => $row["adv_code"],
            "address"    => $row["address"],
            "name"       => $row["name"]
        ];
    }

    private function getAdvocateDetailsByEnroll($enroll_num)
    {
        $sql = "SELECT 
                contact_num,
                enroll_num,
                adv_code,
                address,
                name
            FROM gatepass_users
            WHERE enroll_num = :enroll_num
            LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":enroll_num" => trim($enroll_num)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [
                "mobile"      => null,
                "enroll_num"  => null,
                "address"    => null,
                "name"       => null
            ];
        }

        return [
            "mobile"      => $this->getEncryptValue($row["contact_num"]),
            "enroll_num"  => $row["enroll_num"],
            "adv_code"    => $row["adv_code"],
            "address"    => $row["address"],
            "name"       => $row["name"]
        ];
    }

    function toNull($v)
    {
        return ($v === "" ? null : $v);
    }
    public function actionGenerateCourtPass()
    {
        $this->requireRole([10]);
        $this->requireAuth();
        $cino      = $this->decodeField(($_POST["cino"]))      ?? '';
        $adv_type  = $this->decodeField($_POST["adv_type"])  ?? '';
        $cldt      = $this->decodeField($_POST["cldt"])     ?? '';
        $cltype    = $this->decodeField($_POST["cltype"])   ?? '';
        $courtno   = $this->decodeField($_POST["courtno"])   ?? '';
        $itemno    = $this->decodeField($_POST["itemno"])    ?? '';
        $party     = $this->decodeField($_POST["party"])    ?? '';
        $passfor   = $this->decodeField($_POST["passfor"])  ?? '';
        $partyno   = $this->decodeField($_POST["partyno"])  ?? '';
        $partynm   = $this->decodeField($_POST["partynm"])  ?? '';
        $partymob  = $this->decodeField($_POST["partymob"]) ?? '';
        $adv_code  = $this->decodeField($_POST["adv_code"])  ?? '';
        $user_ip  = $_SESSION["admin_user"]["username"];
        // ========== DUPLICATE PASS CHECK ==========
        $model = new Pass();
        // Advocate details
        $advDetails = $this->getAdvocateDetails($adv_code);

        if (empty($advDetails['enroll_num'])) {
            echo json_encode([
                "status"  => "ERROR",
                "code" => 404,
                "message" => "Advocate is not registered in the Gate Pass system. Please register first to continue."
            ]);
            return;
        }

        if ($model->isAdvocatePassExists($cino, $adv_code, $courtno, $itemno, $cldt)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "A pass has already been generated for this advocate for the same court, item, and date."
            ]);
            return;
        }
        // Generate pass no
        $pass_no = $cltype . date("dmY", strtotime($cldt)) . $courtno . $itemno . date("His");


        $paddress    = $advDetails['address']     ?? '';
        $adv_enroll  = $advDetails['enroll_num']  ?? null;
        $sql = "INSERT INTO gatepass_details
            (cino, causelist_dt, causelist_type, court_no, item_no, pass_no, adv_type, 
             paddress, party_no, party_name, party_type, party_mob_no, passfor, passtype, 
             entry_dt, adv_code, adv_enroll,user_ip)
            VALUES
            (:cino, :cldt, :cltype, :courtno, :itemno, :pass_no, :adv_type, :paddress, 
             :partyno, :partynm, :party, :mob, :passfor, 2, NOW(), :adv_code, :adv_enroll,:user_ip)
            RETURNING pass_no";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cino"        => $cino,
            ":cldt"        => date("Y-m-d", strtotime($cldt)),
            ":cltype"      => $cltype,
            ":courtno"     => $this->toNull($courtno),
            ":itemno"      => $this->toNull($itemno),
            ":pass_no"     => $pass_no,
            ":adv_type"    => $adv_type,
            ":paddress"    => $paddress,
            ":partyno"     => $this->toNull($partyno),
            ":partynm"     => $partynm,
            ":party"       => $this->toNull($party),
            ":mob"         => $this->toNull($partymob),
            ":passfor"     => $passfor,
            ":adv_code"    => $this->toNull($adv_code),
            ":adv_enroll"  => $adv_enroll,
            ":user_ip"     => $user_ip
        ]);

        echo json_encode([
            "status"  => "OK",
            "pass_no" => $pass_no
        ]);
    }

    public function actionGenerateCourtPassLitigant()
    {
        $this->requireRole([10]);
        $this->requireAuth();
        $cino      = $this->decodeField($_POST["cino"])      ?? '';
        $adv_type  = $this->decodeField($_POST["adv_type"])  ?? '';
        $cldt      = $this->decodeField($_POST["cldt"])      ?? '';
        $cltype    = $this->decodeField($_POST["cltype"])   ?? '';
        $courtno   = $this->decodeField($_POST["courtno"])  ?? '';
        $itemno    = $this->decodeField($_POST["itemno"])    ?? '';
        $party     = $this->decodeField($_POST["party"])    ?? '';
        $passfor   = $this->decodeField($_POST["passfor"])   ?? '';
        $partyno   = $this->decodeField($_POST["partyno"])   ?? '';
        $partynm   = $this->decodeField($_POST["lit_name"])  ?? '';
        $partymob  = $this->decodeField($_POST["lit_mobile"]) ?? '';
        $adv_code  = $this->decodeField($_POST["recommended_code"]) ?? '';
        $sideText = '';
        if ($adv_type == 1) {
            $sideText = ' (Petitioner side)';
        } elseif ($adv_type == 2) {
            $sideText = ' (Respondent side)';
        }
        if ($adv_code == 0) {
            echo json_encode([
                "status"  => "ERROR",
                "code" => 404,
                "message" => "Advocate{$sideText} is not registered in the Gate Pass system. Please register first."
            ]);
            return;
        }


        $user_ip  = $_SESSION["admin_user"]["username"];
        // VALID MOBILE
        if (!preg_match('/^[6-9][0-9]{9}$/', $partymob)) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid mobile number."]);
            return;
        }
        $partymob = $this->getEncryptValue($partymob);

        if ($partynm === "" || $partymob === "") {
            echo json_encode(["status" => "ERROR", "message" => "Litigant name and mobile are required."]);
            return;
        }
        // Advocate details
        $advDetails = $this->getAdvocateDetails($adv_code);

        if (empty($advDetails['enroll_num'])) {
            echo json_encode([
                "status"  => "ERROR",
                "code" => 404,
                "message" => "Advocate{$sideText} is not registered in the Gate Pass system. Please register first."
            ]);
            return;
        }

        // ===== DUPLICATE CHECK =====
        $model = new Pass();
        $exists = $model->checkLitigantPassExists(
            $this->decodeField($_POST['lit_mobile']),
            $this->decodeField($_POST['courtno']),
            $this->decodeField($_POST['itemno']),
            $this->decodeField($_POST['cldt']),
            $this->decodeField($_POST['recommended_code'])
        );

        if ($exists) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Litigant already has a pass <b>" .  $this->decodeField($_POST['cldt'])  . "</b> for this court & item (recommended by same advocate)."
            ]);
            return;
        }
        // Generate pass no
        $pass_no = $cltype . date("dmY", strtotime($cldt)) . $courtno . $itemno . date("His");


        $paddress    = $this->decodeField($_POST["lit_address"])  ?? '';
        $adv_enroll  = $advDetails['enroll_num']  ?? null;
        $sql = "INSERT INTO gatepass_details
            (cino, causelist_dt, causelist_type, court_no, item_no, pass_no, adv_type, 
             paddress, party_no, party_name, party_type, party_mob_no, passfor, passtype, 
             entry_dt, adv_code, adv_enroll,user_ip)
            VALUES
            (:cino, :cldt, :cltype, :courtno, :itemno, :pass_no, :adv_type, :paddress, 
             :partyno, :partynm, :party, :mob, :passfor, 2, NOW(), :adv_code, :adv_enroll,:user_ip)
            RETURNING pass_no";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cino"        => $cino,
            ":cldt"        => date("Y-m-d", strtotime($cldt)),
            ":cltype"      => $cltype,
            ":courtno"     => $this->toNull($courtno),
            ":itemno"      => $this->toNull($itemno),
            ":pass_no"     => $pass_no,
            ":adv_type"    => $adv_type,
            ":paddress"    => $paddress,
            ":partyno"     => $this->toNull($partyno),
            ":partynm"     => $partynm,
            ":party"       => $this->toNull($party),
            ":mob"         => $this->toNull($partymob),
            ":passfor"     => $passfor,
            ":adv_code"    => $this->toNull($adv_code),
            ":adv_enroll"  => $adv_enroll,
            ":user_ip"     => $user_ip
        ]);

        echo json_encode([
            "status"  => "OK",
            "pass_no" => $pass_no
        ]);
    }
    public function actionGenerateCourtPassPartyInPerson()
    {
        $this->requireRole([10]);
        $this->requireAuth();

        // POST fields
        $cino      = $this->decodeField($_POST["cino"])      ?? '';
        $adv_type  = null; // FIX: PIP has NO advocate → must be NULL, NOT "P"
        $cldt      = $this->decodeField($_POST["cldt"])      ?? '';
        $cltype    = $this->decodeField($_POST["cltype"])    ?? '';
        $courtno   = intval($this->decodeField($_POST["courtno"]) ?? 0);
        $itemno    = $this->decodeField($_POST["itemno"]) ?? '';  // varchar in DB

        // PIP fixed values
        $party     = null; // you requested NULL
        $partyno   = 0;

        $passfor   = $this->decodeField($_POST["passfor"])   ?? 'P';
        $partynm   = $this->decodeField($_POST["partynm"])   ?? '';
        $partymob  = $this->decodeField($_POST["partymob"])  ?? '';
        $paddress  = $this->decodeField($_POST["paddress"])  ?? '';
        $user_ip   = $_SESSION["admin_user"]["username"];
        if ($partynm === "" || $partymob === "") {
            echo json_encode(["status" => "ERROR", "message" => "Party name and mobile are required."]);
            return;
        }
        // Validate mobile length
        if (!preg_match('/^[6-9][0-9]{9}$/', $partymob)) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid mobile number."]);
            return;
        }
        $userModel = new User();
        $exists = $userModel->checkPartyExists($partynm, $partymob);

        if (!$exists) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "Party not registered as Party-in-Person. Please register first."
            ]);
            return;
        }
        $passModel = new Pass();
        if ($passModel->isPipPassExists($cino, $partynm, $partymob, $courtno, $itemno, $cldt)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Pass already generated for this Party for the same court/item/date."
            ]);
            return;
        }

        // Encrypt mobile
        $partymob = $this->getEncryptValue($partymob);

        // Generate unique pass no
        $pass_no = $cltype . date("dmY", strtotime($cldt)) . $courtno . $itemno . date("His");

        $sql = "INSERT INTO gatepass_details
        (cino, causelist_dt, causelist_type, court_no, item_no, pass_no, adv_type, 
         paddress, party_no, party_name, party_type, party_mob_no, passfor, passtype, 
         entry_dt, adv_code, adv_enroll, user_ip)
        VALUES
        (:cino, :cldt, :cltype, :courtno, :itemno, :pass_no, :adv_type,
         :paddress, :partyno, :partynm, :party, :mob, :passfor, 3, NOW(),
         NULL, NULL, :user_ip)
        RETURNING pass_no";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cino"     => $cino,
            ":cldt"     => date("Y-m-d", strtotime($cldt)),
            ":cltype"   => $cltype,
            ":courtno"  => $courtno,
            ":itemno"   => $itemno,
            ":pass_no"  => $pass_no,
            ":adv_type" => null,     // FIXED HERE
            ":paddress" => $paddress,
            ":partyno"  => $partyno,
            ":partynm"  => $partynm,
            ":party"    => null,     // now NULL
            ":mob"      => $partymob,
            ":passfor"  => $passfor,
            ":user_ip"  => trim($user_ip)
        ]);

        echo json_encode([
            "status"  => "OK",
            "pass_no" => $pass_no
        ]);
    }


    public function actionMyPasses()
    {
        $this->requireRole([10]);  // Only officers

        // Officer username from session
        $username = $_SESSION["admin_user"]["username"];

        // Fetch passes created by this officer
        $sql = "SELECT *
            FROM gatepass_details
            WHERE user_ip = :username
            ORDER BY entry_dt DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":username" => $username]);

        $passes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Load view
        $this->render("pass/my_passes", ["passes" => $passes]);
    }
    public function mySectionPasses()
    {
        $this->requireAuth();
        $this->requireRole([10]);

        $username = $_SESSION['admin_user']['username'];

        $sql = "SELECT d.*, u.name 
            FROM gatepass_details_section d
            LEFT JOIN gatepass_users u ON u.enroll_num = d.enroll_no
            WHERE d.userip = :user
            ORDER BY d.entry_dt DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":user" => $username]);

        $passes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render("pass/my_section_passes", ["passes" => $passes]);
    }
    private function getCasePartyDetails($cino)
    {
        $sql = "
        SELECT 
            B.pet_name,
            B.res_name,
            B.reg_no,
            B.reg_year,
            C.type_name
        FROM civil_t AS B
        INNER JOIN case_type_t AS C ON B.regcase_type = C.case_type
        WHERE B.cino = :cino
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":cino" => $cino]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function actionDownloadPdf()
    {
        $this->requireRole([10]);
        if (ob_get_length()) ob_end_clean();

        $id = $_GET['id'] ?? null;
        if (!$id) exit("Invalid Pass");

        // Fetch pass data
        $stmt = $this->pdo->prepare("SELECT * FROM gatepass_details WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$p) exit("Pass not found");
        if ($p['passfor'] == 'L') {
            $adv = $this->getAdvocateDetails($p['adv_code']);
            $adv_name   = $adv['name'] ?? "-";
            $adv_enroll = $adv['enroll_num'] ?? "-";
            $address    = $p['paddress'] ?? "-";
            $case = $this->getCasePartyDetails($p['cino']);
            $pet_name = $case['pet_name'] ?? "-";
            $res_name = $case['res_name'] ?? "-";
            $case_details = "-";
            if (!empty($case['reg_no'])) {
                $case_details = $case['reg_no'] . "/" . $case['reg_year'] . " (" . $case['type_name'] . ")";
            }
            $valid = date("d/m/Y", strtotime($p['causelist_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));
            $qrText =
                "COURT PASS DETAILS FOR LITIGANT \n" .
                "Pass No: {$p['pass_no']}\n" .
                "CINO: {$p['cino']}\n" .
                "Court No: {$p['court_no']}\n" .
                "Item No: {$p['item_no']}\n" .
                "Name: {$p['party_name']}\n" .
                "Address: {$address}\n" .
                "Date of Hearing: {$valid}\n" .
                "Generated On: {$gen}";

            // INIT PDF
            $pdf = new CourtPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid, "RAJASTHAN HIGH COURT");
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();

            // QR Code
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);

            // BODY HTML
            $html = '
        <table border="1" cellspacing="0" cellpadding="5" width="100%">
            <tr><td colspan="4" align="center"><b>Pass for Litigant</b></td></tr>

            <tr>
                <td width="25%"><b>Case Details</b></td>
                <td width="25%">' . $case_details . '</td>
                <td width="25%"><b>Pass Number</b></td>
                <td width="25%">' . $p['pass_no'] . '</td>
            </tr>

    <tr>
        <td><b>Petitioner</b></td>
        <td>' . $pet_name . '</td>

        <td><b>Respondent</b></td>
        <td>' . $res_name . '</td>
    </tr>

     <tr>
        <td width="50%"><b>Pass recommended by</b></td>
        <td width="50%"> Ad. ' . htmlspecialchars($adv_name) . '</td>

    </tr>
</table>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <tr>
        <td width="75%">
            This entry pass is issued for  Mr./Ms./Mrs.
            <b>' . $p['party_name'] . ' R/O ' . $address . '</b>
            and valid for case hearing on <b>' . $valid . ' only. </b> Litigant must carry a valid Photo ID with this ePass.
        </td>

        <td width="25%" align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>
</table>
';

            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');

            $pdf->Output("PASS_{$p['pass_no']}.pdf", 'D');
        } else if ($p['passfor'] == 'P') {
            $address    = $p['paddress'] ?? "-";
            $case = $this->getCasePartyDetails($p['cino']);
            $pet_name = $case['pet_name'] ?? "-";
            $res_name = $case['res_name'] ?? "-";
            $case_details = "-";
            if (!empty($case['reg_no'])) {
                $case_details = $case['reg_no'] . "/" . $case['reg_year'] . " (" . $case['type_name'] . ")";
            }
            $valid = date("d/m/Y", strtotime($p['causelist_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));
            $qrText =
                "COURT PASS DETAILS FOR PARTY IN PERSON\n" .
                "Pass No: {$p['pass_no']}\n" .
                "CINO: {$p['cino']}\n" .
                "Court No: {$p['court_no']}\n" .
                "Item No: {$p['item_no']}\n" .
                "Name: {$p['party_name']}\n" .
                "Address: {$address}\n" .
                "Date of Hearing: {$valid}\n" .
                "Generated On: {$gen}";

            // INIT PDF
            $pdf = new CourtPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid, "RAJASTHAN HIGH COURT");
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();

            // QR Code
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);

            // BODY HTML
            $html = '
        <table border="1" cellspacing="0" cellpadding="5" width="100%">
            <tr><td colspan="4" align="center"><b>Pass for Party in Person</b></td></tr>

            <tr>
                <td width="25%"><b>Case Details</b></td>
                <td width="25%">' . $case_details . '</td>
                <td width="25%"><b>Pass Number</b></td>
                <td width="25%">' . $p['pass_no'] . '</td>
            </tr>

    <tr>
        <td><b>Petitioner</b></td>
        <td>' . $pet_name . '</td>

        <td><b>Respondent</b></td>
        <td>' . $res_name . '</td>
    </tr>
</table>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <tr>
      <td width="75%">
    This entry pass is issued for  Mr./Ms./Mrs. <b>' . $p['party_name'] . '</b>
    and is valid for item no. <b>' . $p['item_no'] . '</b> in court no.
    <b>' . $p['court_no'] . '</b>.
    This pass is valid for case hearing on <b>' . $valid . '</b> only.
    Party-in-Person must carry a valid Photo ID with this ePass.
</td>



        <td width="25%" align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>
</table>
';

            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');

            $pdf->Output("PASS_{$p['pass_no']}.pdf", 'D');
        } else {




            // Fetch advocate details
            $adv = $this->getAdvocateDetails($p['adv_code']);
            $adv_name   = $adv['name'] ?? "-";
            $adv_enroll = $adv['enroll_num'] ?? "-";
            $address    = $adv['address'] ?? "-";

            // Fetch case party details
            $case = $this->getCasePartyDetails($p['cino']);
            $pet_name = $case['pet_name'] ?? "-";
            $res_name = $case['res_name'] ?? "-";

            // Case details like REG no/year/type
            $case_details = "-";
            if (!empty($case['reg_no'])) {
                $case_details = $case['reg_no'] . "/" . $case['reg_year'] . " (" . $case['type_name'] . ")";
            }

            $valid = date("d/m/Y", strtotime($p['causelist_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));

            // QR text
            $qrText =
                "COURT PASS DETAILS\n" .
                "Pass No: {$p['pass_no']}\n" .
                "CINO: {$p['cino']}\n" .
                "Court No: {$p['court_no']}\n" .
                "Item No: {$p['item_no']}\n" .
                "Advocate: {$adv_name}\n" .
                "Enrollment No: {$adv_enroll}\n" .
                "Address: {$address}\n" .
                "Date of Hearing: {$valid}\n" .
                "Generated On: {$gen}";

            // INIT PDF
            $pdf = new CourtPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid, "RAJASTHAN HIGH COURT");
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();

            // QR Code
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);

            // BODY HTML
            $html = '
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr><td colspan="4" align="center"><b>Pass for Advocate</b></td></tr>

    <tr>
        <td width="25%"><b>Case Details</b></td>
        <td width="25%">' . $case_details . '</td>
        <td width="25%"><b>Pass Number</b></td>
        <td width="25%">' . $p['pass_no'] . '</td>
    </tr>

    <tr>
        <td><b>Petitioner</b></td>
        <td>' . $pet_name . '</td>

        <td><b>Respondent</b></td>
        <td>' . $res_name . '</td>
    </tr>
</table>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <tr>
        <td width="75%">
            This entry pass is issued for  Ad.
            <b>' . $adv_name . ' R/O ' . $address . '</b>
            and valid for case hearing on <b>' . $valid . '</b>.
        </td>

        <td width="25%" align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>
</table>
';

            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');

            $pdf->Output("PASS_{$p['pass_no']}.pdf", 'D');
        }
    }



    public function actionSaveAdvocateSection()
    {
        $this->requireAuth();
        $this->requireRole([10]);
        header("Content-Type: application/json");

        $enroll   = trim($this->decodeField($_POST['enroll']) ?? '');
        $sections = $this->decodeFieldJson($_POST['sections']) ?? [];
        $remarks  = $this->decodeFieldJson($_POST['purpose']) ?? [];
        $pass_dt = $this->decodeField($_POST['visit_date']) ?? date("Y-m-d");

        if ($enroll == "" || empty($sections)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Please fill all required fields."
            ]);
            return;
        }
        $adv = $this->getAdvocateDetailsByEnroll($enroll);

        if ($adv["enroll_num"] === null) {
            echo json_encode([
                "status"  => "ERROR",
                "code" => 404,
                "message" => "Advocate is not registered in the Gate Pass system. Please register first to continue."
            ]);
            return;
        }
        $purposeIds = implode(",", $sections);
        $remarkList = [];
        foreach ($sections as $sid) {
            $remarkList[] = [
                "purpose" => $sid,
                "remark"  => $remarks[$sid] ?? ""
            ];
        }
        $remarkJson = json_encode($remarkList);
        $passNo  = date("dmY") . date("His");
        $userid  = $_SESSION['admin_user']['id'];

        $model = new Pass();
        if ($model->advocateSectionPassExists($enroll, $pass_dt)) {
            echo json_encode([
                "status"  => "ERROR",
                "message" => "A section pass for this advocate is already generated for " .
                    date("d-m-Y", strtotime($pass_dt)) . "."
            ]);
            return;
        }
        $ok = $model->saveAdvocateSectionRaw([
            "pass_dt"     => $pass_dt,
            "userid"      => $userid,
            "userip"      => $_SESSION["admin_user"]["username"],
            "adv_cd"      => $adv["adv_code"] ?? null,
            "adv_enroll"  => $enroll,
            "pass_no"     => $passNo,

            "purpose_ids" => $purposeIds,
            "remarks"     => $remarkJson,

            "passfor"     => "S",
            "passtype"    => 3
        ]);


        if (!$ok) {
            echo json_encode(["status" => "ERROR", "message" => "Unable to save pass"]);
            return;
        }

        echo json_encode([
            "status"   => "OK",
            "message"  => "Section pass generated successfully.",
            "redirect" => "/HC-EPASS-MVC/public/index.php?r=pass/viewSection&id=" . $ok
        ]);
    }
    public function actionSaveLitigantSection()
    {
        $this->requireAuth();
        $this->requireRole([10]);
        header("Content-Type: application/json");
        $enroll   = trim($this->decodeField($_POST['enroll']) ?? '');
        $sections = $this->decodeFieldJson($_POST['sections']) ?? [];
        $remarks  =  $this->decodeFieldJson($_POST['purpose']) ?? [];
        $pass_dt = $this->decodeField($_POST['visit_date']) ?? date("Y-m-d");
        $litigantname = $this->decodeField($_POST['lit_name']) ?? '';
        $litigantmobile = $this->decodeField($_POST['lit_mobile']) ?? '';
        $litigant_address = $this->decodeField($_POST['lit_address']) ?? '';

        $litigantmobile = $this->getEncryptValue($litigantmobile);
        if ($enroll == "" || empty($sections)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Please fill all required fields."
            ]);
            return;
        }

        // Fetch advocate details
        $adv = $this->getAdvocateDetailsByEnroll($enroll);

        if ($adv["enroll_num"] === null) {
            echo json_encode([
                "status"  => "ERROR",
                "code" => 404,
                "message" => "Advocate is not registered in the Gate Pass system. Please register first to continue."
            ]);
            return;
        }


        $model = new Pass();

        // DUPLICATE CHECK (before saving)
        if ($model->checkLitigantSectionDuplicate($this->decodeField($_POST["lit_mobile"]), $pass_dt, $adv["adv_code"])) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "A section pass already exists today for this litigant recommended by this advocate."
            ]);
            return;
        }
        $purposeIds = implode(",", $sections);
        $remarkList = [];

        foreach ($sections as $sid) {
            $remarkList[] = [
                "purpose" => $sid,
                "remark"  => $remarks[$sid] ?? ""
            ];
        }
        $remarkJson = json_encode($remarkList);
        $passNo  = date("dmY") . date("His");
        $userid  = $_SESSION['admin_user']['id'];
        $ok = $model->saveLitigantSectionRaw([
            "pass_dt"     => $pass_dt,
            "userid"      => $userid,
            "userip"      => $_SESSION["admin_user"]["username"],
            "adv_cd"      => $adv["adv_code"] ?? null,
            "adv_enroll"  => $enroll,
            "pass_no"     => $passNo,
            "purpose_ids" => $purposeIds,
            "remarks"     => $remarkJson,
            "passfor"     => "LS",
            "passtype"    => 3,
            "litigantname" => $litigantname,
            "litigantmobile" => $litigantmobile,
            "litigant_address" => $litigant_address
        ]);


        if (!$ok) {
            echo json_encode(["status" => "ERROR", "message" => "Unable to save pass"]);
            return;
        }

        echo json_encode([
            "status"   => "OK",
            "message"  => "Section pass generated successfully.",
            "redirect" => "/HC-EPASS-MVC/public/index.php?r=pass/viewSectionLitigant&id=" . $ok
        ]);
    }

    public function actionSavePIPSection()
    {
        $this->requireAuth();
        $this->requireRole([10]);
        header("Content-Type: application/json");

        $sections = $this->decodeFieldJson($_POST['sections']) ?? [];
        $remarks  = $this->decodeFieldJson($_POST['purpose']) ?? [];
        $pass_dt = $this->decodeField($_POST['visit_date']) ?? date("Y-m-d");
        $litigantname = $this->decodeField($_POST['pip_name']) ?? '';
        $litigantmobile = $this->decodeField($_POST['pip_mobile']) ?? '';
        $litigant_address = $this->decodeField($_POST['pip_address']) ?? '';
        $litigantmobile = $this->getEncryptValue($litigantmobile);
        if (empty($sections)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "Please fill all required fields."
            ]);
            return;
        }
        $purposeIds = implode(",", $sections);
        $remarkList = [];

        foreach ($sections as $sid) {
            $remarkList[] = [
                "purpose" => $sid,
                "remark"  => $remarks[$sid] ?? ""
            ];
        }
        $remarkJson = json_encode($remarkList);
        $passNo  = date("dmY") . date("His");
        $userid  = $_SESSION['admin_user']['id'];

        $model = new Pass();
        if ($this->decodeField($_POST['pip_name']) === "" || $this->decodeField($_POST['pip_mobile']) === "") {
            echo json_encode(["status" => "ERROR", "message" => "Party name and mobile are required."]);
            return;
        }
        // Validate mobile length
        if (!preg_match('/^[6-9][0-9]{9}$/',  $this->decodeField($_POST['pip_mobile']))) {
            echo json_encode(["status" => "ERROR", "message" => "Invalid mobile number."]);
            return;
        }
        $userModel = new User();
        $exists = $userModel->checkPartyExists($this->decodeField($_POST['pip_name']), $this->decodeField($_POST['pip_mobile']));

        if (!$exists) {

            echo json_encode([
                "status"  => "ERROR",
                "message" => "Party not registered as Party-in-Person. Please register first."
            ]);
            return;
        }
        $passModel = new Pass();
        if ($passModel->checkPIPSectionDuplicate($this->decodeField($_POST["pip_mobile"]), $pass_dt)) {
            echo json_encode([
                "status" => "ERROR",
                "message" => "A section pass already exists <b>" . $pass_dt . "</b> for this Party-in-Person."
            ]);
            return;
        }





        $ok = $model->saveLitigantSectionRaw([
            "pass_dt"     => $pass_dt,
            "userid"      => $userid,
            "userip"      => $_SESSION["admin_user"]["username"],
            "adv_cd"      =>  $userid,
            "adv_enroll"  => $userid,
            "pass_no"     => $passNo,
            "purpose_ids" => $purposeIds,
            "remarks"     => $remarkJson,
            "passfor"     => "PS",
            "passtype"    => 3,
            "litigantname" => $litigantname,
            "litigantmobile" => $litigantmobile,
            "litigant_address" => $litigant_address
        ]);


        if (!$ok) {
            echo json_encode(["status" => "ERROR", "message" => "Unable to save pass"]);
            return;
        }

        echo json_encode([
            "status"   => "OK",
            "message"  => "Section pass generated successfully.",
            "redirect" => "/HC-EPASS-MVC/public/index.php?r=pass/viewSectionParty&id=" . $ok
        ]);
    }
    public function viewSection()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Invalid pass ID");

        $model = new Pass();
        $pass = $model->getSectionPassById($id);

        if (!$pass) die("Pass not found");
        $pass = $this->passModel->getSectionPassById($id);

        if ($pass && !empty($pass['purposermks'])) {

            $decoded = json_decode($pass['purposermks'], true);
            $pass['purpose_items'] = [];

            if (is_array($decoded)) {
                foreach ($decoded as $r) {
                    $sectionId = $r['purpose'] ?? null;

                    $pass['purpose_items'][] = [
                        'section_id'   => $sectionId,
                        'section_name' => $sectionId ? $this->getSectionNameById($sectionId) : '',
                        'remark'       => $r['remark'] ?? ''
                    ];
                }
            }
        }

        /* render based on pass type */
        switch (trim($pass['passfor'])) {
            case 'S':
                $this->render("pass/view_section", ["pass" => $pass]);
                break;

            case 'LS':
                $this->render('pass/view_section_litigant', ['pass' => $pass]);
                break;

            case 'PS':
                $this->render('pass/view_section_party', ['pass' => $pass]);
                break;

            default:
                die('Invalid section pass type');
        }
        //
    }
    private function getSectionNameById($id)
    {
        $stmt = $this->pdo->prepare("SELECT purpose FROM gatepass_purpose_visit WHERE id = :id");
        $stmt->execute([":id" => $id]);
        return $stmt->fetchColumn() ?: "";
    }

    public function printSection()
    {
        $this->requireRole([10]);
        if (ob_get_length()) ob_end_clean();

        $id = $_GET['id'] ?? null;
        if (!$id) exit("Invalid Pass");

        // FETCH PASS ROW
        $stmt = $this->pdo->prepare("SELECT * FROM gatepass_details_section WHERE id = :id");
        $stmt->execute([":id" => $id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$p) exit("Pass not found");

        if (trim($p['passfor']) == 'LS') {
            // FETCH ADVOCATE DETAILS
            $adv = $this->getAdvocateDetailsByEnroll($p['enroll_no']);
            $advName   = $adv['name'] ?? "N/A";
            $address   = $p['litigant_address'] ?? "N/A";
            $litigantname = trim($p['litigantname']) ?? "N/A";

            // DECODE PURPOSE JSON
            $purposeData = json_decode($p['purposermks'], true);

            $purposeLines = [];
            $remarkLines  = [];

            if (is_array($purposeData)) {
                foreach ($purposeData as $item) {

                    $sectionId   = $item['purpose'];
                    $sectionName = $this->getSectionNameById($sectionId);
                    $remarkText  = $item['remark'] ?? "";

                    $purposeLines[] = $sectionName;
                    $remarkLines[]  = $remarkText;
                }
            }
            $purposeHTML = implode("<br>", $purposeLines);
            $remarksHTML = implode("<br>", $remarkLines);
            $valid = date("d/m/Y", strtotime($p['pass_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));

            $qrText =
                "SECTION PASS DETAILS FOR LITIGANT\n" .
                "Pass No: {$p['pass_no']}\n" .
                "Litigant: {$litigantname}\n" .
                "Visit Date: {$valid}\n" .
                "Generated On: {$gen}\n";
            $pdf = new SectionPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid);
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);
            $html = '
            <table border="1" cellspacing="0" cellpadding="5" width="100%">

                <tr><td colspan="4" align="center"><b>Pass for Litigant</b></td></tr>

                <tr>
                    <td width="25%"><b>Date of Visit</b></td>
                    <td width="25%">' . $valid . '</td>
                    <td width="25%"><b>Pass Number</b></td>
                    <td width="25%">' . $p['pass_no'] . '</td>
                </tr>

    

                <tr>
                    <td width="25%"><b>Purpose of Visit</b></td>
                    <td>' . $purposeHTML . '</td>
                    <td width="25%"><b>Remarks</b></td>
                    <td>' . $remarksHTML . '</td>
                </tr>
            <tr>
            <td width="50%"><b>Pass recommended by</b></td>
                <td width="50%"> Ad. ' . $advName . '</td>
            </tr>

</table>

<br>
';

            // ================================
            // NEW EPASS DETAILS TABLE (HC STYLE)
            // ================================
            $html .= '
<table border="1" cellspacing="0" cellpadding="5" width="100%">

    <!-- Blue Header -->
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <!-- Pink Row -->
   


    <!-- Main Body -->
    <tr>
        <td colspan="3">
            This entry pass is issued for Mr./Ms./Mrs.
            <b>' . $litigantname . ', R/O ' . $address . '</b>,  and is valid for 
            Ancillary Purposes other than court hearing on 
            <b>' . $valid . '</b> only.Litigant must carry a valid Photo ID with this ePass.
        </td>
        <td align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>

</table>
';

            // Render
            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output("SECTION_PASS_{$p['pass_no']}.pdf", 'D');
        } else if (trim($p['passfor']) == 'PS') {
            // FETCH ADVOCATE DETAILS

            $address   = $p['litigant_address'] ?? "N/A";
            $litigantname = trim($p['litigantname']) ?? "N/A";

            // DECODE PURPOSE JSON
            $purposeData = json_decode($p['purposermks'], true);

            $purposeLines = [];
            $remarkLines  = [];

            if (is_array($purposeData)) {
                foreach ($purposeData as $item) {

                    $sectionId   = $item['purpose'];
                    $sectionName = $this->getSectionNameById($sectionId);
                    $remarkText  = $item['remark'] ?? "";

                    $purposeLines[] = $sectionName;
                    $remarkLines[]  = $remarkText;
                }
            }
            $purposeHTML = implode("<br>", $purposeLines);
            $remarksHTML = implode("<br>", $remarkLines);
            $valid = date("d/m/Y", strtotime($p['pass_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));

            $qrText =
                "SECTION PASS DETAILS FOR PARTY IN PERSON\n" .
                "Pass No: {$p['pass_no']}\n" .
                "PARTY: {$litigantname}\n" .
                "Visit Date: {$valid}\n" .
                "Generated On: {$gen}\n";
            $pdf = new SectionPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid);
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);
            $html = '
            <table border="1" cellspacing="0" cellpadding="5" width="100%">

                <tr><td colspan="4" align="center"><b>Pass for Party in Person</b></td></tr>

                <tr>
                    <td width="25%"><b>Date of Visit</b></td>
                    <td width="25%">' . $valid . '</td>
                    <td width="25%"><b>Pass Number</b></td>
                    <td width="25%">' . $p['pass_no'] . '</td>
                </tr>

    

                <tr>
                    <td width="25%"><b>Purpose of Visit</b></td>
                    <td>' . $purposeHTML . '</td>
                    <td width="25%"><b>Remarks</b></td>
                    <td>' . $remarksHTML . '</td>
                </tr>
           

</table>

<br>
';

            // ================================
            // NEW EPASS DETAILS TABLE (HC STYLE)
            // ================================
            $html .= '
<table border="1" cellspacing="0" cellpadding="5" width="100%">

    <!-- Blue Header -->
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <!-- Pink Row -->
   


    <!-- Main Body -->
    <tr>
        <td colspan="3">
            This entry pass is issued for Mr./Ms./Mrs.
            <b>' . $litigantname . ', R/O ' . $address . '</b>,  and is valid for 
            Ancillary Purposes other than court hearing on 
            <b>' . $valid . '</b> only.Party in Person must carry a valid Photo ID with
this ePass.
        </td>
        <td align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>

</table>
';

            // Render
            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output("SECTION_PASS_{$p['pass_no']}.pdf", 'D');
        } else {
            // FETCH ADVOCATE DETAILS
            $adv = $this->getAdvocateDetailsByEnroll($p['enroll_no']);
            $advName   = $adv['name'] ?? "N/A";
            $advEnroll = $p['enroll_no'];
            $address   = $adv['address'] ?? "N/A";

            // DECODE PURPOSE JSON
            $purposeData = json_decode($p['purposermks'], true);

            $purposeLines = [];
            $remarkLines  = [];

            if (is_array($purposeData)) {
                foreach ($purposeData as $item) {

                    $sectionId   = $item['purpose'];
                    $sectionName = $this->getSectionNameById($sectionId);
                    $remarkText  = $item['remark'] ?? "";

                    $purposeLines[] = $sectionName;
                    $remarkLines[]  = $remarkText;
                }
            }

            // MULTI-LINE PURPOSE & REMARKS
            $purposeHTML = implode("<br>", $purposeLines);
            $remarksHTML = implode("<br>", $remarkLines);

            // FORMAT DATES
            // FORMAT DATES
            $valid = date("d/m/Y", strtotime($p['pass_dt']));
            $gen   = date("d/m/Y H:i:s", strtotime($p['entry_dt']));

            // Build purpose list (ensure variable exists)
            $purposeListStr = "";
            if (!empty($purposeLines)) {
                $purposeListStr = implode(", ", $purposeLines);
            }

            // SAFE QR TEXT (TCPDF compatible)
            $qrText =
                "SECTION PASS DETAILS\n" .
                "Pass No: {$p['pass_no']}\n" .
                "Advocate: {$advName}\n" .
                "Enrollment No: {$advEnroll}\n" .
                "Address: {$address}\n" .
                "Visit Date: {$valid}\n" .
                "Generated On: {$gen}\n";


            // INIT PDF
            $pdf = new SectionPDF('P', 'mm', 'A4');
            $pdf->setHeaderValues($valid);
            $pdf->SetMargins(9, 33, 9);
            $pdf->AddPage();

            // QR CODE
            $pdf->write2DBarcode($qrText, 'QRCODE,H', 178, 8, 22, 22);

            // FIRST TABLE (UNCHANGED)
            $html = '
<table border="1" cellspacing="0" cellpadding="5" width="100%">

    <tr><td colspan="4" align="center"><b>Pass for Advocate</b></td></tr>

    <tr>
        <td width="25%"><b>Date of Visit</b></td>
        <td width="25%">' . $valid . '</td>
        <td width="25%"><b>Pass Number</b></td>
        <td width="25%">' . $p['pass_no'] . '</td>
    </tr>

    <tr>
        <td><b>Advocate</b></td>
        <td>' . $advName . '</td>
        <td><b>Enrollment No</b></td>
        <td>' . $advEnroll . '</td>
    </tr>

    <tr>
        <td width="25%"><b>Purpose of Visit</b></td>
        <td>' . $purposeHTML . '</td>
        <td width="25%"><b>Remarks</b></td>
        <td>' . $remarksHTML . '</td>
    </tr>

</table>

<br>
';

            // ================================
            // NEW EPASS DETAILS TABLE (HC STYLE)
            // ================================
            $html .= '
<table border="1" cellspacing="0" cellpadding="5" width="100%">

    <!-- Blue Header -->
    <tr style="background-color:#c8d9f1;">
        <td colspan="4" align="center"><b>ePass Details</b></td>
    </tr>

    <!-- Pink Row -->
   


    <!-- Main Body -->
    <tr>
        <td colspan="3">
            This entry pass is issued for Ad.
            <b>' . $advName . ', R/O ' . $address . '</b>, Advocate and is valid for 
            Ancillary Purposes other than court hearing on 
            <b>' . $valid . '</b> only.
        </td>
        <td align="center">
            <b>Pass Generation Date</b><br>' . $gen . '
        </td>
    </tr>

</table>
';

            // Render
            $pdf->SetFont('times', '', 11);
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output("SECTION_PASS_{$p['pass_no']}.pdf", 'D');
        }
    }
}





class CourtPDF extends TCPDF
{
    public $validDate;
    public $passHeaderText;

    public function setHeaderValues($validDate, $passHeaderText)
    {
        $this->validDate = $validDate;
        $this->passHeaderText = $passHeaderText;
    }

    public function Header()
    {
        // OUTER BORDER BOX
        $this->Rect(6, 5, 200, 287);

        // TOP LINE
        $this->Line(6, 30, 206, 30);

        // LEFT LOGO BOX LINE
        $this->Line(35, 5, 35, 30);

        // RIGHT QR BOX LINE
        $this->Line(175, 5, 175, 30);

        // CORRECT LOGO PATH
        $logoPath = __DIR__ . "/../../public/assets/images/hc_logo.png";

        if (file_exists($logoPath)) {
            $this->Image($logoPath, 11, 8, 18, 0, 'PNG');
        } else {
            $this->Text(10, 10, 'LOGO NOT FOUND');
        }


        if ($_SESSION['admin_user']['establishment'] == 'B') {
            $this->SetFont('times', 'B', 15);
            $this->Text(55, 10, 'RAJASTHAN HIGH COURT BENCH JAIPUR');
        } else if ($_SESSION['admin_user']['establishment'] == 'P') {
            $this->SetFont('times', 'B', 15);
            $this->Text(55, 10, 'RAJASTHAN HIGH COURT JODHPUR');
        }





        // PASS DETAILS
        $this->SetFont('times', 'B', 12);
        $this->Text(88, 17, 'ePass Details');

        // VALID DATE
        $this->SetFont('times', '', 11);
        $this->Text(80, 23, '(Pass valid for: ' . $this->validDate . ' only)');
    }
}

class SectionPDF extends TCPDF
{
    public $validDate;

    public function setHeaderValues($validDate)
    {
        $this->validDate = $validDate;
    }

    public function Header()
    {
        // OUTER BORDER BOX
        $this->Rect(6, 5, 200, 287);

        // TOP LINE
        $this->Line(6, 30, 206, 30);

        // LEFT LOGO BOX LINE
        $this->Line(35, 5, 35, 30);

        // RIGHT QR BOX LINE
        $this->Line(175, 5, 175, 30);

        // LOGO
        $logoPath = __DIR__ . "/../../public/assets/images/hc_logo.png";
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 11, 8, 18, 0, 'PNG');
        }

        // COURT NAME
        if ($_SESSION['admin_user']['establishment'] == 'B') {
            $this->SetFont('times', 'B', 15);
            $this->Text(55, 10, 'RAJASTHAN HIGH COURT BENCH JAIPUR');
        } else if ($_SESSION['admin_user']['establishment'] == 'P') {
            $this->SetFont('times', 'B', 15);
            $this->Text(55, 10, 'RAJASTHAN HIGH COURT JODHPUR');
        }
        // $this->SetFont('times', 'B', 15);
        // $this->Text(55, 10, 'RAJASTHAN HIGH COURT BENCH JAIPUR');

        // EPASS DETAILS CENTER
        $this->SetFont('times', 'B', 12);
        $this->Text(88, 17, 'ePass Details');

        // VALID DATE
        $this->SetFont('times', '', 11);
        $this->Text(80, 23, '(Pass valid for: ' . $this->validDate . ' only)');
    }
}
