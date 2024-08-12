<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'csrf_token.php';

requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ | しゃべクリップ</title>
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'components/navbar.php'; ?>

    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-xl mb-4">作成した動画一覧</h1>
        <?php
        $videoStmt = $pdo->prepare("SELECT * FROM videos WHERE user_id = :user_id ORDER BY created_at DESC");
        $videoStmt->execute([':user_id' => $_SESSION['user_id']]);
        $videos = $videoStmt->fetchAll();

        if (count($videos) > 0) {
            echo '<ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
            foreach ($videos as $video) {
                echo '<li class="bg-white rounded-lg shadow-md p-4">';
                echo '<h3 class="text-sm font-semibold">' . htmlspecialchars($video['title'] ?? 'No Title') . '</h3>';
                echo '<p class="text text-gray-600 mb-2">' . htmlspecialchars($video['subtitle'] ?? 'No Subtitle') . '</p>';
                echo '<p class="text-gray-600 mb-2 text-xs">' . htmlspecialchars($video['created_at']) . '</p>';
                echo '<a href="edit.php?id=' . $video['id'] . '" class="text-blue-500 hover:underline mr-2">編集</a>';
                echo '<a href="#" onclick="confirmDelete(' . $video['id'] . ')" class="text-red-500 hover:underline">削除</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="text-gray-600">まだ動画がありません。</p>';
        }
        ?>
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm("本当に削除しますか？")) {
                window.location.href = "destroy.php?id=" + id + "&csrf_token=<?php echo generate_csrf_token(); ?>";
            }
        }
    </script>
</body>
</html>