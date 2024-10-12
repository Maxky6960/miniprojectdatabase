<?php
// รวมไฟล์เชื่อมต่อฐานข้อมูล
require_once 'db_connect.php';

// ตรวจสอบว่ามีการส่งค่าจากการค้นหาหรือไม่
if (isset($_GET['query'])) {
    $searchQuery = trim($_GET['query']);
    
    // เตรียมคำสั่ง SQL
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ?");
    $likeQuery = "%" . $searchQuery . "%"; // ใช้ % เพื่อค้นหาคำที่ตรงกัน
    $stmt->execute([$likeQuery, $likeQuery]);

    // ดึงผลลัพธ์
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งข้อมูลกลับเป็น JSON
    if ($stmt->rowCount() > 0) {
        echo json_encode($results);
    } else {
        echo json_encode([]); // ส่ง array ว่างถ้าไม่มีผลลัพธ์
    }
} else {
    echo json_encode([]); // ส่ง array ว่างถ้าไม่มี query
}
?>
