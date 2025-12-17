<?php
require_once __DIR__ . '/../app/controller/ReservationController.php';
require_once __DIR__ . '/../app/controller/EventController.php';

$action = $_GET["action"] ?? "home";

$resCtrl = new ReservationController();
$eventCtrl = new EventController();

switch ($action) {

    /* HOME PAGE */
    case "home":
        $resCtrl->home();
        break;

    /* RESERVATIONS CRUD */
    case "submit":
        $resCtrl->submit();
        break;

    case "list":
        $resCtrl->list();
        break;

    case "edit":
        $resCtrl->edit($_GET["id"]);
        break;

    case "update":
        $resCtrl->update();
        break;

    case "delete":
        $resCtrl->delete($_GET["id"]);
        break;

    case "dashboard":
        $resCtrl->dashboard();
        break;

    /* EVENTS CRUD */
    case "event_list":
        $eventCtrl->list();
        break;

    case "event_add":
        $eventCtrl->addPage();
        break;

    case "event_add_submit":
        $eventCtrl->add();
        break;

    case "event_edit":
        $eventCtrl->edit($_GET["id"]);
        break;

    case "event_update":
        $eventCtrl->update();
        break;

    case "event_delete":
        $eventCtrl->delete($_GET["id"]);
        break;

    /* PUBLIC EVENT CREATION */
    case "event_add_public":
        $eventCtrl->addPublicPage();
        break;

    case "event_add_public_submit":
        $eventCtrl->submitPublic();
        break;

    /* EVENT DETAILS */
    case "event":
        $eventCtrl->show($_GET["id"]);
        break;

    default:
        $resCtrl->home();
}
?>
