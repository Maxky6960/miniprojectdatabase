<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirm_password = $_POST['confirm_password'];
    $profile_image = 'DEFAULT.jpg';

    // ตรวจสอบว่าผู้ใช้นี้มีอยู่ในฐานข้อมูลหรือไม่
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว!');window.history.back();</script>";
        exit;
    }

    // เพิ่มข้อมูลผู้ใช้ใหม่ไปยังฐานข้อมูล
    $insertStmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
    if ($insertStmt->execute([$username, $first_name, $last_name, $email, $password, $profile_image])) {
        header("Location: login.php");
        exit;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการสมัครสมาชิก!');window.history.back();</script>";
        exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/register_style.css">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : สมัครสมาชิก</title>
</head>

<body>
    <!-- ฟองสบู่ -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <div class="register-container">
    <center><a class="navbar-brand" href="index.php">
            <img src="logo/ECHOHUBanner2.png" alt="Logo" oncontextmenu="return false;" style="height: 80px;">
        </a></center>
        <div class="register-header">สมัครสมาชิก</div>
        <form action="" method="POST" class="mt-4">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" id="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="ชื่อจริง" required>
            </div>
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="นามสกุล" required>
            </div>
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" class="form-control" placeholder="อีเมล" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="รหัสผ่าน" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control"
                    placeholder="กรอกรหัสผ่านอีกครั้ง" required>
            </div>
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        </form>
    </div>
    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            var emailInput = document.getElementById('email');
            var emailValue = emailInput.value;

            // ตรวจสอบว่าอีเมลมีรูปแบบที่ถูกต้อง
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailValue)) {
                alert('กรุณากรอกอีเมลที่ถูกต้อง');
                event.preventDefault(); // ป้องกันการส่งฟอร์ม
                return; // ออกจากฟังก์ชัน
            }

            var passwordInput = document.getElementById('password');
            var confirmPasswordInput = document.getElementById('confirm_password');
            var passwordValue = passwordInput.value;
            var confirmPasswordValue = confirmPasswordInput.value;

            // ตรวจสอบว่า confirm_password ตรงกับ password หรือไม่
            if (passwordValue !== confirmPasswordValue ) {
                alert('กรุณากรอกรหัสผ่านให้ตรงกัน!');
                event.preventDefault(); // ป้องกันการส่งฟอร์ม
            }
            if (passwordValue.length <= 7 && confirmPasswordValue.length <= 7) {
                alert('กรุณากรอกรหัสผ่านที่มีความยาวอย่างน้อย 8 ตัวอักษร');
                event.preventDefault(); // ป้องกันการส่งฟอร์ม
            }
            
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>