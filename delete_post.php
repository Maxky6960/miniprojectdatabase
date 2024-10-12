<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$postId = $_POST['id'] ?? null;

if (!$postId) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบ ID ของโพสต์']);
    exit;
}

// ตรวจสอบว่าผู้ใช้เป็นเจ้าของโพสต์หรือเป็นแอดมิน
$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($post && ($post['user_id'] == $_SESSION['user_id'] || isAdmin($_SESSION['user_id']))) {
    // ลบความคิดเห็นที่เกี่ยวข้องกับโพสต์
    $deleteCommentsStmt = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
    $deleteCommentsStmt->execute([$postId]);

    // ลบไลค์และรีโพสต์ที่เกี่ยวข้องกับโพสต์
    $deleteLikesStmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ?");
    $deleteLikesStmt->execute([$postId]);
    $deleteRepostsStmt = $pdo->prepare("DELETE FROM reposts WHERE post_id = ?");
    $deleteRepostsStmt->execute([$postId]);

    // ลบโพสต์
    $deletePostStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $result = $deletePostStmt->execute([$postId]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบโพสต์']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ลบโพสต์นี้']);
}
?>