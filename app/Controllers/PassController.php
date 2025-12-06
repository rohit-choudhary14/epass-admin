<?php

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
        $this->requireRole([2]);

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
        $this->requireRole([2]);

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

        view('pass/view', ['p' => $p]);
    }
    public function ajaxList()
    {
        $this->requireAuth();
        $this->requireRole([2]);

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
        $this->requireRole([2]);

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
        if (in_array($type, ['advocate', 'sr_advocate', 'litigant', 'vendor'])) {
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
        $validTypes = ['advocate', 'sr_advocate'];

        // Allowed goto modes
        $validGoto = ['court', 'section', 'both'];

        // Validate type
        if (!in_array($type, $validTypes)) {
            die("<h2 style='color:red;text-align:center'>Invalid Pass Type</h2>");
        }

        // Validate goto
        if (!in_array($goto, $validGoto)) {
            die("<h2 style='color:red;text-align:center'>Invalid Destination Option</h2>");
        }

        // ============================
        // ADVOCATE -> COURT
        // ============================
        if ($type === 'advocate' && $goto === 'court') {
            $this->render("pass/generate_court_search", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }

        // ============================
        // SENIOR ADVOCATE -> COURT
        // ============================
        if ($type === 'sr_advocate' && $goto === 'court') {
            $this->render("pass/generate_court_search", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }

        // ============================
        // ADVOCATE -> SECTION
        // ============================
        if ($type === 'advocate' && $goto === 'section') {
            $this->render("pass/generate_form_advocate", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }

        // ============================
        // SENIOR ADVOCATE -> SECTION
        // ============================
        if ($type === 'sr_advocate' && $goto === 'section') {
            $this->render("pass/generate_form_advocate", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }

        // ============================
        // BOTH → UNIVERSAL ADVOCATE FORM
        // ============================
        if ($goto === 'both') {
            $this->render("pass/generate_form_advocate", [
                "type" => $type,
                "goto" => $goto
            ]);
            return;
        }
    }




    public function actionSaveAdvocate()
    {
        $model = new PassModel();

        $data = [
            "cnr"        => $_POST['cnr'],
            "case_type"  => $_POST['case_type'],
            "case_no"    => $_POST['case_no'],
            "year"       => $_POST['year'],
            "adv_name"   => $_POST['adv_name'],
            "adv_enroll" => $_POST['adv_enroll'],
            "mobile"     => $_POST['mobile'],
            "purpose"    => $_POST['purpose'],
            "passfor"    => "ADV",
            "created_by" => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveAdvocatePass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }
    public function actionSaveSrAdvocate()
    {
        $model = new PassModel();

        $data = [
            "cnr"        => $_POST['cnr'],
            "case_type"  => $_POST['case_type'],
            "case_no"    => $_POST['case_no'],
            "year"       => $_POST['year'],
            "adv_name"   => $_POST['adv_name'],
            "adv_enroll" => $_POST['adv_enroll'],
            "mobile"     => $_POST['mobile'],
            "purpose"    => $_POST['purpose'],
            "passfor"    => "SRADV",
            "created_by" => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveSrAdvocatePass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }

    public function actionSaveLitigant()
    {

        $model = new PassModel();

        $data = [
            "lit_name"   => $_POST['lit_name'],
            "mobile"     => $_POST['mobile'],
            "purpose"    => $_POST['purpose'],
            "cnr"        => $_POST['cnr'],
            "case_type"  => $_POST['case_type'],
            "case_no"    => $_POST['case_no'],
            "year"       => $_POST['year'],
            "court_no"   => $_POST['court_no'],
            "item_no"    => $_POST['item_no'],
            "passfor"    => "LIT",
            "created_by" => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveLitigantPass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }
    public function actionSaveCourt()
    {

        $model = new PassModel();

        $data = [
            "cnr"          => $_POST['cnr'],
            "case_type"    => $_POST['case_type'],
            "case_no"      => $_POST['case_no'],
            "year"         => $_POST['year'],
            "person_name"  => $_POST['person_name'],
            "mobile"       => $_POST['mobile'],
            "court_no"     => $_POST['court_no'],
            "item_no"      => $_POST['item_no'],
            "hearing_date" => $_POST['hearing_date'],
            "purpose"      => $_POST['purpose'],
            "passfor"      => "COURT",
            "created_by"   => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveCourtPass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }


    public function actionSaveSection()
    {

        $model = new PassModel();

        $data = [
            "person_name" => $_POST['person_name'],
            "mobile"      => $_POST['mobile'],
            "section"     => $_POST['section'],
            "visit_date"  => $_POST['visit_date'],
            "purpose"     => $_POST['purpose'],
            "passfor"     => "SECTION",
            "created_by"  => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveSectionPass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }
    public function actionSaveVendor()
    {

        $model = new PassModel();

        $data = [
            "vendor_name" => $_POST['vendor_name'],
            "mobile"      => $_POST['mobile'],
            "company"     => $_POST['company'],
            "purpose"     => $_POST['purpose'],
            "valid_upto"  => $_POST['valid_upto'],
            "id_proof"    => $_POST['id_proof'],
            "passfor"     => "VENDOR",
            "created_by"  => $_SESSION['admin_user']['id']
        ];

        $passId = $model->saveVendorPass($data);

        header("Location: /HC-EPASS-MVC/public/index.php?r=pass/view&id=" . $passId);
        exit;
    }
    public function actionView()
    {
        $id = $_GET['id'];
        $model = new PassModel();
        $pass = $model->getPassById($id);

        $this->render("pass/view", ["pass" => $pass]);
    }
    public function actionPrint()
    {
        $id = $_GET['id'];
        $model = new PassModel();
        $pass = $model->getPassById($id);

        include __DIR__ . '/../views/pass/print.php';
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
        $this->requireRole([10]); // officer only

        require_once __DIR__ . '/../Models/Court.php';
        $court = new Court();

        $caseTypes = $court->getCaseTypes();

        // Load the view
        $this->render("pass/generate_search_court", [
            "caseTypes" => $caseTypes
        ]);
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
                address
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
                "address"    => null
            ];
        }

        return [
            "mobile"      => $this->getEncryptValue($row["contact_num"]),
            "enroll_num"  => $row["enroll_num"],
            "adv_code"    => $row["adv_code"],
            "address"    => $row["address"]
        ];
    }

    public function actionGenerateCourtPass()
    {
        $this->requireRole([10]);

        $cino      = $_POST["cino"];
        $adv_type  = $_POST["adv_type"];
        $cldt      = $_POST["cldt"];
        $cltype    = $_POST["cltype"];
        $courtno   = $_POST["courtno"];
        $itemno    = $_POST["itemno"];
        // $paddress  = $_POST["paddress"];
        $party     = $_POST["party"];
        $passfor   = $_POST["passfor"];
        $partyno   = $_POST["partyno"];
        $partynm   = $_POST["partynm"];
        $partymob  = $_POST["partymob"];
        $adv_code = $_POST["adv_code"];
       
        $pass_no = $cltype . date("dmY", strtotime($cldt)) . $courtno . $itemno . date("His");

        $advDetails = $this->getAdvocateDetails($adv_code);
        $paddress = $advDetails['address'];
        $adv_enroll = $advDetails['enroll_num'];
       
      
      $sql = "INSERT INTO gatepass_details
            (cino, causelist_dt, causelist_type, court_no, item_no, pass_no, adv_type, paddress, party_no, party_name, party_type, party_mob_no, passfor, passtype, entry_dt,adv_code,adv_enroll)
            VALUES
            (:cino, :cldt, :cltype, :courtno, :itemno, :pass_no, :adv_type, :paddress, :partyno, :partynm, :party, :mob, :passfor, 2, NOW(),:adv_code,:adv_enroll)
            RETURNING pass_no";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":cino"     => $cino,
            ":cldt"     => date("Y-m-d", strtotime($cldt)),
            ":cltype"   => $cltype,
            ":courtno"  => $courtno,
            ":itemno"   => $itemno,
            ":pass_no"  => $pass_no,
            ":adv_type" => $adv_type,
            ":paddress" => $paddress,
            ":partyno"  => $partyno,
            ":partynm"  => $partynm,
            ":party"    => $party,
            ":mob"      => $partymob,
            ":passfor"  => $passfor,
            ":adv_code" => $adv_code,
            ":adv_enroll" => $adv_enroll
        ]);

        echo json_encode([
            "status" => "OK",
            "pass_no" => $pass_no
        ]);
    }
}
