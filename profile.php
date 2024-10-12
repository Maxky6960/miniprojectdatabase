<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่ามีการส่ง user_id มาหรือไม่
$profile_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, profile_image, created_at, is_admin FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าไม่พบข้อมูลผู้ใช้
if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}

// ตรวจสอบว่าเป็นโปรไฟล์ของตัวเองหรือไม่ 
$is_own_profile = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id'];

// ดึงข้อมูล is_admin ของผู้ใช้ที่เข้าสู่ระบบ
$logged_in_user_stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$logged_in_user_stmt->execute([$_SESSION['user_id']]);
$logged_in_user = $logged_in_user_stmt->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลจำนวนโพสต์ของผู้ใช้
$posts_count_stmt = $pdo->prepare("SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?");
$posts_count_stmt->execute([$profile_id]);
$posts_count = $posts_count_stmt->fetch(PDO::FETCH_ASSOC)['post_count'];

// ดึงข้อมูลรายงานของผู้ใช้
$reportsStmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY reg_date DESC");
$reportsStmt->execute([$profile_id]);
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของ <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <link rel="stylesheet" href="css/profile.css">
    <title>EchoHub : โปรไฟล์</title>
    <style>
        .response-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            position: relative;
        }
        .response-box:hover {
            background-color: #e2e6ea;
        }
        .response-content {
            display: none;
            margin-top: 10px;
        }
        .modal-body .response-box {
            margin-bottom: 20px;
        }
        .delete-button {
            position: absolute;
            top: 10px;
            right: 10px;
            color: red;
            cursor: pointer;
        }
        .btn-custom {
            background-color: #80cbc4;
            border-color: #80cbc4;
            color: white;
        }
        .btn-custom:hover {
            background-color: #9fa8da;
            border-color: #9fa8da;
        }
    </style>
</head>
<body>
<div class="star">★</div>
<div class="star">★</div>
<div class="star">★</div>
<div class="star">★</div>
<div class="star">★</div>
<div class="star">★</div>
    <div class="container">
        <div class="profile-container text-center">
            <img src="uploads/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="รูปโปรไฟล์" class="profile-image">
            <h2><?php echo htmlspecialchars($user['username']); ?></h2>
            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
            <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>วันที่เข้าร่วม:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
            <p><strong>จำนวนโพสต์:</strong> <?php echo $posts_count; ?></p>
            <?php if ($is_own_profile): ?>
                <a href="manage_account.php" class="btn btn-custom mt-3"><i class="fas fa-edit"></i> แก้ไขโปรไฟล์</a>
                <button class="btn btn-custom mt-3" data-toggle="modal" data-target="#reportsModal"><i class="fas fa-flag"></i> รายงานของคุณ</button>
            <?php endif; ?>
            <?php if (isset($logged_in_user['is_admin']) && $logged_in_user['is_admin'] == 1): // ตรวจสอบว่าเป็นแอดมินหรือไม่ ?>
                <a href="dashboard.php" class="btn btn-custom mt-3"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <?php endif; ?>
            <a href="index.php" class="btn btn-custom mt-3"><i class="fas fa-home"></i> กลับหน้าหลัก</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportsModalLabel">รายงานของคุณ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mt-3">
                        <?php foreach ($reports as $report): ?>
                            <div class="response-box" id="report-<?= $report['id'] ?>" onclick="toggleResponse(<?= $report['id'] ?>)">
                                <strong>วันที่รายงาน:</strong> <?= htmlspecialchars($report['reg_date']) ?><br>
                                <strong>ข้อความ:</strong> <?= htmlspecialchars($report['message']) ?>
                                <div id="response-<?= $report['id'] ?>" class="response-content">
                                    <strong>การตอบกลับ:</strong> <?= htmlspecialchars($report['response']) ?: 'ยังไม่มีการตอบกลับ' ?>
                                </div>
                                <span class="delete-button" onclick="deleteReport(<?= $report['id'] ?>)">&times;</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleResponse(reportId) {
            const responseContent = document.getElementById('response-' + reportId);
            if (responseContent.style.display === 'none' || responseContent.style.display === '') {
                responseContent.style.display = 'block';
            } else {
                responseContent.style.display = 'none';
            }
        }

        function deleteReport(reportId) {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายงานนี้?')) {
                $.post('delete_report.php', { report_id: reportId }, function(response) {
                    if (response.status === 'success') {
                        document.getElementById('report-' + reportId).remove();
                    } else {
                        alert('เกิดข้อผิดพลาดในการลบรายงาน');
                    }
                }, 'json');
            }
        }
    </script>
</body>
</html>