<?php
require_once __DIR__ . '/../bootstrap.php';

// simple router: ?r=controller/action
$route = isset($_GET['r']) ? $_GET['r'] : 'auth/loginForm';
$parts = explode('/', $route);
$controller = $parts[0];
$action = isset($parts[1]) ? $parts[1] : 'index';

// map controllers
switch ($controller) {
    case 'auth':
        $c = new AuthController();
        if ($action === 'loginPost' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->loginPost();
        elseif ($action === 'registerPost' && $_SERVER['REQUEST_METHOD'] === 'POST') $c->registerPost();
        elseif ($action === 'registerForm') $c->registerForm();
        elseif ($action === 'logout') $c->logout();
        else $c->loginForm();
        break;

    case 'dashboard':
        $c = new DashboardController();
        $c->index();
        break;

   case 'pass':
    $c = new PassController();

    if ($action === 'list') {
        $c->list();
    }
    elseif ($action === 'ajaxList') {
        $c->ajaxList();
    }
    elseif ($action === 'view') {
        $c->view();
    }
    elseif ($action === 'revoke' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $c->revoke();
    }
    elseif ($action === 'exportCsv') {
        $c->exportCsv();
    }
    else {
        $c->list();
    }
    break;




    default:
        $c = new AuthController();
        $c->loginForm();
        break;
}
