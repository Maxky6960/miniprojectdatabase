<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category'];

    // ตรวจสอบการอัปโหลดรูปภาพ
    $image = null; // ใช้ null ถ้าไม่มีการอัปโหลด
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // จำกัดขนาดไฟล์ที่ 2MB (2048000 ไบต์)
        if ($fileSize > 2048000) {
            echo "ขนาดไฟล์ต้องไม่เกิน 2MB.";
            exit;
        }

        // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการชนกัน
        $newFileName = uniqid() . '-' . $fileName;
        $uploadFileDir = './uploaded_images/';
        $dest_path = $uploadFileDir . $newFileName;

        // อัปโหลดไฟล์
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image = $dest_path; // เก็บที่อยู่ไฟล์ที่อัปโหลด
        } else {
            echo "ไม่สามารถอัปโหลดไฟล์ได้.";
            exit;
        }
    }

    // บันทึกข้อมูลลงฐานข้อมูล
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, category_id, image, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $category_id, $image, $_SESSION['user_id']]);

    header("Location: index.php");
    exit;
}
?>
