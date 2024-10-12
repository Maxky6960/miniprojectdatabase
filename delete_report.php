<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (isset($_POST['report_id'])) {
    $reportId = $_POST['report_id'];
    $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ? AND user_id = ?");
    $stmt->execute([$reportId, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Report not found or not authorized']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
