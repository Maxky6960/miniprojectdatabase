<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['user_id'])) {
    // ดึงข้อมูลผู้ใช้
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $user = null;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?msg=Invalid post ID');
    exit;
}

$postId = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT posts.*, 
    (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
    (SELECT COUNT(*) FROM reposts WHERE post_id = posts.id) AS repost_count,
    users.username AS poster_username,
    users.profile_image AS poster_profile_image,
    users.id AS poster_id
FROM posts 
JOIN users ON posts.user_id = users.id
WHERE posts.id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: index.php?msg=โพสต์ไม่พบ');
    exit;
}

// ดึงความคิดเห็น
$commentsStmt = $pdo->prepare("SELECT comments.*, users.username, users.profile_image, users.id AS commenter_id FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at DESC");
$commentsStmt->execute([$postId]);
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/view_post_style.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : <?= htmlspecialchars($post['title']) ?></title>
</head>

<body>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="index.php"><img src="logo/ECHOHUBanner3.png" alt="Logo" style="height: 40px; margin-right: 10px;"></a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <div id="suggestions" class="suggestions-list"></div>
                <li class="nav-item">
                    <a href="profile.php?id=<?= $_SESSION['user_id'] ?>">
                        <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                            class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a href="profile.php?id=<?= $post['poster_id'] ?>">
                        <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_post.php"><i class="fas fa-plus"></i> เพิ่มกระทู้</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5 post-container">
        <div class="d-flex align-items-center mb-3">
            <a href="profile.php?id=<?= $post['poster_id'] ?>">
                <img src="uploads/<?= htmlspecialchars($post['poster_profile_image']) ?>" alt="Profile Image"
                    style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
            </a>
            <a href="profile.php?id=<?= $post['poster_id'] ?>">
                <strong><?= htmlspecialchars($post['poster_username']) ?></strong>
            </a>
            <?php if ($post['user_id'] == $_SESSION['user_id'] || $user['is_admin'] == 1): ?>
                <button class="btn btn-danger btn-sm ml-auto" onclick="deletePost(<?= $postId ?>)">ลบโพสต์</button>
            <?php endif; ?>
        </div>
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        <?php if (!empty($post['image'])): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>"
                class="img-fluid mb-3">
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-outline-success btn-sm btn-custom" id="like-button"
                    onclick="toggleLike(<?= $postId ?>)">
                    <i class="fas fa-thumbs-up"></i>
                    <span id="like-count"><?= $post['like_count'] ?></span> ไลค์
                </button>
                <button class="btn btn-outline-info btn-sm btn-custom" id="repost-button"
                    onclick="toggleRepost(<?= $postId ?>)">
                    <i class="fas fa-share"></i>
                    <span id="repost-count"><?= $post['repost_count'] ?></span> รีโพสต์
                </button>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="comments-header">ความคิดเห็น</div>
        
        <!-- Comment Form -->
        <form class="mt-2" onsubmit="submitComment(event, <?= $postId ?>)">
            <div class="comment-input" id="comment-input-<?= $postId ?>" contenteditable="true"
                placeholder="เขียนความคิดเห็น..." required></div>
            <div align="right">
                <button type="button" class="emoji-button" onclick="toggleEmojiPicker(<?= $postId ?>)">😊</button>
                <button type="submit" class="submit-comment">ตอบกลับ</button>
            </div>
        </form>
        <!-- Emoji Picker -->
        <div id="emoji-picker-<?= $postId ?>" class="emoji-picker" style="display: none;">
            <div class="emoji-list"></div>
        </div>

        <ul class="list-group" id="comments-list">
            <?php foreach ($comments as $comment): ?>
                <li class="list-group-item comment-item d-flex justify-content-between align-items-center"
                    id="comment-<?= $comment['id'] ?>">
                    <div class="d-flex align-items-center">
                        <a href="profile.php?id=<?= $comment['commenter_id'] ?>">
                            <img src="uploads/<?= htmlspecialchars($comment['profile_image']) ?>" alt="Profile Image"
                                style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                        </a>
                        <a href="profile.php?id=<?= $comment['commenter_id'] ?>">
                            <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        </a>:
                        <?= htmlspecialchars($comment['content']) ?>
                    </div>
                    <div>
                        <small class="text-muted"><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></small>
                        <?php if ($comment['user_id'] == $_SESSION['user_id'] || $post['user_id'] == $_SESSION['user_id'] || $user['is_admin'] == 1): ?>
                            <button class="btn btn-danger btn-sm" onclick="deleteComment(<?= $comment['id'] ?>)">ลบ</button>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="mt-3 text-center">
        <a href="index.php" class="btn btn-back">
            <i class="fas fa-home"></i> กลับไปที่หน้าหลัก
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function toggleLike(postId) {
            $.post('toggle_like.php', { post_id: postId }, function (response) {
                const data = JSON.parse(response);
                $('#like-count').text(data.like_count);
                $('#like-button').toggleClass('liked', data.user_liked);
            });
        }

        function toggleRepost(postId) {
            $.post('toggle_repost.php', { post_id: postId }, function (response) {
                const data = JSON.parse(response);
                $('#repost-count').text(data.repost_count);
                $('#repost-button').toggleClass('reposted', data.user_reposted);
            });
        }

        function deleteComment(commentId) {
            if (confirm('คุณแน่ใจหรือว่าต้องการลบความคิดเห็นนี้?')) {
                $.post('delete_comment.php', { id: commentId }, function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#comment-' + commentId).remove();
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function deletePost(postId) {
            if (confirm('คุณแน่ใจหรือว่าต้องการลบโพสต์นี้?')) {
                $.post('delete_post.php', { id: postId }, function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        window.location.href = 'index.php?msg=ลบโพสต์เรียบร้อย';
                    } else {
                        alert(data.message);
                    }
                });
            }
        }

        function submitComment(event, postId) {
            event.preventDefault();

            let content = $('#comment-input-' + postId).html();
            content = content.replace(/<br\s*[\/]?>/gi, "\n").replace(/<div>/gi, "\n").replace(/<\/div>/gi, "").trim();

            if (content.length === 0) {
                alert("กรุณาเขียนความคิดเห็น"); // แจ้งให้ผู้ใช้กรอกความคิดเห็น
                return;
            }

            $.ajax({
                url: 'add_comment.php',
                method: 'POST',
                data: { post_id: postId, content: content },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const currentTime = new Date(); // ดึงเวลาปัจจุบัน
                        const year = currentTime.getFullYear(); // ปีปัจจุบัน
                        const datePart = `${year}-${(currentTime.getMonth() + 1).toString().padStart(2, '0')}-${currentTime.getDate().toString().padStart(2, '0')}`; // แสดงวันที่ในรูปแบบ YYYY-MM-DD
                        const timePart = currentTime.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', hour12: false }); // แยกเวลาในรูปแบบ HH:MM

                        $('#comments-list').prepend(`
                            <li class="list-group-item comment-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <a href="profile.php?id=${data.user.id}">
                                        <img src="uploads/${data.user.profile_image}" alt="Profile Image" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                    </a>
                                    <a href="profile.php?id=${data.user.id}">
                                        <strong>${data.user.username}</strong>
                                    </a>: ${data.content.replace(/\n/g, '<br>')}
                                </div>
                                <div>
                                    <small class="text-muted">${datePart} ${timePart}</small>
                                    <button class="btn btn-danger btn-sm" onclick="deleteComment(${data.comment_id})">ลบ</button>
                                </div>
                            </li>
                        `);

                        $('#comment-input-' + postId).html(''); // ล้างค่า input
                    } else {
                        alert(data.message);
                        window.location.href = 'login.php';
                    }
                }
            });
        }

        function toggleEmojiPicker(postId) {
            const emojiPicker = $('#emoji-picker-' + postId);
            emojiPicker.toggle(); // แสดง/ซ่อนแผงอิโมจิ
            if (emojiPicker.is(':visible')) {
                loadEmojis(postId);
            }
        }

        function loadEmojis(postId) {
            const emojiList = $('#emoji-picker-' + postId + ' .emoji-list');
            emojiList.empty(); // ล้างรายการอิโมจิ
            const emojis = ["😀", "😁", "😂", "🤣", "😃", "😄", "😅", "😆", "😉", "😊", "😋", "😎", "😍", "😘", "😗", "😙", "😚", "🙂", "🤗", "🤔", "🤭", "🤫", "🤥", "😐", "😑", "😶", "🙄", "😏", "😒", "😬", "😔", "😪", "😴", "😷", "🤒", "🤕", "🤢", "🤧", "😵", "😲", "😳", "😱", "😨", "😰", "😢", "😥", "😓", "🙁", "😖", "😣", "😞", "😫", "😩", "😤", "😠", "😡", "🤬", "😈", "👿", "💀", "👻", "👽", "💩", "😺", "😸", "😻", "😼", "😽", "🙀", "😿", "😹", "😾"
            , "👍", "👎", "👌", "❤", "🧡", "💚", "💙", "💜", "🤎", "🖤", "🤍", "💔", "❣", "💕", "💞", "💓", "💗", "💖", "💘", "❤️‍🔥", "❤️‍🩹", "💟", "🔞", "🚭"
            ];
            emojis.forEach(emoji => {
                emojiList.append(`<span class="emoji-item" onclick="addEmoji('${emoji}', ${postId})">${emoji}</span>`);
            });
        }

        function addEmoji(emoji, postId) {
            const input = $('#comment-input-' + postId);
            input.append(emoji); // เพิ่มอิโมจิลงในพื้นที่แสดงความคิดเห็น
            $('#emoji-picker-' + postId).hide(); // ซ่อนแผงอิโมจิหลังจากเลือก
        }

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.emoji-picker, .emoji-button').length) {
                $('.emoji-picker').hide(); // ซ่อนแผงอิโมจิเมื่อคลิกที่ส่วนอื่นของหน้าเว็บ
            }
        });
    </script>
</body>

</html>