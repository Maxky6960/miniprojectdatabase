<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // ดึงข้อมูลผู้ใช้
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบรหัสผ่านปัจจุบัน
    if (password_verify($current_password, $user['password'])) {
        // แฮชรหัสผ่านใหม่
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // อัปเดตรหัสผ่านในฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);

        // แสดง alert และเปลี่ยนหน้า
        echo "<script>alert('รหัสผ่านถูกเปลี่ยนเรียบร้อยแล้ว!'); window.location.href = 'manage_account.php';</script>";
        exit;
    } else {
        // แสดง alert รหัสผ่านปัจจุบันไม่ถูกต้อง
        echo "<script>alert('รหัสผ่านปัจจุบันไม่ถูกต้อง.'); window.location.href = 'manage_account.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/change_password_style.css">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : เปลี่ยนรหัสผ่าน</title>
</head>
<body>

    <div class="container">
        <h2 class="text-center">เปลี่ยนรหัสผ่าน</h2>
        <form action="" method="POST" class="mt-4">
            <div class="form-group">
                <label for="current_password">รหัสผ่านปัจจุบัน</label>
                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="รหัสผ่านปัจจุบัน" required>
            </div>
            <div class="form-group">
                <label for="new_password">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="รหัสผ่านใหม่" required>
            </div>
            <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
        </form>
        <div class="mt-3 text-center">
            <a href="manage_account.php" class="btn btn-secondary">กลับไปที่จัดการบัญชี</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
