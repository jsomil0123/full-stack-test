<?php
$conn = mysqli_connect("localhost", "root", "mysql", "full-stack-test");
if (!$conn) die("Database connection failed");

// Tab creation
if (isset($_POST['add_tab'])) {
    $title = $_POST['tab_title'];
    mysqli_query($conn, "INSERT INTO tabs (title) VALUES ('$title')");
    header("Location: index.php");
    exit;
}

if (isset($_POST['add_slide'])) {
    $tab_id = $_POST['slide_tab'];
    $content = $_POST['slide_content'];
    $image_path = '';

    if (!empty($_FILES['slide_image']['name'])) {
        $upload_dir = 'assets/uploads/';
        $filename = basename($_FILES['slide_image']['name']);
        $target = $upload_dir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['slide_image']['tmp_name'], $target)) {
            $image_path = $target;
        }
    }

    mysqli_query($conn, "INSERT INTO slides (tab_id, content, image_url) VALUES ($tab_id, '$content', '$image_path')");
    header("Location: index.php?tab=$tab_id");
    exit;
}

if (isset($_GET['delete_slide'])) {
    $id = $_GET['delete_slide'];
    mysqli_query($conn, "DELETE FROM slides WHERE id = $id");
    header("Location: index.php");
    exit;
}

$active_tab = $_GET['tab'] ?? 1;
$tabs_result = mysqli_query($conn, "SELECT * FROM tabs");
$tabs = [];
while ($row = mysqli_fetch_assoc($tabs_result)) {
    $tabs[] = $row;
}
$slides_result = mysqli_query($conn, "SELECT * FROM slides WHERE tab_id = $active_tab");
$slides = [];
while ($row = mysqli_fetch_assoc($slides_result)) {
    $slides[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP Full Stack CRUD Slider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="container">

<h2>Full Stack Test (No API)</h2>
<div class="row">
    <div class="col-md-3">
        <form method="POST" class="mb-3">
            <input type="text" name="tab_title" class="form-control mb-2" placeholder="New Tab Name" required>
            <button type="submit" name="add_tab" class="btn btn-primary w-100">Add Tab</button>
        </form>

        <ul class="nav flex-column nav-pills" id="tabNav">
            <?php foreach ($tabs as $tab): ?>
                <li class="nav-item mb-1">
                    <a href="?tab=<?= $tab['id'] ?>" class="nav-link <?= $tab['id'] == $active_tab ? 'active' : '' ?>">
                        <?= htmlspecialchars($tab['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="accordion" id="tabAccordion">
            <?php foreach ($tabs as $i => $tab): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i !== 0 ? 'collapsed' : '' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#acc<?= $tab['id'] ?>">
                            <?= htmlspecialchars($tab['title']) ?>
                        </button>
                    </h2>
                    <div id="acc<?= $tab['id'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>">
                        <div class="accordion-body">
                            <a href="?tab=<?= $tab['id'] ?>" class="btn btn-sm btn-outline-primary">Open Slider</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-5">
        <div id="slideCarousel" class="carousel slide mb-3" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($slides as $i => $slide): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" data-img="<?= $slide['image_url'] ?>">
                        <div class="p-3">
                            <h5><?= htmlspecialchars($slide['content']) ?></h5>
                            <a href="?delete_slide=<?= $slide['id'] ?>" class="btn btn-sm btn-danger mt-2">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#slideCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#slideCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="slide_tab" value="<?= $active_tab ?>">
            <textarea name="slide_content" class="form-control mb-2" placeholder="Slide content" required></textarea>
            <input type="file" name="slide_image" class="form-control mb-2" accept="image/*" required>
            <button type="submit" name="add_slide" class="btn btn-success w-100">Add Slide</button>
        </form>
    </div>

    <div class="col-md-4">
        <div id="col3Image" class="col3-image"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
