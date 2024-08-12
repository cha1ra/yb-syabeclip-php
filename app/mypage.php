<?php
require_once 'db.php';
require_once 'auth.php';

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
    <title>マイページ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-2xl mb-4">マイページ</h1>
        <p class="mb-4">ようこそ、<?php echo htmlspecialchars($user['name']); ?>さん</p>
        <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">ログアウト</a>
    </div>
    <div class="container mx-auto mt-8 p-4">
        <h2 class="text-xl mb-4">あなたの動画一覧</h2>
        <?php
        $videoStmt = $pdo->prepare("SELECT * FROM videos WHERE user_id = :user_id ORDER BY created_at DESC");
        $videoStmt->execute([':user_id' => $_SESSION['user_id']]);
        $videos = $videoStmt->fetchAll();

        if (count($videos) > 0) {
            echo '<ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
            foreach ($videos as $video) {
                echo '<li class="bg-white rounded-lg shadow-md p-4">';
                echo '<h3 class="text-lg font-semibold mb-2">' . htmlspecialchars($video['title']) . '</h3>';
                echo '<p class="text-gray-600 mb-2">' . htmlspecialchars($video['subtitle']) . '</p>';
                echo '<a href="edit.php?id=' . $video['id'] . '" class="text-blue-500 hover:underline">動画を見る</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="text-gray-600">まだ動画がありません。</p>';
        }
        ?>
    </div>
</body>
</html>