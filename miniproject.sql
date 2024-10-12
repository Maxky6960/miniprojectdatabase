-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2024 at 09:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `miniproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'ข่าวสาร', '2024-09-23 08:04:39'),
(2, 'เทคโนโลยี', '2024-09-23 08:04:39'),
(3, 'ความรู้ทั่วไป', '2024-09-23 08:04:39'),
(4, 'กีฬา', '2024-09-23 08:56:31'),
(5, 'ความบันเทิง', '2024-09-23 08:56:31'),
(6, 'สุขภาพ', '2024-09-23 08:56:31'),
(7, 'การศึกษา', '2024-09-23 08:56:31'),
(8, 'ธุรกิจ', '2024-09-23 08:56:31'),
(9, 'ไลฟ์สไตล์', '2024-09-23 08:56:31'),
(10, 'ท่องเที่ยว', '2024-09-23 08:56:31'),
(11, 'อาหาร', '2024-09-23 08:56:31'),
(12, 'วิทยาศาสตร์', '2024-09-23 08:56:31');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gif_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`, `gif_url`) VALUES
(281, 3, 6, '😱', '2024-10-01 13:58:59', NULL),
(282, 3, 5, '🙀', '2024-10-01 14:02:11', NULL),
(283, 8, 6, '🤔🤫', '2024-10-03 18:49:13', NULL),
(285, 12, 15, '🤫', '2024-10-04 03:46:48', NULL),
(286, 12, 15, '🤭', '2024-10-04 03:58:44', NULL),
(287, 12, 15, '🤭', '2024-10-04 04:02:01', NULL),
(289, 12, 50, '😀', '2024-10-04 05:02:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(4, 1, 8, '2024-09-23 10:25:26'),
(17, 1, 11, '2024-09-23 11:33:48'),
(51, 1, 5, '2024-09-23 18:10:09'),
(52, 1, 9, '2024-09-23 18:11:00'),
(53, 1, 12, '2024-09-23 18:14:27'),
(242, 6, 12, '2024-09-25 17:21:01'),
(263, 15, 37, '2024-09-27 15:08:25'),
(268, 5, 40, '2024-09-27 16:10:36'),
(299, 15, 40, '2024-09-28 15:00:43'),
(304, 5, 51, '2024-09-28 15:28:40'),
(317, 15, 55, '2024-09-29 14:54:45'),
(319, 15, 53, '2024-09-29 14:54:51'),
(320, 15, 52, '2024-09-29 14:54:54'),
(321, 15, 51, '2024-09-29 14:54:56'),
(322, 5, 55, '2024-09-29 15:06:32'),
(326, 5, 63, '2024-09-30 07:22:14'),
(334, 40, 70, '2024-10-01 09:13:04'),
(335, 41, 55, '2024-10-01 09:36:39'),
(337, 15, 70, '2024-10-01 13:43:40'),
(340, 40, 5, '2024-10-02 07:37:08'),
(341, 5, 7, '2024-10-03 13:29:46'),
(342, 15, 7, '2024-10-03 13:33:37'),
(343, 5, 8, '2024-10-03 13:41:06'),
(344, 44, 7, '2024-10-03 19:48:08'),
(448, 15, 8, '2024-10-04 03:38:17'),
(454, 15, 11, '2024-10-04 03:39:52'),
(508, 48, 19, '2024-10-04 04:18:45'),
(543, 15, 30, '2024-10-04 04:31:02'),
(562, 15, 12, '2024-10-04 04:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` blob DEFAULT NULL,
  `like_count` int(11) DEFAULT 0,
  `repost_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `category_id`, `user_id`, `created_at`, `image`, `like_count`, `repost_count`) VALUES
(1, 'ESP32', 'ESP32 เป็นชิปไอซีไมโครคอนโทรลเลอร์ 32 บิต ที่มี WiFi และบลูทูธเวอร์ชั่น 4.2 ในตัว ซึ่งเป็นรุ่นต่อของชิปไอซี ESP8266 รุ่นยอดนิยม ผลิตโดยบริษัท Espressif', 2, 6, '2024-10-01 13:51:43', 0x2e2f75706c6f616465645f696d616765732f363666626665366663363938302d6f6d2e706e67, 0, 0),
(2, 'Sinet FTTX internet Khonkean', 'Sinet', 2, 6, '2024-10-01 13:55:15', 0x2e2f75706c6f616465645f696d616765732f363666626666343339383135382d696d61676573202831292e6a7067, 0, 0),
(3, '\"อิ๊งค์” ตัดพ้อ “สนธิ ลิ้ม” เพิ่งทำงานเดือนเดียวจะไล่แล้วเหรอ พร้อมเปิดห้องคุยให้ประเทศเดินหน้า', 'นายกฯ พร้อมเปิดห้องคุย “สนธิ ลิ้ม” หลังประกาศเตรียมระดมม็อบไล่รัฐบาลต้นปีหน้า อยากให้ประเทศเดินหน้าไม่สะดุด ชี้ชาติสงบสุขเป็นสิ่งสำคัญ พ้อเพิ่งทำงานได้เดือนเดียวจะไล่แล้ว\r\n\r\nวันที่ 1 ตุลาคม 2567 นางสาวแพทองธาร ชินวัตร นายกรัฐมนตรี ตอบคำถามผู้สื่อข่าวกรณีที่นายสนธิ ลิ้มทองกุล ประกาศขอเดินครั้งสุดท้ายรวมมวลชนขับไล่รัฐบาลต้นปีหน้าและเรียกร้องนายกรัฐมนตรี 3 เงื่อนไขและให้ตามหาคนที่เคยลอบยิงตนเองเมื่อปี 2552 นั้น ในฐานะที่นายกรัฐมนตรีเป็นหัวหน้ารัฐบาลจะประเมินและเตรียมรับมืออย่างไรบ้าง ซึ่งนายกรัฐมนตรีตอบว่า เพิ่งทำงานได้เดือนเดียวเองจะไล่แล้วเหรอ พร้อมกับหัวเราะและพูดซ้ำว่า “จะไล่แล้วเหรอคะ อย่าพึ่งไล่เลยค่ะ” นายกรัฐมนตรีกล่าวต่อว่ายังไม่มีโอกาสได้คุยกับนายสนธิเลย และตนเองก็พร้อมที่จะพูดคุย วันนี้เป็นรัฐบาลเป็นนายกรัฐมนตรีก็ต้องพูดคุยกับทุกคนและการทำให้ประเทศชาติสงบสุขเป็นสิ่งสำคัญ ถ้าประเทศสงบสุขคนไทยก็มีความสุข ต่างประเทศก็จะมาลงทุน ถือเป็นเรื่องสำคัญ\r\n\r\nเมื่อถามว่านายกรัฐมนตรีจะมีวิธีพูดคุยอย่างไร จะใช้การเดินสายพูดคุยหรือไม่ นางสาวแพทองธารถามกลับผู้สื่อข่าวว่า ให้แนะนำหน่อยยังไงดี ให้ประเทศชาติสงบสุขจะทำยังไงดี ตนเองพร้อมและไม่พร้อมมีเรื่องอยู่แล้ว ไม่คิดจะไปสู้กับอะไรที่นอกเหนือจากนี้ ซึ่งทั้งตนเองและรัฐบาลพุ่งเป้าไปที่การกระตุ้นเศรษฐกิจ เชื่อว่าถ้าเศรษฐกิจดีคนไทยมีฐานะที่รวยขึ้นสบายขึ้นทุกอย่างก็จะดีขึ้น ซึ่งตนเองก็ไม่อยากจะมีเรื่อง ถ้าประเทศชาติมันดีเศรษฐกิจมันไป นั่นแหละคือสิ่งที่อยากให้เป็น\r\n\r\nส่วนการรวมมวลชนลงถนนจะเป็นการเมืองแบบเก่า และส่วนตัวนายกรัฐมนตรีในฐานะที่เป็นคนรุ่นใหม่คิดว่าควรจะใช้การเมืองใหม่แบบสร้างสรรค์หรือไม่นั้น นายกรัฐมนตรีกล่าวว่าไม่ได้คิดเรื่องการลงถนน และคิดว่าถ้าถึงขนาดต้องลงถนนกันจริงๆ ก็มาคุยกันก่อนก็ได้ว่าปัญหาคืออะไร และคิดว่าทุกอย่างน่าจะคุยกันได้ ไม่จำเป็นต้องใช้ความเกลียดชังหรือความรุนแรงเข้าหา วันนี้ก็เป็นตัวอย่างว่าทุกคนเป็นพรรคร่วมรัฐบาลที่มาจากหลายพรรคการเมืองก็คุยกันได้', 1, 42, '2024-10-01 13:57:31', 0x2e2f75706c6f616465645f696d616765732f363666626666636262323733322d644651524f72376f577a756c713546613672426c484c3345333237666b694b68666d7a4474397870687331316e6f4e72374355583171356d507430303864716356436c2e6a7067, 0, 0),
(5, '“บิ๊กป้อม” ทำหนังสือขอคืนเงินเดือน สส. บอก ไม่ขอรับอีก จากนี้เข้าสภาฯ มากขึ้น', '“ไพบูลย์” แถลง “พล.อ.ประวิตร” ทำหนังสือขอคืนเงินประจำตำแหน่งและเงินเพิ่มของ สส. ทั้งหมด ไม่ขอรับอีก จากนี้เข้าสภาฯ มากขึ้น พร้อมแจ้ง 3 ต.ค. ยื่นหนังสือลาประชุมไว้แล้ว เนื่องจากติดภารกิจสำคัญ\r\n\r\nเมื่อเวลา 13.30 น. วันที่ 1 ตุลาคม 2567 นายไพบูลย์ นิติตะวัน เลขาธิการพรรคพลังประชารัฐ (พปชร.) พร้อมด้วย นายภัครธรณ์ เทียนไชย และ น.ส.กาญจนา จังหวะ รองเลขาธิการพรรค ร่วมแถลง โดย นายไพบูลย์ กล่าวว่า พล.อ.ประวิตร วงษ์สุวรรณ สส.แบบบัญชีรายชื่อ ในฐานะหัวหน้าพรรคพลังประชารัฐ มีความประสงค์ขอไม่รับเงินประจำตำแหน่งและเงินเพิ่มของสมาชิกสภาผู้แทนราษฎร (สส.) ให้มีผลตั้งแต่วันที่ 1 ตุลาคม 2567 ไปจนถึงวันสิ้นสุดการดำรงตำแหน่ง สส.\r\n\r\nนอกจากนี้ ยังได้ส่งหนังสือแจ้งความประสงค์ขอคืนเงินประจำตำแหน่งและเงินเพิ่มของ สส. ทั้งหมดที่ได้รับตั้งแต่เป็นสมาชิกภาพจนถึงวันที่ 30 กันยายน 2567 โดยให้สำนักงานเลขาธิการสภาผู้แทนราษฎร แจ้งจำนวนเงินทั้งหมดให้ทราบโดยเร็ว เพื่อนำส่งคืนให้ครบถ้วน', 1, 42, '2024-10-01 14:01:44', 0x2e2f75706c6f616465645f696d616765732f363666633030633865623463372d644651524f72376f577a756c713546613672426c484d494b42424d77536f4850383161444f5666576d5364787436617a314c4a3033514472435a44504d5252756f5a382e6a7067, 0, 0),
(7, 'เห็นละรักเลยย', 'เหงาจัง', 9, 15, '2024-10-03 13:26:18', 0x75706c6f6164732f6a36757a427533476853302e6a7067, 0, 0),
(8, 'แมนยู 5 พัลล #แมนยูทูเดย์ #manutdfans', 'โปรแกรมถ่ายทอดสด ยูฟ่า ยูโรป้า ลีก รอบลีก นัดที่ 2\r\n\r\nปอร์โต้ พบ แมนฯ ยูไนเต็ด\r\n\r\nคืนวันพฤหัสบดีที่ 3 ตุลาคม 2567 เวลา 02.00 น.\r\n\r\nสนาม : เอสตาดิโอ ดราเกา', 4, 5, '2024-10-03 13:40:00', 0x2e2f75706c6f616465645f696d616765732f363666653965623032343765332d3436313930333736345f3531373438383835343337333231365f333636343235353239303635333837343931355f6e2e6a7067, 0, 0),
(11, 'FT ปอร์โต้ 3-3 แมนยู', 'หลวงพี่ฮากคัมแบ็ก', 4, 15, '2024-10-04 03:38:56', 0x2e2f75706c6f616465645f696d616765732f363666663633353064363431362d3436323038383431335f3937323337393938383236373530355f313933323330333839393637353431353038385f6e2e6a7067, 0, 0),
(12, 'หลวงพี่ฮาก! มีกูไว้มึงไม่แพ้!!', 'คัมแบ็กอย่างเท่ห์', 4, 15, '2024-10-04 03:39:49', 0x2e2f75706c6f616465645f696d616765732f363666663633383537616636302d3436323038353738385f3937323431333638383236343133355f373132383436353735353531383731313137305f6e2e6a7067, 0, 0),
(36, 'yummyaa', 'yummmmk', 4, 50, '2024-10-04 05:02:37', 0x75706c6f6164732f4d656d652d30312e6a706567, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(6) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `response` text DEFAULT NULL,
  `reply` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `message`, `reg_date`, `user_id`, `response`, `reply`) VALUES
(17, 'ลืมรหัสผ่าน', '2024-10-04 05:04:50', 50, 'แก้ไขแล้ว', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reposts`
--

CREATE TABLE `reposts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reposts`
--

INSERT INTO `reposts` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(4, 1, 12, '2024-09-23 10:38:48'),
(23, 1, 5, '2024-09-23 18:10:08'),
(24, 1, 9, '2024-09-23 18:11:01'),
(150, 15, 37, '2024-09-27 15:08:25'),
(179, 15, 40, '2024-09-28 15:00:44'),
(180, 5, 40, '2024-09-28 15:00:47'),
(185, 5, 51, '2024-09-28 15:28:41'),
(195, 15, 55, '2024-09-29 14:54:46'),
(197, 15, 53, '2024-09-29 14:54:52'),
(198, 15, 52, '2024-09-29 14:54:55'),
(199, 15, 51, '2024-09-29 14:54:57'),
(200, 5, 55, '2024-09-29 15:06:33'),
(213, 5, 63, '2024-09-30 07:41:08'),
(220, 41, 70, '2024-10-01 09:36:34'),
(221, 41, 55, '2024-10-01 09:36:39'),
(223, 15, 70, '2024-10-01 13:46:36'),
(225, 40, 5, '2024-10-02 07:37:07'),
(226, 5, 7, '2024-10-03 13:29:46'),
(227, 15, 7, '2024-10-03 13:33:37'),
(229, 5, 8, '2024-10-03 13:44:09'),
(230, 44, 7, '2024-10-03 19:48:09'),
(231, 15, 8, '2024-10-04 03:21:23'),
(246, 48, 19, '2024-10-04 04:18:47'),
(270, 15, 12, '2024-10-04 04:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `created_at`, `profile_image`, `is_admin`) VALUES
(1, 'mikeclassic123', NULL, NULL, 'mikeclassic1012@hotmail.com', '$2y$10$Cdnp75hhQXJ5dVeP696cse7vx6WjZuKOv6zFEsE2/0VmwX/9gGu/y', '2024-09-23 08:10:32', '1414994332-149833-o.jpg', 1),
(4, 'mikeclassic1012', NULL, NULL, 'poramet.mo@rmuti.ac.th', '$2y$10$LUChCVtOhIweqinSF5yNfuqsfMMVUuadPPJzL51UyFwv71ZGlERjy', '2024-09-23 08:46:30', '1start108pl-360x203.jpg', 0),
(5, 'jirawat.bn', NULL, NULL, 'jj1212@gmail.com', '$2y$10$/qJJT5OTw39Mk4uN2s3qf.gWRRoY3hW31an41gcC9AagDU6TXhprm', '2024-09-23 09:14:48', 'artworks-DF34XUmGTdotYGJ4-96UpXA-t500x500.jpg', 1),
(6, 'Max', NULL, NULL, 'kkk@Echohub.com', '$2y$10$3koXyKH1.8QixOEFW0rhxO5tYgpfOvw9MvTlTHwuh95PwY8y72Oze', '2024-09-23 09:31:57', 'MAXXXXX.png', 1),
(15, 'admin', 'Poramet', 'Moonsit', 'onumalnwsza007@gmail.com', '$2y$10$2DiZAtqeiEGerRG.AcWLeOO7XHinpffzmBrFY3dzlyAyRckHYurBa', '2024-09-24 13:06:07', 'dxsRWY2iVZE.jpg', 1),
(21, 'Maxky', NULL, NULL, 'kkkhhh@Echohub.com', '$2y$10$ee7MJlpEsu1FkD8gIcbfk.VsPir4ria0Ao3.Je3igAnpErbkz.U5G', '2024-09-24 18:02:58', 'DEFAULT.jpg', 0),
(40, '0634365174', 'ไทย', 'เยอรมัน', 'mikeclas11111ssic1012@gmail.com', '$2y$10$3lSp8ZOeETnypGkXG9luf.kuFF0r7OT09QpWI4jGdu5ksEt3q1E9e', '2024-10-01 09:00:25', 'DEFAULT.jpg', 0),
(42, 'prayut', 'สมถุย', 'ลุยสวน', 'mikeclasssssic1012@hotmail.com', '$2y$10$zLT8zQQqi675Ng7a/SlXWu7TGKCPvrgD/GsgLf0eJKSJtS3pmOx1.', '2024-10-01 13:53:07', 'Yuki-Doll-เน็ตไอดอลแซ่บ.jpg', 0),
(47, 'AdminMax', 'Max', '1', '1@gmail.com', '$2y$10$F1/BCI4k.9R8aWFVLMFQGePAoDKLQYZDSwyLgEUdByikvULhHzxdK', '2024-10-04 02:58:50', 'DEFAULT.jpg', 1),
(48, 'ovo', 'gfhg', 'dflszgl', 'ggi123@gmail.com', '$2y$10$kF.p6EIlPW3TyN49jTwvyO6rgRjqSVfB9AMtBfIUgglrVFY1M/G.e', '2024-10-04 04:12:51', 'DEFAULT.jpg', 0),
(49, 'adminmike', 'a', 'maxy', 'ss@gmail.com', '$2y$10$DBH0HPytVXu6riXtTu18Se2.34xLiQh6KyyL95F.pD7IZRTlIlD36', '2024-10-04 04:43:20', 'DEFAULT.jpg', 0),
(50, 'mikekey', 'a', 'a', 'mikeclassic222@gmail.com', '$2y$10$8O5mswDI6RUWroOCuGyMruv2swyoIpY6okmAUSpd1BE2vgQJJSvfm', '2024-10-04 05:01:24', 'DEFAULT.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `comments_ibfk_1` (`post_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`user_id`);

--
-- Indexes for table `reposts`
--
ALTER TABLE `reposts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=564;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reposts`
--
ALTER TABLE `reposts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `userid` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
