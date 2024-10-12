<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if (isset($_SESSION['user_id'])) {
    // ดึงข้อมูลผู้ใช้
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $user = null;
}

// ดึงกระทู้ของผู้ใช้
$userId = $_SESSION['user_id'];
$myPostsStmt = $pdo->prepare("SELECT posts.*, 
    (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
    (SELECT COUNT(*) FROM reposts WHERE post_id = posts.id) AS repost_count
FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$myPostsStmt->execute([$userId]);
$myPosts = $myPostsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="stylesheet" href="css/my_posts_style.css">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : กระทู้ของฉัน</title>
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
        <h1 class="text-center text-primary">กระทู้ของฉัน</h1>
        <ul class="list-group">
            <?php foreach ($myPosts as $post): ?>
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="view_post.php?id=<?= $post['id'] ?>" class="font-weight-bold">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                        <div>
                            <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-primary"><i
                                    class="fas fa-edit"></i> แก้ไข</a>
                            <button class="btn btn-sm btn-danger" onclick="deletePost(<?= $post['id'] ?>)"><i class="fas fa-trash"></i> ลบ</button>
                            <button class="btn btn-sm btn-secondary" data-toggle="collapse"
                                data-target="#content-<?= $post['id'] ?>" aria-expanded="false"
                                aria-controls="content-<?= $post['id'] ?>">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse" id="content-<?= $post['id'] ?>">
                        <div class="mt-2">
                            <p><?= htmlspecialchars($post['content']) ?></p>
                            <?php if (!empty($post['image'])): ?>
                                <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>"
                                    class="img-fluid" style="max-height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="mt-2">
                                <span class="badge badge-info">ไลค์: <span
                                        id="like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span></span>
                                <span class="badge badge-warning">แชร์: <span
                                        id="repost-count-<?= $post['id'] ?>"><?= $post['repost_count'] ?></span></span>
                            </div>
                            <small class="text-muted mt-2">โพสต์เมื่อ:
                                <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function deletePost(postId) {
            if (confirm('คุณแน่ใจหรือว่าต้องการลบโพสต์นี้?')) {
                $.post('delete_post.php', { id: postId }, function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        alert('ลบโพสต์เรียบร้อยแล้ว');
                        location.reload(); // รีเฟรชหน้าเว็บเพื่ออัปเดตการลบโพสต์
                    } else {
                        alert(data.message);
                    }
                });
            }
        }
    </script>
</body>

</html>