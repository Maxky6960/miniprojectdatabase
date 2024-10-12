<?php
include 'db_connect.php'; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนส่งรายงาน');</script>";
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

// ตรวจสอบว่าข้อมูลถูกส่งมาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่าฟิลด์ 'msg' มีค่าหรือไม่
    if (isset($_POST['msg']) && !empty($_POST['msg'])) {
        // รับค่าจากฟอร์ม
        $message = $_POST['msg'];
        $user_id = $_SESSION['user_id']; // รับ user_id จาก session
    } else {
        // แสดงข้อความเตือนหากไม่พบค่า
        echo "<script>alert('กรุณากรอกข้อความ');</script>";
        exit();
    }

    // SQL สำหรับบันทึกข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO reports (message, user_id) VALUES (:message, :user_id)";

    try {
        // เตรียมคำสั่ง SQL
        $stmt = $pdo->prepare($sql);
        // ผูกค่ากับพารามิเตอร์
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_id', $user_id);
        // เรียกใช้งานคำสั่ง
        $stmt->execute();

        // Redirect กลับไปยังหน้าก่อนหน้า
        echo '<script type="text/javascript">';
        echo 'window.location.href = "' . $_SERVER['HTTP_REFERER'] . '";';
        echo '</script>';
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage() . "');</script>";
    }

    // ปิดการเชื่อมต่อฐานข้อมูล (ไม่จำเป็น แต่ทำได้)
    $pdo = null;
}
?>