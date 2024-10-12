<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // ตรวจสอบว่าผู้ใช้เป็นเจ้าของกระทู้หรือไม่
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user_id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: my_posts.php?msg=คุณไม่มีสิทธิ์แก้ไขกระทู้นี้');
        exit;
    }
} else {
    header('Location: my_posts.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];

    // จัดการการอัปโหลดรูปภาพ
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $image = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        // หากไม่อัปโหลดรูปใหม่ ใช้รูปเดิม
        $image = $post['image'];
    }

    // อัปเดตกระทู้ในฐานข้อมูล
    $updateStmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category_id = ?, image = ? WHERE id = ?");
    $updateStmt->execute([$title, $content, $category_id, $image, $postId]);

    header('Location: my_posts.php?msg=แก้ไขกระทู้เรียบร้อย');
    exit;
}

// ดึงหมวดหมู่สำหรับการเลือก
$categoriesStmt = $pdo->prepare("SELECT * FROM categories");
$categoriesStmt->execute();
$categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลผู้ใช้
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="stylesheet" href="css/edit_post_style.css">
    <title>EchoHub : แก้ไขกระทู้</title>
</head>
<body>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="index.php"><img src="logo/ECHOHUBanner3.png" alt="Logo" style="height: 40px; margin-right: 10px;"></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="manage_account.php">
                        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                            class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a href="manage_account.php">
                        <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
                    </a>
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">แก้ไขกระทู้</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">หัวข้อกระทู้</label>
                <input type="text" class="form-control" id="title" name="title"
                    value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="content">เนื้อหากระทู้</label>
                <textarea class="form-control" id="content" name="content" rows="5"
                    required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">หมวดหมู่</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($category['id'] == $post['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">อัปโหลดรูปภาพ</label>
                <input type="file" class="form-control-file" id="image" name="image">
                <?php if (!empty($post['image'])): ?>
                    <small class="b-img">รูปภาพเดิม: <img src="<?= htmlspecialchars($post['image']) ?>" alt="Current Image"
                            style="max-height: 100px;"></small>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            <a href="my_posts.php" class="btn btn-secondary">กลับไปกระทู้ของฉัน</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>