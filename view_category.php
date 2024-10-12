<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
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

$category_id = $_GET['id'];

$categoryStmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$categoryStmt->execute([$category_id]);
$category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

$postsStmt = $pdo->prepare("
    SELECT posts.*, 
           LEFT(posts.content, 300) AS short_content, 
           users.username 
    FROM posts 
    LEFT JOIN users ON posts.user_id = users.id 
    WHERE posts.category_id = ? 
    ORDER BY posts.created_at DESC
");
$postsStmt->execute([$category_id]);
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงกระทู้ยอดนิยม
$categoriesStmt = $pdo->prepare("
    SELECT categories.*, COUNT(posts.id) AS post_count 
    FROM categories 
    LEFT JOIN posts ON categories.id = posts.category_id 
    GROUP BY categories.id 
    ORDER BY post_count DESC 
    LIMIT 10
");
$categoriesStmt->execute();
$popularCategories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="css/view_category_style.css">
    <title>EchoHub : <?= htmlspecialchars($category['name']) ?> - กระทู้</title>
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
        <div class="collapse navbar-collapse w-100" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <div id="suggestions" class="suggestions-list"></div>
                <?php if (isset($_SESSION['user_id']) && $user): ?>
    <li class="nav-item">
        <a href="profile.php">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                    class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
            <?php else: ?>
                <img src="path/to/default_profile_image.png" alt="Default Profile Image"
                    class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a href="profile.php">
            <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
        </a>
    </li>
<?php endif; ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 rounded-title">
                <h1 class="category"><?= htmlspecialchars($category['name']) ?></h1>
                <h2 class="sub-label rounded-title">กระทู้ในหมวดหมู่นี้</h2>
                <ul class="list-group mt-4 w-100">
                    <?php if (count($posts) > 0): ?>
                        <?php foreach ($posts as $post): ?>
                            <li class="list-post">
                                <!-- แสดงรูปภาพกระทู้ -->

                                <a href="view_post.php?id=<?= $post['id'] ?>"
                                    class="font-weight-bold"><?= htmlspecialchars($post['title']) ?></a><br>
                                <?php if (!empty($post['image'])): ?>
                                    <div class="mt-2">
                                        <img src="<?= htmlspecialchars($post['image']) ?>"
                                            alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid"
                                            style="max-height: 200px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                                <!-- แสดงชื่อผู้เขียน -->
                                <p class="mb-1">ผู้เขียน: <?= htmlspecialchars($post['username']) ?></p>
                                <!-- แสดงเนื้อหาแบบย่อ -->
                                <p class="mb-1"><?= nl2br(htmlspecialchars($post['short_content'])) ?></p>
                                <small class="text-muted">โพสต์เมื่อ:
                                    <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-post">ไม่มีการกระทู้ในหมวดหมู่นี้</li>
                    <?php endif; ?>
                </ul>
                <div class="mt-3 text-center">
                    <a href="index.php" class="btn btn-back">
                        <i class="fas fa-home"></i> กลับไปที่หน้าหลัก
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <h2 class="mt-4 title">หมวดหมู่ยอดนิยม</h2>
                <ul class="list-group">
                    <?php foreach ($popularCategories as $category): ?>
                        <?php
                        $iconClass = '';
                        switch ($category['name']) {
                            case 'ข่าวสาร':
                                $iconClass = 'fas fa-newspaper';
                                break;
                            case 'เทคโนโลยี':
                                $iconClass = 'fas fa-laptop';
                                break;
                            case 'ความรู้ทั่วไป':
                                $iconClass = 'fas fa-book';
                                break;
                            case 'กีฬา':
                                $iconClass = 'fas fa-futbol';
                                break;
                            case 'ความบันเทิง':
                                $iconClass = 'fas fa-film';
                                break;
                            case 'สุขภาพ':
                                $iconClass = 'fas fa-heartbeat';
                                break;
                            case 'การศึกษา':
                                $iconClass = 'fas fa-school';
                                break;
                            case 'วิทยาศาสตร์':
                                $iconClass = 'fas fa-flask';
                                break;
                            default:
                                $iconClass = 'fas fa-folder';
                        }
                        ?>
                        <li class="list-group-item">
                            <a href="view_category.php?id=<?= $category['id'] ?>"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="<?= $iconClass ?>"></i> <?= htmlspecialchars($category['name']) ?></span>
                                <span class="badge badge-primary badge-pill"><?= $category['post_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>