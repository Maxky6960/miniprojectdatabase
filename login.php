<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}


?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="stylesheet" href="css/login_style.css">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : เข้าสู่ระบบ</title>
</head>

<body>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="login-container">
    <center><a class="navbar-brand" style="center">
            <img src="logo/ECHOHUBanner2.png" alt="Logo" oncontextmenu="return false;" style="height: 70px;">
        </a></center>
            
        <div class="login-header">เข้าสู่ระบบ</div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST" class="mt-4">
            <div class="form-group" style="position: relative;"> <!-- เพิ่ม position: relative เพื่อจัดการกับไอคอน -->
                <i class="fas fa-user" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i> <!-- จัดตำแหน่งไอคอน -->
                <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" required style="padding-left: 40px; border-radius: 30px;"> <!-- เพิ่ม padding ซ้ายและปรับขอบ -->
            </div>
            <div class="form-group" style="position: relative;"> <!-- เพิ่ม position: relative เพื่อจัดการกับไอคอน -->
                <i class="fas fa-lock" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%);"></i> <!-- จัดตำแหน่งไอคอน -->
                <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required style="padding-left: 40px; border-radius: 30px;"> <!-- เพิ่ม padding ซ้ายและปรับขอบ -->
            </div>
            <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
        </form>
        <div class="mt-3 text-center">
            <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>