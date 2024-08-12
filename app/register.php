<?php
require_once 'db.php';
require_once 'auth.php';

// 許可されたIPアドレスのリスト
$allowed_ips = ['153.124.188.220','1.33.106.162']; // ここに許可するIPアドレスを追加

// クライアントのIPアドレスを取得
$client_ip = $_SERVER['REMOTE_ADDR'];

// クライアントのIPアドレスが許可リストにない場合、アクセスを拒否
if (!in_array($client_ip, $allowed_ips)) {
    header('HTTP/1.0 403 Forbidden');
    echo "IP:".$client_ip."<br>";
    echo 'アクセスが拒否されました。';
    exit;
}

initSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = '全てのフィールドを入力してください。';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '有効なメールアドレスを入力してください。';
    } elseif (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $error = 'パスワードは8文字以上で、少なくとも1つの記号を含む必要があります。';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $error = 'このメールアドレスは既に登録されています。';
        } else {
            $hashedPassword = hashPassword($password);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashedPassword
            ]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header('Location: mypage.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録 | しゃべクリップ</title>
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-8 p-4">
        <h1 class="text-2xl mb-4">ユーザー登録</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="register.php" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    名前
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    メールアドレス
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    パスワード
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required pattern="(?=.*[!@#$%^&*(),.?\:{}|<>]).{8,}" title="8文字以上で、少なくとも1つの記号を含む必要があります。">
                <p class="text-sm text-gray-600">パスワードは8文字以上で、少なくとも1つの記号を含む必要があります。</p>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    登録
                </button>
            </div>
        </form>
    </div>
</body>
</html>