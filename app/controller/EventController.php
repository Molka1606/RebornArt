<?php
require_once __DIR__ . '/../model/Event.php';

class EventController {

    public function list() {
        $model = new Event();
        $events = $model->getAll();

        include __DIR__ . '/../view/event_list.php';
    }

    public function addPage() {
        include __DIR__ . '/../view/event_add.php';
    }

    public function add() {
        $model = new Event();

        $image = $model->uploadImage($_FILES["image"]);

        $latitude = !empty($_POST["latitude"]) ? $_POST["latitude"] : null;
        $longitude = !empty($_POST["longitude"]) ? $_POST["longitude"] : null;

        $model->create(
            $_POST["title"],
            $_POST["location"],
            $_POST["event_date"],
            $_POST["description"],
            $image,
            $latitude,
            $longitude
        );

        header("Location: /res_event/public/index.php?action=event_list");
        exit;
    }

    public function edit($id) {
        $model = new Event();
        $event = $model->find($id);

        include __DIR__ . '/../view/event_edit.php';
    }

    public function update() {
        $model = new Event();

        $newImage = null;

        if (!empty($_FILES["image"]["name"])) {
            $newImage = $model->uploadImage($_FILES["image"]);
        }

        $latitude = !empty($_POST["latitude"]) ? $_POST["latitude"] : null;
        $longitude = !empty($_POST["longitude"]) ? $_POST["longitude"] : null;

        $model->update(
            $_POST["id"],
            $_POST["title"],
            $_POST["location"],
            $_POST["event_date"],
            $_POST["description"],
            $newImage,
            $latitude,
            $longitude
        );

        header("Location: /res_event/public/index.php?action=event_list");
        exit;
    }

    public function delete($id) {
        $model = new Event();
        $model->delete($id);

        header("Location: /res_event/public/index.php?action=event_list");
        exit;
    }

    public function show($id) {
    $model = new Event();
    $event = $model->getById($id);

    if (!$event) {
        echo "Event not found";
        return;
    }

    include __DIR__ . '/../view/event_detail.php';
    }
    public function addPublicPage() {
    include __DIR__ . '/../view/event_add_public.php';
    }

    public function submitPublic() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $model = new Event();

        $imageName = null;
        if (!empty($_FILES["image"]["name"])) {
            $imageName = time() . "_" . basename($_FILES["image"]["name"]);
            $target = __DIR__ . '/../../uploads/events/' . $imageName;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target);
        }

        $latitude = !empty($_POST["latitude"]) ? $_POST["latitude"] : null;
        $longitude = !empty($_POST["longitude"]) ? $_POST["longitude"] : null;

        $model->createPublic(
            $_POST["title"],
            $_POST["description"],
            $_POST["location"],
            $_POST["event_date"],
            $imageName,
            $latitude,
            $longitude
        );

        echo "<script>alert('Your event has been submitted and is pending approval!'); 
        window.location.href='index.php';</script>";
        }
    }


}
