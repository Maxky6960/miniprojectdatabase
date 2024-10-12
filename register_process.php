<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // แฮชรหัสผ่านเพื่อความปลอดภัย
    $confirm_password = $_POST['confirm_password'];
    $profile_image = 'DEFAULT.jpg'; // กำหนดค่าเริ่มต้นสำหรับ profile_image

    // ตรวจสอบว่าผู้ใช้นี้มีอยู่ในฐานข้อมูลหรือไม่
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        // ถ้ามีผู้ใช้นี้อยู่แล้ว ให้แสดงข้อความแจ้งเตือน
        echo "<script>alert('ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว!');window.history.back();</script>";
        exit;
    }

    // เพิ่มข้อมูลผู้ใช้ใหม่ไปยังฐานข้อมูล
    $insertStmt = $pdo->prepare("INSERT INTO users (username, email, password,profile_image) VALUES (?, ?, ?,?)");
    if ($insertStmt->execute([$username, $email, $password,$profile_image])) {
        // สมัครสมาชิกสำเร็จ เปลี่ยนหน้าไปที่ index.php
        header("Location: login.php");
        exit;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการสมัครสมาชิก!');window.history.back();</script>";
        exit;
    }
}
?>
