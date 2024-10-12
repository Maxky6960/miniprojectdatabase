<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบหรือสมัครสมาชิก']);
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // ตรวจสอบการไลค์
    $likeStmt = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $likeStmt->execute([$post_id, $user_id]);
    $like = $likeStmt->fetch();

    if ($like) {
        $deleteStmt = $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $deleteStmt->execute([$post_id, $user_id]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $insertStmt->execute([$post_id, $user_id]);
    }

    // อัพเดตจำนวนไลค์
    $countStmt = $pdo->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
    $countStmt->execute([$post_id]);
    $like_count = $countStmt->fetchColumn();

    $pdo->commit();
    echo json_encode(['success' => true, 'like_count' => $like_count]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error occurred: ' . $e->getMessage()]);
}
?>
