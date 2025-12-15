<?php
require_once __DIR__ . '/../bootstrap.php';

// simple router: ?r=controller/action
$route = isset($_GET['r']) ? $_GET['r'] : 'auth/loginForm';
$parts = explode('/', $route);
$controller = $parts[0];
$action = isset($parts[1]) ? $parts[1] : 'index';

// ROUTER
switch ($controller) {

    /* ===============================
       AUTH CONTROLLER
       =============================== */
    case 'auth':
        $c = new AuthController();

        if ($action === 'loginPost' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->loginPost();
        elseif ($action === 'registerPost' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->registerPost();
        elseif ($action === 'registerForm') $c->registerForm();
        elseif ($action === 'registerOfficerForm') $c->registerOfficerForm();
        elseif ($action === 'registerOfficerPost' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->registerOfficerPost();
        elseif ($action === 'userList') $c->userList();
        elseif ($action === 'logout') $c->logout();
        else $c->loginForm();
        break;


    /* ===============================
       ADMIN DASHBOARD
       =============================== */
    case 'dashboard':
        $c = new DashboardController();
        $c->index();
        break;


    /* ===============================
       OFFICER CONTROLLER
       =============================== */
    case 'officer':
        $c = new OfficerController();

        if ($action === 'dashboard') $c->dashboard();
        elseif ($action === 'ch_estab') $c->changeEstablishment();
        elseif ($action === 'saveEstablishment' && $_SERVER['REQUEST_METHOD'] === 'POST')
    $c->saveEstablishment();
        else $c->dashboard();
        break;


    /* ===============================
       PASS CONTROLLER
       =============================== */

    /* ===============================
   PASS CONTROLLER
   =============================== */
    case 'pass':
        $c = new PassController();

        if ($action === 'list') $c->list();
        elseif ($action === 'ajaxList') $c->ajaxList();
        elseif ($action === 'view') $c->view();
        elseif ($action === 'revoke' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->revoke();
        elseif ($action === 'exportCsv') $c->exportCsv();

        // NEW ROUTES (ONLY ONE PASS BLOCK)
        elseif ($action === 'generate') $c->generate();
        elseif ($action === 'generateForm') $c->actionGenerateForm();
        elseif ($action === 'courtForm') $c->actionCourtForm();
        elseif ($action === 'searchCourtCase') $c->actionSearchCourtCase();
        elseif ($action === 'generateCourt') $c->actionGenerateCourtPass();
        elseif ($action === 'generateCourtLitigant') $c->actionGenerateCourtPassLitigant();
        elseif ($action === 'generateCourtPIP') $c->actionGenerateCourtPassPartyInPerson();
        elseif ($action === 'myPasses') $c->actionMyPasses();
        elseif ($action === 'mySectionPasses') $c->mySectionPasses();
        elseif ($action === 'printSection') $c->printSection();
        elseif ($action === 'downloadPdf') $c->actionDownloadPdf();
        elseif ($action === 'saveAdvocateSection') $c->actionSaveAdvocateSection();
        elseif ($action === 'saveLitigantSection') $c->actionSaveLitigantSection();
        elseif ($action === 'savePartyInPsersonSection') $c->actionSavePIPSection();
        elseif ($action === 'viewSectionAdvocate') $c->viewSection();
        elseif ($action === 'viewSectionLitigant') $c->viewSection();
        elseif ($action === 'viewSectionParty') $c->viewSection();




        else $c->list();
        break;




    /* ===============================
       DEFAULT
       =============================== */
    default:
        $c = new AuthController();
        $c->loginForm();
        break;
}
