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

    fputcsv($out, ['ID','Pass No','Date','For','Adv Enroll','Court','Item','CINO']);

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
        echo json_encode(['ok'=>false]);
        exit;
    }

    $id = (int) $_POST['id'];
    $passModel = new Pass();

    $ok = $passModel->revoke($id, $_SESSION['admin_user']['id']);

    echo json_encode(['ok' => $ok]);
    exit;
}

}
