<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลผู้ใช้
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบการอัปโหลดรูปโปรไฟล์
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_image'])) {
    $image = $_FILES['profile_image'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // ตรวจสอบประเภทไฟล์
    if ($image["size"] > 500000) {
        echo "ขอโทษ, ขนาดไฟล์ใหญ่เกินไป.";
        $uploadOk = 0;
    }
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "ขอโทษ, เพียงแค่ไฟล์ JPG, JPEG, PNG & GIF เท่านั้นที่อนุญาต.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1 && move_uploaded_file($image["tmp_name"], $targetFile)) {
        // อัปเดตข้อมูลในฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([basename($targetFile), $_SESSION['user_id']]);
        header("Location: manage_account.php");
    } else {
        echo "ขอโทษ, เกิดข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.";
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
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : จัดการบัญชี</title>
    <style>
        body {
            font-family: 'Sriracha', cursive;
            background: linear-gradient(135deg, #A3E4D7 25%, #2ECC71 75%);
            background-size: 400% 400%; /* เพิ่มขนาดพื้นหลังเพื่อให้การเคลื่อนไหวสมูทขึ้น */
            animation: gradient 20s ease infinite; /* เพิ่มระยะเวลาและใช้ ease เพื่อให้สมูทขึ้น */
            height: auto;
            justify-content: center; /* จัดกลางแนวนอน */
            align-items: flex-start; /* จัดแนวตั้งที่จุดเริ่มต้น */
        }

        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin-top: 2rem;
            position: relative;
            animation: fadeIn 0.8s ease-in-out;
        }

        .header {
            background-color: #2DF793;
            color: white;
            padding: 1rem;
            border-radius: 15px 15px 0 0;
            padding: 10px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-control {
            border-radius: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #2DF793;
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
            color: white;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #26CBFC;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
                max-width: 90%;
            }
        }

        .profile-image {
            max-height: 150px;
            border-radius: 50%;
            border: 10px solid #2DF793;
            margin-bottom: 20px;
        }

        .bubble {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0.6;
            animation: bubble 10s infinite;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            /* เพิ่มเงาให้ฟองอากาศ */
        }

        .bubble:nth-child(1) {
            width: 100px;
            height: 100px;
            left: 10%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 0s;
        }

        .bubble:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 25%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 2s;
        }

        .bubble:nth-child(3) {
            width: 80px;
            height: 80px;
            left: 50%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 4s;
        }

        .bubble:nth-child(4) {
            width: 50px;
            height: 50px;
            left: 75%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 6s;
        }

        .bubble:nth-child(5) {
            width: 70px;
            height: 70px;
            left: 80%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 8s;
        }

        .bubble:nth-child(6) {
            width: 90px;
            height: 90px;
            left: 15%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 10s;
        }

        .bubble:nth-child(7) {
            width: 55px;
            height: 55px;
            left: 35%;
            bottom: -150px;
            /* ปรับตำแหน่งเริ่มต้น */
            animation-delay: 12s;
        }

        @keyframes bubble {
            0% {
                transform: translateY(0);
                opacity: 0.6;
            }

            50% {
                transform: translateY(-300px);
                /* ปรับความสูงของการลอย */
                opacity: 0.2;
            }

            100% {
                transform: translateY(0);
                opacity: 0.6;
            }
        }

        .btn-back {
            background-color: #2DF793;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1.1rem;
            color: white;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-back:hover {
            background-color: #26CBFC;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-back i {
            margin-right: 5px;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            25% {
                background-position: 50% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            75% {
                background-position: 50% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="index.php" class="navbar-brand">
            <img src="logo/ECHOHUBanner3.png" alt="Logo" style="height: 40px; margin-right: 10px;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($user): ?>
                    <li class="nav-item">
                        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                            class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </li>
                    <li class="nav-item">
                        <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="my_posts.php"><i class="fas fa-list"></i> กระทู้ของฉัน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="popular_posts.php"><i class="fas fa-fire"></i> กระทู้ยอดนิยม</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</head>

<body>
    <!-- ฟองลอย -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="container">
        <h1 class="text-center text-primary">จัดการบัญชีของคุณ</h1>
        <p class="text-center">ชื่อผู้ใช้: <?= htmlspecialchars($user['username']) ?></p>

        <?php if ($user['profile_image']): ?>
            <div class="text-center">
                <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                    class="profile-image img-fluid">
            </div>
        <?php endif; ?>

        <div class="header">อัปโหลดรูปโปรไฟล์</div>
        <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
            <div class="form-group">
                <label for="profile_image">เลือกไฟล์รูปภาพ</label>
                <input type="file" name="profile_image" id="profile_image" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">อัปโหลด</button>
        </form>

        <div class="header mt-4">เปลี่ยนรหัสผ่าน</div>
        <form action="change_password.php" method="POST" class="mt-3">
            <div class="form-group">
                <label for="current_password">รหัสผ่านปัจจุบัน</label>
                <input type="password" name="current_password" id="current_password" class="form-control"
                    placeholder="รหัสผ่านปัจจุบัน" required>
            </div>
            <div class="form-group">
                <label for="new_password">รหัสผ่านใหม่</label>
                <input type="password" name="new_password" id="new_password" class="form-control"
                    placeholder="รหัสผ่านใหม่" required>
            </div>
            <button type="submit" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
        </form>

        <div class="mt-3 text-center">
            <a href="index.php" class="btn btn-back">
                <i class="fas fa-home"></i> กลับไปที่หน้าหลัก
            </a>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>