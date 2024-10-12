<?php
session_start(); // เริ่มเซสชัน
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบข้อมูลผู้ใช้ในฐานข้อมูล
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // ถ้าล็อกอินสำเร็จ ให้บันทึกข้อมูลผู้ใช้ในเซสชัน
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // เปลี่ยนหน้าไปที่ index.php
        header("Location: index.php");
        exit;
    } else {
        echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
    }
}
?>
