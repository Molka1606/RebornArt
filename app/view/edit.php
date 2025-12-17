<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Reservation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/res_event/assets/css/modern-styles.css" />
</head>

<body class="bg-light p-5">

<div class="container fade-in">

    <h2 class="mb-4 text-center slide-in-left">Edit Reservation</h2>

    <div class="card p-4 shadow glass fade-in" style="max-width: 600px; margin: 0 auto; background: rgba(255, 255, 255, 0.95);">

        <form action="/res_event/public/index.php?action=update" method="POST">
            <input type="hidden" name="id" value="<?= $reservation['id'] ?>">

            <label class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control mb-3"
                   name="full_name" id="full_name"
                   value="<?= htmlspecialchars($reservation['full_name']) ?>" required minlength="2" maxlength="100">

            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control mb-3"
                   name="email" id="email"
                   value="<?= htmlspecialchars($reservation['email']) ?>" required maxlength="255">

            <label class="form-label">Event <span class="text-danger">*</span></label>
            <select name="event_id" id="event_id" class="form-select mb-3" required>
                <option value="">Select Event</option>
                <?php foreach ($events as $event): ?>
                    <option value="<?= $event['id'] ?>" <?= $reservation['event_id'] == $event['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($event['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="form-label">Message</label>
            <textarea class="form-control mb-4" rows="4"
                      name="message" id="message" maxlength="500"><?= htmlspecialchars($reservation['message']) ?></textarea>

            <button class="btn btn-success w-100">Update</button>
        </form>

    </div>

    <div class="text-center mt-3">
        <a href="/res_event/public/index.php?action=list"
           class="btn btn-secondary">Back</a>
    </div>

</div>

<script src="/res_event/assets/js/form-validation.js"></script>

</body>
</html>
