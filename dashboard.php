<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ดึงข้อมูลผู้ใช้
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าเป็นแอดมินหรือไม่
if ($user['is_admin'] != 1) {
    header('Location: unauthorized.php'); // หรือหน้าอื่นที่คุณต้องการ
    exit;
}

// ดึงข้อมูลผู้ใช้ทั้งหมด
$allUsersStmt = $pdo->prepare("SELECT * FROM users");
$allUsersStmt->execute();
$allUsers = $allUsersStmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูล reports ทั้งหมด โดยเรียงลำดับตามการตอบกลับ
$allReportsStmt = $pdo->prepare("SELECT * FROM reports ORDER BY response IS NULL DESC, response ASC");
$allReportsStmt->execute();
$allReports = $allReportsStmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูล posts ทั้งหมด
$allPostsStmt = $pdo->prepare("SELECT * FROM posts");
$allPostsStmt->execute();
$allPosts = $allPostsStmt->fetchAll(PDO::FETCH_ASSOC);

// นับจำนวนโพสทั้งหมด
$postCountStmt = $pdo->prepare("SELECT COUNT(*) as post_count FROM posts");
$postCountStmt->execute();
$postCount = $postCountStmt->fetch(PDO::FETCH_ASSOC)['post_count'];

// นับจำนวนผู้ใช้ทั้งหมด
$userCountStmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM users");
$userCountStmt->execute();
$userCount = $userCountStmt->fetch(PDO::FETCH_ASSOC)['user_count'];

// นับจำนวนไลค์ทั้งหมด
$likeCountStmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM likes");
$likeCountStmt->execute();
$likeCount = $likeCountStmt->fetch(PDO::FETCH_ASSOC)['like_count'];

// นับจำนวนรีโพสทั้งหมด
$repostCountStmt = $pdo->prepare("SELECT COUNT(*) as repost_count FROM reposts");
$repostCountStmt->execute();
$repostCount = $repostCountStmt->fetch(PDO::FETCH_ASSOC)['repost_count'];

// นับจำนวนรีพอร์ททั้งหมด
$reportCountStmt = $pdo->prepare("SELECT COUNT(*) as report_count FROM reports");
$reportCountStmt->execute();
$reportCount = $reportCountStmt->fetch(PDO::FETCH_ASSOC)['report_count'];

// ฟังก์ชันสำหรับลบผู้ใช้
if (isset($_POST['delete_user_id'])) {
    $deleteUserId = $_POST['delete_user_id'];

    // ลบโพสต์ทั้งหมดของผู้ใช้
    $deletePostsStmt = $pdo->prepare("DELETE FROM posts WHERE user_id = ?");
    $deletePostsStmt->execute([$deleteUserId]);

    // ลบผู้ใช้
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$deleteUserId]);

    header("Location: dashboard.php");
    exit;
}
// ฟังก์ชันสำหรับลบรายงาน
if (isset($_POST['delete_report_id'])) {
    $deleteReportId = $_POST['delete_report_id'];
    $deleteStmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
    $deleteStmt->execute([$deleteReportId]);
    header("Location: dashboard.php");
    exit;
}
// ฟังก์ชันสำหรับลบการตั้งกระทู้
if (isset($_POST['delete_post_id'])) {
    $deletePostId = $_POST['delete_post_id'];
    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $deleteStmt->execute([$deletePostId]);
    header("Location: dashboard.php");
    exit;
}
// ฟังก์ชันสำหรับตอบกลับรายงาน
if (isset($_POST['reply_report_id']) && isset($_POST['reply_message'])) {
    $replyReportId = $_POST['reply_report_id'];
    $replyMessage = $_POST['reply_message'];
    $replyStmt = $pdo->prepare("UPDATE reports SET response = ? WHERE id = ?");
    $replyStmt->execute([$replyMessage, $replyReportId]);
    echo json_encode(['status' => 'success']);
    exit;
}

// ฟังก์ชันสำหรับเปลี่ยน role ของผู้ใช้
if (isset($_POST['change_role_user_id']) && isset($_POST['new_role'])) {
    $changeRoleUserId = $_POST['change_role_user_id'];
    $newRole = $_POST['new_role'];

    // ตรวจสอบว่า newRole เป็นค่าที่ถูกต้อง (0 หรือ 1)
    if (in_array($newRole, ['0', '1'])) {
        $changeRoleStmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $changeRoleStmt->execute([$newRole, $changeRoleUserId]);
    }
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>DashboardAdminEchoHub</title>
    <style>
        body {
            font-size: 14px;
            background-color: #f0f0f0;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .user-table,
        .report-table,
        .post-table {
            width: 100%;
            margin-top: 20px;
            background-color: #ffffff;
            /* เปลี่ยนสีพื้นหลังให้ขาวเพื่อให้ดูสะอาดตา */
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* เพิ่มความเข้มของเงา */
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            /* ป้องกันการแสดงผลที่ไม่ต้องการ */
        }
        .user-table th,
        .user-table td,
        .report-table th,
        .report-table td,
        .post-table th,
        .post-table td {
            padding: 15px;
            /* เพิ่ม padding เพื่อให้มีระยะห่างมากขึ้น */
            text-align: left;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
            /* เอฟเฟกต์การเปลี่ยนสีพื้นหลัง */
        }

        .user-table th,
        .report-table th,
        .post-table th {
            background-color: #f2f7fc;
            /* เปลี่ยนสีพื้นหลังของหัวตารางให้ดูน่าสนใจ */
            font-weight: bold;
            /* ทำให้ข้อความในหัวตารางหนาขึ้น */
            color: #333;
            /* เปลี่ยนสีข้อความให้เข้มขึ้น */
        }

        .user-table tr:hover,
        .report-table tr:hover,
        .post-table tr:hover {
            /* เอฟเฟกต์เมื่อชี้ที่แถว */
            background-color: #f9f9f9;
            /* เปลี่ยนสีพื้นหลังเมื่อชี้ */
        }

        .is-admin-0 {
            color: green;
        }

        .is-admin-1 {
            color: red;
        }

        .content {
            overflow-y: auto;
            max-height: 100vh;
            padding: 20px;
            margin-left: 250px;
        }
        .delete-button,
        .reply-button {
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            color: white;
            font-size: 14px;
            margin: 2px;
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #c82333;
        }

        .reply-button {
            background-color: #007bff;
        }

        .reply-button:hover {
            background-color: #0056b3;
        }

        .replied-label {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .chart-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .chart-container canvas {
            max-width: 300px;
            max-height: 300px;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #76b7fa;
            /* เปลี่ยนสีพื้นหลังเป็นสีฟ้า */
        }

        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px;
            min-height: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5em;
            color: #333;
        }

        .counts-container {
            display: flex;
            justify-content: space-around;
            width: 100%;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .count-box {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 250px;
            color: black;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .count-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .count-box.users {
            background-color: #ADD8E6;
        }

        .count-box.posts {
            background-color: #E6E6FA;
        }

        .count-box.likes {
            background-color: #FFD700;
        }

        .count-box.reposts {
            background-color: #FFB6C1;
        }

        .count-box.reports {
            background-color: #FFA07A;
        }

        .count-box h3 {
            margin: 0;
            font-size: 1.5em;
        }

        .count-box p {
            margin: 5px 0 0;
            font-size: 2em;
            font-weight: bold;
        }

        .chart-container {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .chart-container canvas {
            max-width: 400px;
            max-height: 400px;
        }

        .button-container {
            margin-top: 20px;
        }

        .dashboard-button {
            padding: 10px 20px;
            font-size: 1em;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .dashboard-button:hover {
            background-color: #0056b3;
            transform: translateY(-5px);
        }

        .role-select {
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-right: 5px;
        }

        .pie-chart-container {
            background-color: white;
            /* Set the background color to white */
            border-radius: 16px;
            /* Optional: Add rounded corners */
            padding: 16px;
            /* Optional: Add padding around the canvas */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Optional: Add a shadow for depth */
        }
    </style>
</head>

<body>
    <section>
        <div id="nav-bar">
            <input id="nav-toggle" type="checkbox" />
            <div id="nav-header">
                <a id="nav-title" href="index.php" target="_blank">
                    <img src="logo/ECHOHUBanner2.png" alt="Logo" style="height: 55px;">
                </a>
                <label for="nav-toggle"><span id="nav-toggle-burger"></span></label>
                <hr />
            </div>
            <div id="nav-content">
                <div class="nav-button" onclick="Dashboard()">
                    <i class="fas fa-palette"></i><span>Dashboard</span>
                </div>
                <div class="nav-button" onclick="showPost()">
                    <i class="fas fa-images"></i><span>Post</span>
                </div>
                <div class="nav-button" onclick="showReport()">
                    <i class="fas fa-thumbtack"></i><span>Report</span>
                </div>
                <div class="nav-button" onclick="showUserProfile()">
                    <i class="fas fa-heart"></i><span>User</span>
                </div>
            </div>
            <input id="nav-footer-toggle" type="checkbox" />
            <div id="nav-footer">
                <div id="nav-footer-heading">
                    <div id="nav-footer-avatar">
                        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>">
                    </div>
                    <div id="nav-footer-titlebox">
                        <?= htmlspecialchars($user['username']) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="content" id="content">
        <!-- User data will be displayed here -->
    </div>

    <script>
        function showMessage(message) {
            document.getElementById('content').innerHTML = `<h1>${message}</h1>`;
        }

        function showUserProfile() {
            const userProfile = `
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Profile Image</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created At</th>
                            <th>Is Admin</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" style="width: 50px; height: 50px;"></td>
                                <td><a href="profile.php?id=<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></a></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                                <td><select class="role-select" onchange="changeRole(<?= $user['id'] ?>, this.value)">
                                        <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>User</option>
                                        <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>Admin</option>
                                    </select></td>
                                <td><button class="delete-button" onclick="deleteUser(<?= $user['id'] ?>)">Delete</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            `;
            document.getElementById('content').innerHTML = userProfile;
        }


        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'dashboard.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_user_id';
                input.value = userId;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showReport() {
            const reportTable = `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Report Title</th>
                            <th>created_at</th>
                            <th>UserID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allReports as $report): ?>
                            <tr id="report-row-<?= $report['id'] ?>">
                                <td><?= htmlspecialchars($report['id']) ?></td>
                                <td><?= htmlspecialchars($report['message']) ?></td>
                                <td><?= htmlspecialchars($report['reg_date']) ?></td>
                                <td><a href="profile.php?id=<?= $report['user_id'] ?>"><?= htmlspecialchars($report['user_id']) ?></a></td>
                                <td><button class="delete-button" onclick="deleteReport(<?= $report['id'] ?>)">Delete</button>
                                    <button class="reply-button" onclick="replyReport(<?= $report['id'] ?>)">Reply</button>
                                <?php if ($report['response']): ?>
                                        <span class="replied-label">Replied</span>
                                    <?php endif; ?><?php endforeach; ?>
                                </td>
                            </tr>
                    </tbody>
                </table>
            `;
            document.getElementById('content').innerHTML = reportTable;
        }

        function deleteReport(reportId) {
            if (confirm('Are you sure you want to delete this report?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'dashboard.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_report_id';
                input.value = reportId;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function replyReport(reportId) {
            const replyMessage = prompt('Enter your reply:');
            if (replyMessage) {
                $.post('dashboard.php', {
                    reply_report_id: reportId,
                    reply_message: replyMessage
                }, function(response) {
                    if (response.status === 'success') {
                        const reportRow = document.getElementById('report-row-' + reportId);
                        reportRow.querySelector('.reply-button').remove();
                        // Update the report data in the table
                        reportRow.querySelector('.replied-label').textContent = 'ตอบกลับแล้ว';
                    } else {
                        alert('Error replying to report');
                    }
                }, 'json');
            }
        }

        function showPost() {
            const postTable = `
                <table class="post-table">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Title</th>
                            <th>content</th>
                            <th>UserID</th>
                            <th>Image</th>
                            <th>created_at</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPosts as $post): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['id']) ?></td>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= htmlspecialchars($post['content']) ?></td>
                                <td><a href="profile.php?id=<?= $post['user_id'] ?>"><?= htmlspecialchars($post['user_id']) ?></a></td>
                                <td><img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="width: 200px; height: auto;"></td>
                                <td><?= htmlspecialchars($post['created_at']) ?></td>
                                <td><button class="delete-button" onclick="deletePost(<?= $post['id'] ?>)">Delete</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            `;
            document.getElementById('content').innerHTML = postTable;
        }

        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'dashboard.php';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_post_id';
                input.value = postId;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function Dashboard() {
            const dashboardContent = `
                <div class="dashboard-container">
                    <div class="counts-container">
                        <div class="count-box users">
                            <h3>Users</h3>
                            <p><?= number_format($userCount) ?></p>
                        </div>
                        <div class="count-box posts">
                            <h3>Posts</h3>
                            <p><?= number_format($postCount) ?></p>
                        </div>
                        <div class="count-box likes">
                            <h3>Likes</h3>
                            <p><?= number_format($likeCount) ?></p>
                        </div>
                        <div class="count-box reposts">
                            <h3>Reposts</h3>
                            <p><?= number_format($repostCount) ?></p>
                        </div>
                        <div class="count-box reports">
                            <h3>Reports</h3>
                            <p><?= number_format($reportCount) ?></p>
                        </div>
                    </div>
                    <div class="chart-container pie-chart-container">
                        <canvas id="userPostPieChart"></canvas>
                    </div>
                </div>
            `;
            document.getElementById('content').innerHTML = dashboardContent;

            // Pie Chart
            const pieCtx = document.getElementById('userPostPieChart').getContext('2d');
            const userPostPieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Users', 'Posts', 'Likes', 'Reposts', 'Reports'], // Labels for the pie chart
                    datasets: [{
                        label: 'Count',
                        data: [<?= $userCount ?>, <?= $postCount ?>, <?= $likeCount ?>, <?= $repostCount ?>, <?= $reportCount ?>], // User, post, like, repost, and report count data
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 215, 0, 0.2)',
                            'rgba(255, 182, 193, 0.2)',
                            'rgba(255, 160, 122, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 215, 0, 1)',
                            'rgba(255, 182, 193, 1)',
                            'rgba(255, 160, 122, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        function changeRole(userId, newRole) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dashboard.php';

            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'change_role_user_id';
            userIdInput.value = userId;
            form.appendChild(userIdInput);

            const newRoleInput = document.createElement('input');
            newRoleInput.type = 'hidden';
            newRoleInput.name = 'new_role';
            newRoleInput.value = newRole;
            form.appendChild(newRoleInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Dashboard();
        });
    </script>
</body>

</html>