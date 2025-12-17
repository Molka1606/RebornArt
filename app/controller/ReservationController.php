<?php
require_once __DIR__ . '/../model/Reservation.php';

class ReservationController {

    public function home() {
        include __DIR__ . '/../view/home.php';
    }

    public function submit() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            require_once __DIR__ . '/../model/Event.php';
            require_once __DIR__ . '/../service/MailService.php';
            
            $m = new Reservation();
            $result = $m->create(
                $_POST["full_name"],
                $_POST["email"],
                $_POST["event_id"],
                $_POST["message"]
            );
            
            if ($result) {
                try {
                    $eventModel = new Event();
                    $event = $eventModel->find($_POST["event_id"]);
                    
                    if ($event) {
                        $reservation = $m->getById($result);
                        
                        if ($reservation) {
                            $mailService = new MailService();
                            $emailSent = $mailService->sendReservationConfirmation($reservation, $event);
                            
                            if (!isset($_SESSION)) {
                                session_start();
                            }
                            $_SESSION['email_sent'] = $emailSent;
                        }
                    }
                } catch (Exception $e) {
                    error_log("Email sending failed: " . $e->getMessage());
                }
            }
            
            include __DIR__ . '/../view/success.php';
        }
    }

    public function list() {
        require_once __DIR__ . '/../model/Event.php';
        
        $model = new Reservation();
        $reservations = $model->getAll();
        
        
        $eventModel = new Event();
        $events = $eventModel->getAll();
        $eventsById = [];
        foreach ($events as $event) {
            $eventsById[$event['id']] = $event['title'];
        }

        include __DIR__ . '/../view/admin_list.php';
    }

    public function edit($id) {
        require_once __DIR__ . '/../model/Event.php';
        
        if (!$id) die("Invalid reservation ID.");

        $model = new Reservation();
        $reservation = $model->find($id);

        if (!$reservation) die("Reservation not found.");

        
        $eventModel = new Event();
        $events = $eventModel->getAll();

        include __DIR__ . '/../view/edit.php';
    }

    public function update() {
        if (!isset($_POST["id"])) die("Missing ID");

        $model = new Reservation();
        $model->update(
            $_POST["id"],
            $_POST["full_name"],
            $_POST["email"],
            $_POST["event_id"],
            $_POST["message"]
        );

        header("Location: /res_event/public/index.php?action=list");
        exit;
    }

    public function delete($id) {
        if (!$id) die("Missing ID");

        $model = new Reservation();
        $model->delete($id);

        header("Location: /res_event/public/index.php?action=list");
        exit;
    }

    public function dashboard() {
        require_once __DIR__ . '/../model/Event.php';
        
        $model = new Reservation();
        $total_reservations = $model->countAll();
        $pending_messages = $model->countPending();
        
        $eventModel = new Event();
        $total_events = count($eventModel->getAll());
        
        include __DIR__ . '/../view/dashboard.php';
    }

}
