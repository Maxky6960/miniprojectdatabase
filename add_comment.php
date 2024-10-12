<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบหรือสมัครสมาชิก']);
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$content = $_POST['content'];

// เพิ่มความคิดเห็นลงในฐานข้อมูล
$insertStmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
$insertStmt->execute([$post_id, $user_id, $content]);

// ดึงข้อมูลผู้ใช้เพื่อส่งกลับ
$userStmt = $pdo->prepare("SELECT username, profile_image FROM users WHERE id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// ส่งข้อมูลกลับไปยังหน้าเว็บ
echo json_encode([
    'success' => true,
    'content' => htmlspecialchars($content),
    'user' => $user,
    'created_at' => date('Y-m-d H:i')
]);
?>
