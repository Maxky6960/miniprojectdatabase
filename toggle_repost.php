<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู้ระบบหรือสมัครสมาชิก']);
    exit;
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    // ตรวจสอบการรีโพสต์
    $repostStmt = $pdo->prepare("SELECT * FROM reposts WHERE post_id = ? AND user_id = ?");
    $repostStmt->execute([$post_id, $user_id]);
    $repost = $repostStmt->fetch();

    if ($repost) {
        $deleteStmt = $pdo->prepare("DELETE FROM reposts WHERE post_id = ? AND user_id = ?");
        $deleteStmt->execute([$post_id, $user_id]);
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO reposts (post_id, user_id) VALUES (?, ?)");
        $insertStmt->execute([$post_id, $user_id]);
    }

    // อัพเดตจำนวนรีโพสต์
    $countStmt = $pdo->prepare("SELECT COUNT(*) AS repost_count FROM reposts WHERE post_id = ?");
    $countStmt->execute([$post_id]);
    $repost_count = $countStmt->fetchColumn();

    $pdo->commit();
    echo json_encode(['success' => true, 'repost_count' => $repost_count]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error occurred: ' . $e->getMessage()]);
}
?>
