<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/add_post_style.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : เพิ่มกระทู้</title>
</head>

<body>
    <!-- ฟองลอย -->
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
                    <a href="profile.php">
                        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                            class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php">
                        <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="my_posts.php"><i class="fas fa-list"></i> กระทู้ของฉัน</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="popular_posts.php"><i class="fas fa-fire"></i> กระทู้ยอดนิยม</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">เพิ่มกระทู้ใหม่</h2>
        <form action="add_post_process.php" method="POST" enctype="multipart/form-data" class="mt-4">
            <div class="form-group">
                <label for="title">ชื่อกระทู้</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="ชื่อกระทู้" required>
            </div>
            <div class="form-group">
                <label for="content">เนื้อหากระทู้</label>
                <textarea name="content" id="content" class="form-control" rows="5" placeholder="เนื้อหากระทู้"
                    required></textarea>
            </div>
            <div class="form-group">
                <label for="category">หมวดหมู่</label>
                <select class="form-control"
                    <<select name="category" id="category" class="form-control" required>
            <option value="">เลือกหมวดหมู่</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
            </div>
            <div class="form-group">
                <label for="image">เลือกรูปภาพ (ไม่จำเป็น)</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">เพิ่มกระทู้</button>
            <a href="index.php" class="btn btn-secondary">กลับไปที่หน้าหลัก</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.2.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>