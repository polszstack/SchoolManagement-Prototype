<?php

require_once __DIR__ . '/config/config.php';

//controllers
require_once __DIR__ . '/app/controllers/ApprovalDecisionSupportController.php';

session_start();

$basePath = BASE_URL;
$uri = trim(str_replace($basePath, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');

$segments = explode('/', $uri);

switch ($segments[0] ?? '') {

    case '':
        require_once __DIR__ . '/app/controllers/HomeController.php';
        (new HomeController())->index();
        break;

    case 'borrow':

        require_once __DIR__ . '/app/controllers/PhysBrwController.php';
        $controller = new PhysBrwController();


        if (!isset($segments[1])) {
            $controller->index();
        }


        break;

    case 'damages':

        require_once __DIR__ . '/app/controllers/DamageController.php';
        $controller = new DamageController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'edit' && isset($segments[2])) {
            $controller->edit($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }

        break;

    case 'inventory':
        require_once __DIR__ . '/app/controllers/InventoryController.php';
        $controller = new InventoryController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }

        break;

    case 'psycho-inventory':
        require_once __DIR__ . '/app/controllers/PsychoInvController.php';
        $controller = new PsychoInvController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }

        break;

    case 'he-inventory':
        require_once __DIR__ . '/app/controllers/HeInvController.php';
        $controller = new HeInvController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }


    case 'crim-inventory':
        require_once __DIR__ . '/app/controllers/CrimInvController.php';
        $controller = new CrimInvController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }
        break;

    case 'crim-borrow':
        require_once __DIR__ . '/app/controllers/CrimBrwController.php';
        (new CrimBrwController())->index();
        break;

    case 'it-inventory':
        require_once __DIR__ . '/app/controllers/ItInventoryController.php';
        (new ItInventoryController())->index();
        break;

    case 'crim-damage':
        require_once __DIR__ . '/app/controllers/CrimDmgController.php';
        (new CrimDmgController())->index();
        break;

    case 'phys_borrow':
        require_once __DIR__ . '/app/controllers/PhysBrwController.php';
        (new PhysBrwController())->index();
        break;

    case 'schedule':
        require_once __DIR__ . '/app/controllers/ScheduleController.php';
        (new ScheduleController())->index();
        break;

        break;
    case 'psycho-damage':
        require_once __DIR__ . '/app/controllers/PsychoDmgController.php';
        $controller = new PsychoDmgController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'view' && isset($segments[2])) {
            $controller->view($segments[2]);
        } elseif ($segments[1] === 'delete' && isset($segments[2])) {
            $controller->destroy($segments[2]);
        } elseif ($segments[1] === 'update') {
            $controller->update();
        }
        break;

    case 'approval-decision-support':
        require_once __DIR__ . '/app/controllers/ApprovalDecisionSupportController.php';
        $controller = new ApprovalDecisionSupportController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'update-decision') {
            $controller->updateDecision();
        } elseif ($segments[1] === 'delete') {
            $controller->delete();
        };
        break;

    case 'concern-issue-tracking':
        require_once __DIR__ . '/app/controllers/ConcernIssueTrackingController.php';
        $controller = new ConcernIssueTrackingController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'resolve') {
            $controller->resolve();
        } elseif ($segments[1] === 'delete') {
            $controller->delete();
        };
        break;

    case 'report-submission-management':
        require_once __DIR__ . '/app/controllers/ReportSubmmisionManagementController.php';
        $controller = new ReportSubmmisionManagementController();

        if (!isset($segments[1])) {
            $controller->index();
        } elseif ($segments[1] === 'create') {
            $controller->create();
        } elseif ($segments[1] === 'delete') {
            $controller->delete();
        };
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/app/views/errors/404.php';
}
