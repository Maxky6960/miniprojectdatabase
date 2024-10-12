<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่าเป็นแอดมินหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('คุณไม่มีสิทธิ์เข้าถึงหน้านี้');</script>";
    exit();
}

// ดึงรายงานทั้งหมด
$reportsStmt = $pdo->prepare("SELECT reports.*, users.username FROM reports JOIN users ON reports.user_id = users.id ORDER BY reports.created_at DESC");
$reportsStmt->execute();
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Admin - ดูรายงาน</title>
</head>
<body>
    <div class="container mt-5">
        <h2>รายงานจากผู้ใช้</h2>
        <ul class="list-group">
            <?php foreach ($reports as $report): ?>
                <li class="list-group-item">
                    <strong>ผู้ใช้:</strong> <?= htmlspecialchars($report['username']) ?><br>
                    <strong>ข้อความ:</strong> <?= htmlspecialchars($report['message']) ?><br>
                    <strong>วันที่:</strong> <?= htmlspecialchars($report['created_at']) ?><br>
                    <button class="btn btn-primary mt-2" onclick="showResponseForm(<?= $report['id'] ?>)">ตอบกลับ</button>
                    <div id="response-form-<?= $report['id'] ?>" class="response-form mt-2" style="display: none;">
                        <form action="submit_response.php" method="POST">
                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                            <div class="form-group">
                                <textarea name="response" class="form-control" placeholder="เขียนตอบกลับ..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">ส่ง</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <script>
        function showResponseForm(reportId) {
            const form = document.getElementById('response-form-' + reportId);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>