<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่
if (isset($_SESSION['user_id'])) {
    // ดึงข้อมูลผู้ใช้
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $user = null;
}

// ดึงกระทู้ใหม่พร้อมจำนวนไลค์และรีโพสต์
$postsStmt = $pdo->prepare("SELECT posts.*, 
    (SELECT COUNT(*) FROM likes WHERE post_id = posts.id) AS like_count,
    (SELECT COUNT(*) FROM reposts WHERE post_id = posts.id) AS repost_count
FROM posts ORDER BY created_at DESC LIMIT 20");
$postsStmt->execute();
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่ยอดนิยม
$categoriesStmt = $pdo->prepare("SELECT categories.*, COUNT(posts.id) AS post_count FROM categories LEFT JOIN posts ON categories.id = posts.category_id GROUP BY categories.id ORDER BY post_count DESC LIMIT 10");
$categoriesStmt->execute();
$popularCategories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Sriracha" rel="stylesheet">
    <link rel="stylesheet" href="css/index_style.css">
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.0/emojionearea.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.0/emojionearea.min.js"></script>
    <link rel="icon" href="./logo/LOGOECHOHUB2.png">
    <title>EchoHub : หน้าแรก</title>
</head>

<body>



    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">
            <img src="logo/ECHOHUBanner3.png" alt="Logo" oncontextmenu="return false;" style="height: 40px; margin-right: 10px;">
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if ($user): ?>
                    <form class="form-inline my-2 my-lg-0" id="search-form" onsubmit="return false;"
                        style="position: relative;">
                        <input class="form-control mr-sm-2" type="text" id="search-input" placeholder="ค้นหากระทู้..."
                            aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="button" id="search-button">ค้นหา</button>
                    </form>&nbsp&nbsp&nbsp;
                    <div id="suggestions" class="suggestions-list"></div>
                    <li class="nav-item">
                        <a href="profile.php">
                            <img src="uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image"
                                class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profile.php">
                            <span class="nav-link"><?= htmlspecialchars($user['username']) ?></span>
                        </a>
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

    <div class="container mt-5 slide-in-left">
    
    <div align="center">
        <img src="./logo/welcometoechohub.png" oncontextmenu="return false;" alt="Welcome to Echohub">
    </div>
    
        <div class="row">
            <div class="col-md-4 slide-in-left">
                <h2 class="mt-4 rounded-title">หมวดหมู่ยอดนิยม</h2>
                <ul class="list-group">
                    <?php
                    foreach ($popularCategories as $category):
                        $iconClass = '';
                        switch ($category['name']) {
                            case 'ข่าวสาร':
                                $iconClass = 'fas fa-newspaper';
                                break;
                            case 'เทคโนโลยี':
                                $iconClass = 'fas fa-laptop';
                                break;
                            case 'ความรู้ทั่วไป':
                                $iconClass = 'fas fa-book';
                                break;
                            case 'กีฬา':
                                $iconClass = 'fas fa-futbol';
                                break;
                            case 'ความบันเทิง':
                                $iconClass = 'fas fa-film';
                                break;
                            case 'สุขภาพ':
                                $iconClass = 'fas fa-heartbeat';
                                break;
                            case 'การศึกษา':
                                $iconClass = 'fas fa-graduation-cap';
                                break;
                            case 'ธุรกิจ':
                                $iconClass = 'fas fa-briefcase';
                                break;
                            case 'ไลฟ์สไตล์':
                                
                                $iconClass = 'fas fa-smile';
                                break;
                            case 'ท่องเที่ยว':
                                $iconClass = 'fas fa-plane';
                                break;
                            case 'อาหาร':
                                $iconClass = 'fas fa-utensils';
                                break;
                            case 'วิทยาศาสตร์':
                                $iconClass = 'fas fa-flask';
                                break;
                        }
                        ?>
                        <li class="list-group-item">
                            <a href="view_category.php?id=<?= $category['id'] ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="<?= $iconClass ?>"></i> <?= htmlspecialchars($category['name']) ?></span>
                            <span class="badge badge-primary badge-pill"><?= $category['post_count'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-md-8 slide-in-right">
                <h2 class="mt-4 rounded-title">กระทู้ใหม่</h2>
                <ul class="list-group">
                    <?php foreach ($posts as $post): ?>
                        <li class="list-group-item">
                            <a href="view_post.php?id=<?= $post['id'] ?>" class="font-weight-bold">
                                <i class="fas fa-comments"></i> <?= htmlspecialchars($post['title']) ?>
                            </a>
                            <?php if (!empty($post['image'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($post['image']) ?>"
                                        alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid"
                                        style="max-height: 200px; object-fit: cover;">
                                </div>
                            <?php endif; ?>
                            <p class="mb-1 mt-2"><?= htmlspecialchars($post['content']) ?></p>
                            <small class="text-muted">โพสต์เมื่อ:
                                <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                            <div class="mt-2">
                                <button id="like-button-<?= $post['id'] ?>" class="btn btn-sm btn-info like-button"
                                    onclick="toggleLike(<?= $post['id'] ?>)">
                                    <i class="fas fa-thumbs-up"></i> ไลค์ <span
                                        id="like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span>
                                </button>
                                <button id="repost-button-<?= $post['id'] ?>" class="btn btn-sm btn-warning repost-button"
                                    onclick="toggleRepost(<?= $post['id'] ?>)">
                                    <i class="fas fa-share"></i> รีโพสต์ <span
                                        id="repost-count-<?= $post['id'] ?>"><?= $post['repost_count'] ?></span>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="sharePost(<?= $post['id'] ?>)">
                                    <i class="fas fa-share-alt"></i> แชร์
                                </button>
                            </div>

                            <!-- Comments Section -->
                            <h5 class="mt-3">ความคิดเห็น:</h5>
                            <ul class="list-group" id="comments-list-<?= $post['id'] ?>">
                                <?php
                                // ดึงความคิดเห็นสำหรับโพสต์นี้ (แค่ 5 คอมเมนต์ล่าสุด)
                                $commentsStmt = $pdo->prepare("SELECT comments.*, users.username, users.profile_image FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC LIMIT 5");
                                $commentsStmt->execute([$post['id']]);
                                $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($comments as $comment):
                                    ?>
                                    <li class="comment-box list-group-item">
                                        <img src="uploads/<?= htmlspecialchars($comment['profile_image']) ?>"
                                            alt="Profile Image" class="rounded-circle"
                                            style="height: 40px;margin-right: 10px;">
                                        <strong><?= htmlspecialchars($comment['username']) ?></strong>:
                                        <?= htmlspecialchars($comment['content']) ?>
                                        <small class="text-muted float-right"><?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <!-- Comment Form -->
                            <form class="mt-2" onsubmit="submitComment(event, <?= $post['id'] ?>)">
                                <div class="comment-input" id="comment-input-<?= $post['id'] ?>" contenteditable="true"
                                    placeholder="เขียนความคิดเห็น..." required></div>
                                <div align="right">
                                <button type="button" class="emoji-button"
                                    onclick="toggleEmojiPicker(<?= $post['id'] ?>)">😊</button>
                                <button type="submit" class="submit-comment">ตอบกลับ</button>
                                </div>
                            </form>
                            <!-- Emoji Picker -->
                            <div id="emoji-picker-<?= $post['id'] ?>" class="emoji-picker" style="display: none;">
                                <div class="emoji-list"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- Bubble effect elements -->
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.comment-input').emojioneArea({
                pickerPosition: "bottom",
                toneStyle: "bullet",
                events: {
                    keyup: function () {
                        $(this).data("content", $(this).html());
                    }
                }
            });
        });

        function toggleLike(postId) {
            $.ajax({
                url: 'toggle_like.php',
                method: 'POST',
                data: { post_id: postId },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#like-count-' + postId).text(data.like_count);
                        $('#like-button-' + postId).toggleClass('liked');
                    } else {
                        alert(data.message);
                        window.location.href = 'login.php';
                    }
                }
            });
        }

        function toggleRepost(postId) {
            $.ajax({
                url: 'toggle_repost.php',
                method: 'POST',
                data: { post_id: postId },
                success: function (response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#repost-count-' + postId).text(data.repost_count);
                        $('#repost-button-' + postId).toggleClass('reposted');
                    } else {
                        alert(data.message);
                        window.location.href = 'login.php';
                    }
                }
            });
        }

        function sharePost(postId) {
            $('#shareModal').modal('show');
            $('#shareModal').data('postId', postId);
        }

        function performShare(platform) {
            const postId = $('#shareModal').data('postId');
            if (platform === 'CopyLink') {
                const link = `https://yourwebsite.com/view_post.php?id=${postId}`;
                navigator.clipboard.writeText(link).then(() => {
                    alert("ลิงค์ถูกคัดลอกแล้ว: " + link);
                });
            } else {
                alert("แชร์โพสต์ ID: " + postId + " ไปยัง " + platform);
            }
            $('#shareModal').modal('hide');
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

                        $('#comments-list-' + postId).prepend(`
                            <li class="comment-box list-group-item">
                                <img src="uploads/${data.user.profile_image}" alt="Profile Image" class="rounded-circle" style="width: 40px; height: 40px;">
                                <strong>${data.user.username}</strong>: ${data.content.replace(/\n/g, '<br>')}
                                <small class="text-muted float-right">${datePart} ${timePart}</small> <!-- แสดงวันที่และเวลาปัจจุบัน -->
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
$(document).ready(function () {
    $('#search-input').on('input', function () {
        const query = $(this).val();
        const suggestionsList = $('#suggestions');

        if (query.length > 2) {
            $.ajax({
                url: 'search_posts.php',
                method: 'GET',
                data: { query: query },
                success: function (response) {
                    const results = JSON.parse(response);
                    suggestionsList.empty();
                    if (results.length > 0) {
                        results.forEach(post => {
                            suggestionsList.append(`
                        <div class="suggestion-item">
                            <a href="view_post.php?id=${post.id}">${post.title}</a>
                        </div>
                    `);
                        });
                    } else {
                        suggestionsList.append('<div class="suggestion-item">ไม่พบโพสต์ที่ตรงกัน</div>');
                    }
                },
                error: function () {
                    suggestionsList.empty().append('<div class="suggestion-item">เกิดข้อผิดพลาดในการค้นหา</div>');
                }
            });
        } else {
            suggestionsList.empty();
        }
    });

    $('#search-button').on('click', function () {
        const query = $('#search-input').val();
        if (query.length > 2) {
            $.ajax({
                url: 'search_posts.php',
                method: 'GET',
                data: { query: query },
                success: function (response) {
                    const results = JSON.parse(response);
                    const suggestionsList = $('#suggestions');
                    suggestionsList.empty();
                    if (results.length > 0) {
                        results.forEach(post => {
                            suggestionsList.append(`
                        <div class="suggestion-item">
                            <a href="view_post.php?id=${post.id}">${post.title}</a>
                        </div>
                    `);
                        });
                    } else {
                        suggestionsList.append('<div class="suggestion-item">ไม่พบโพสต์ที่ตรงกัน</div>');
                    }
                },
                error: function () {
                    $('#suggestions').empty().append('<div class="suggestion-item">เกิดข้อผิดพลาดในการค้นหา</div>');
                }
            });
        } else {
            $('#suggestions').empty();
        }
    });
});

$(document).ready(function () {
    // เมื่อมีการคลิกที่ document ให้ซ่อนกล่องผลลัพธ์ค้นหา
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#search-form').length) {
            $('#suggestions').hide(); // ซ่อนกล่องผลลัพธ์
        }
    });

    // เมื่อมีการคลิกที่ input ให้แสดงกล่องผลลัพธ์
    $('#search-input').on('focus', function () {
        $('#suggestions').show(); // แสดงกล่องผลลัพธ์
    });
});
//popup
function openForm() {
  document.getElementById("myForm").style.display = "block";
}

function closeForm() {
  document.getElementById("myForm").style.display = "none";
}
</script>

<img src="logo/admin.png" alt="Report" class="open-button" onclick="openForm()" style="cursor: pointer; width: 140px; height: auto;">

<div class="chat-popup" id="myForm">
  <form action="/MiniProject/action_page.php" method="post" class="form-container">
    <i class="fa-solid fa-chalkboard-user"></i>
    <h3>Report to Admin</h3>

    <textarea placeholder="ข้อความ...." name="msg" required></textarea>

    <button type="submit" class="btn">ส่ง</button>
    <button type="button" class="btn cancel" onclick="closeForm()">ปิด</button>
  </form>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">แชร์โพสต์</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>เลือกแพลตฟอร์มที่ต้องการแชร์:</p>
                <button class="btn btn-primary" onclick="performShare('Facebook')">
                    <i class="fab fa-facebook"></i> Facebook
                </button>
                <button class="btn btn-dark" onclick="performShare('X')">
                    <img src="logo/x.png" alt="X Logo" style="width: 20px; height: 20px;"> X
                </button>
                <button class="btn btn-success" onclick="performShare('Line')">
                    <i class="fab fa-line"></i> Line
                </button>
                <button class="btn btn-secondary" onclick="performShare('CopyLink')">
                    <i class="fas fa-link"></i> คัดลอกลิงค์
                </button>
            </div>
        </div>
    </div>
</div>

</body>

</html>