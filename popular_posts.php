<?php
session_start();
include 'db_connect.php';

// ดึงกระทู้ยอดนิยมตามจำนวนไลค์และรีโพสต์
$popularPostsStmt = $pdo->prepare("
    SELECT posts.*, 
        (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM reposts WHERE post_id = posts.id) AS repost_count,
        ((SELECT COUNT(*) FROM likes WHERE post_id = posts.id) + 
         (SELECT COUNT(*) FROM reposts WHERE post_id = posts.id)) AS total_count
    FROM posts 
    ORDER BY total_count DESC 
    LIMIT 10");
$popularPostsStmt->execute();
$popularPosts = $popularPostsStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <link rel="stylesheet" href="css/popular_posts_style.css">
    <title>EchoHub : กระทู้ยอดนิยม</title>
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
    <a href="index.php"><img src="logo/ECHOHUBanner3.png" alt="Logo"style="height: 40px; margin-right: 10px;"></a>
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
                        <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_posts.php"><i class="fas fa-list"></i> กระทู้ของฉัน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">กระทู้ยอดนิยม</h2>
        <ul class="list-group">
            <?php foreach ($popularPosts as $post): ?>
                <li class="list-group-item">
                    <a href="view_post.php?id=<?= $post['id'] ?>" class="font-weight-bold">
                        <i class="fas fa-comments"></i> <?= htmlspecialchars($post['title']) ?>
                    </a>
                    <p class="mb-1 mt-2"><?= htmlspecialchars($post['content']) ?></p>

                    <?php if (!empty($post['image'])): ?>
                        <img src="<?= htmlspecialchars($post['image']) ?>" class="post-image mt-2" alt="Post Image"><br>
                    <?php endif; ?>

                    <small class="text-muted">โพสต์เมื่อ: <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                    <div class="mt-2">
                        <span class="badge badge-success">ไลค์ <?= $post['like_count'] ?></span>
                        <span class="badge badge-info">รีโพสต์ <?= $post['repost_count'] ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
