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
</body>
</html>