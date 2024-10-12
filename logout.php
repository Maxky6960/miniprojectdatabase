<?php
session_start();
session_destroy(); // ลบข้อมูลเซสชัน
header("Location: index.php?logged_out=1"); // เปลี่ยนทางไปยังหน้าหลัก พร้อมพารามิเตอร์
exit;
?>
