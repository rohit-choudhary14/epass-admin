<?php
// app/Controllers/DashboardController.php

class DashboardController extends BaseController
{
    protected $passModel;

    public function __construct()
    {
        parent::__construct();
        $this->passModel = new Pass();
    }

    /**
     * Renders the dashboard view. Accepts optional GET filters:
     *  from, to, passfor (S/L/ALL), adv (adv_enroll), cino, pass_no, page, perPage
     */
    public function index()
    {

        $this->requireAuth();
        $this->requireRole([20]);

        // filters from GET
        $from = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : null;
        $to   = isset($_GET['to']) && $_GET['to'] !== '' ? $_GET['to'] : null;
        $passfor = isset($_GET['passfor']) && $_GET['passfor'] !== '' ? $_GET['passfor'] : null;
        $adv = isset($_GET['adv']) ? trim($_GET['adv']) : '';
        $cino = isset($_GET['cino']) ? trim($_GET['cino']) : '';
        $pass_no = isset($_GET['pass_no']) ? trim($_GET['pass_no']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 25;

        // Stats
        $today = date('Y-m-d');
        $stats = [
            'total'  => $this->passModel->countPasses(null, null),
            'today'  => $this->passModel->countPasses($today, $today),
            'week'   => $this->passModel->countPasses(date('Y-m-d', strtotime('-6 days')), $today),
            'month'  => $this->passModel->countPasses(date('Y-m-01'), $today),
            'section_total' => $this->passModel->countSectionPasses(null, null),
            'section_today' => $this->passModel->countSectionPasses($today, $today),
        ];

        // Chart and detail data (initial server-side load)
        $chart30 = $this->passModel->passesLastNDays(30, $from, $to, $passfor, $adv);
        $byType  = $this->passModel->passesByType($from, $to, $adv);
        $topAdv  = $this->passModel->topAdvocates(10, $from, $to);

        // recent / paginated table based on filters
        $rows = $this->passModel->filteredList([
            'from' => $from,
            'to' => $to,
            'passfor' => $passfor,
            'adv' => $adv,
            'cino' => $cino,
            'pass_no' => $pass_no,
            'page' => $page,
            'perPage' => $perPage
        ]);

        // Calculate total pages
        $totalRows  = $rows['total'];
        $totalPages = ceil($totalRows / $perPage);
        // pass vars to view
        view('dashboard/index', [
            'stats' => $stats,
            'chart30' => $chart30,
            'byType' => $byType,
            'topAdv' => $topAdv,
            'rows' => $rows['rows'],
            'totalRows' => $rows['total'],
            'page' => $rows['page'],
            'perPage' => $rows['perPage'],
            
            'filters' => ['from' => $from, 'to' => $to, 'passfor' => $passfor, 'adv' => $adv, 'cino' => $cino, 'pass_no' => $pass_no]
        ]);
    }

    /**
     * JSON endpoint for charts (optional AJAX usage)
     * ?r=dashboard/ajaxData&what=chart30
     */
    public function ajaxData()
    {
        $this->requireAuth();
        $what = isset($_GET['what']) ? $_GET['what'] : 'chart30';

        $from = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : null;
        $to   = isset($_GET['to']) && $_GET['to'] !== '' ? $_GET['to'] : null;
        $passfor = isset($_GET['passfor']) && $_GET['passfor'] !== '' ? $_GET['passfor'] : null;
        $adv = isset($_GET['adv']) ? trim($_GET['adv']) : '';

        header('Content-Type: application/json; charset=utf-8');

        if ($what === 'chart30') {
            echo json_encode($this->passModel->passesLastNDays(30, $from, $to, $passfor, $adv));
            exit;
        } elseif ($what === 'byType') {
            echo json_encode($this->passModel->passesByType($from, $to, $adv));
            exit;
        } elseif ($what === 'topAdv') {
            echo json_encode($this->passModel->topAdvocates(10, $from, $to));
            exit;
        } else {
            echo json_encode(['error' => 'unknown']);
            exit;
        }
    }

    /**
     * Export filtered passes to CSV.
     * Uses same filters as index view.
     * Called via: ?r=dashboard/export&from=...&to=...&adv=...
     */
    public function export()
    {
        $this->requireAuth();
        $from = isset($_GET['from']) && $_GET['from'] !== '' ? $_GET['from'] : null;
        $to   = isset($_GET['to']) && $_GET['to'] !== '' ? $_GET['to'] : null;
        $passfor = isset($_GET['passfor']) && $_GET['passfor'] !== '' ? $_GET['passfor'] : null;
        $adv = isset($_GET['adv']) ? trim($_GET['adv']) : '';
        $cino = isset($_GET['cino']) ? trim($_GET['cino']) : '';
        $pass_no = isset($_GET['pass_no']) ? trim($_GET['pass_no']) : '';

        $data = $this->passModel->getFilteredRows([
            'from' => $from,
            'to' => $to,
            'passfor' => $passfor,
            'adv' => $adv,
            'cino' => $cino,
            'pass_no' => $pass_no,
            'page' => 1,
            'perPage' => 100000
        ]);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=passes_export_' . date('Ymd_His') . '.csv');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Pass No', 'Entry Date', 'Pass For', 'Pass Type', 'CINO', 'Adv Enroll', 'Court', 'Item', 'Address']);
        foreach ($data['rows'] as $r) {
            fputcsv($out, [
                $r['id'],
                $r['pass_no'],
                isset($r['entry_dt']) ? $r['entry_dt'] : (isset($r['entry_dt_str']) ? $r['entry_dt_str'] : ''),
                $r['passfor'],
                $r['passtype'],
                isset($r['cino']) ? $r['cino'] : '',
                isset($r['adv_enroll']) ? $r['adv_enroll'] : '',
                isset($r['court_no']) ? $r['court_no'] : '',
                isset($r['item_no']) ? $r['item_no'] : '',
                isset($r['paddress']) ? $r['paddress'] : '',
            ]);
        }
        fclose($out);
        exit;
    }
}
