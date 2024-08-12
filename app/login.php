<?php
require_once 'db.php';
require_once 'auth.php';

initSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: mypage.php');
            exit;
        } else {
            $error = 'メールアドレスまたはパスワードが間違っています。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | しゃべクリップ</title>
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 p-4 max-w-3xl">
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="login.php" method="post" class="bg-white shadow-md rounded px-12 pt-6 pb-8 mb-4 max-w-xl mx-auto">
            <div class="mb-8 text-center">
                <img src="./assets/images/logo-text.png" alt="しゃべクリップ" class="w-64 mx-auto">
            </div>
            <div class="mb-4">
                <input class="appearance-none border rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" placeholder="メールアドレス" required>
            </div>
            <div class="mb-4">
                <input class="appearance-none border rounded w-full py-4 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" placeholder="パスワード" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-amber-500 hover:bg-amber-700 transition text-white font-bold py-4 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit">
                    ログイン
                </button>
            </div>
        </form>
    </div>
</body>
</html>