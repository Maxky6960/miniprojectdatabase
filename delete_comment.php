<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$commentId = $_POST['id'];

// ตรวจสอบเจ้าของความคิดเห็น
$checkCommentStmt = $pdo->prepare("SELECT user_id, post_id FROM comments WHERE id = ?");
$checkCommentStmt->execute([$commentId]);
$comment = $checkCommentStmt->fetch(PDO::FETCH_ASSOC);

if ($comment) {
    // ตรวจสอบว่าเป็นเจ้าของความคิดเห็นหรือเจ้าของโพสต์หรือเป็นแอดมิน
    $isCommentOwner = ($comment['user_id'] == $_SESSION['user_id']);
    
    // ตรวจสอบว่าเป็นเจ้าของโพสต์
    $checkPostStmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
    $checkPostStmt->execute([$comment['post_id']]);
    $post = $checkPostStmt->fetch(PDO::FETCH_ASSOC);
    
    $isPostOwner = ($post && $post['user_id'] == $_SESSION['user_id']);

    // ตรวจสอบว่าเป็นแอดมิน
    $isAdmin = isAdmin($_SESSION['user_id']);

    if ($isCommentOwner || $isPostOwner || $isAdmin) {
        // ลบความคิดเห็น
        $deleteStmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $deleteStmt->execute([$commentId]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Comment not found']);
}
?>