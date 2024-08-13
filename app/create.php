<?php
require_once 'auth.php';
requireLogin();
require_once 'csrf_token.php';
$csrf_token = generate_csrf_token();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規動画作成 | しゃべクリップ</title>
    <link rel="icon" href="./assets/images/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen m-0 bg-slate-50">
    <?php include 'components/navbar.php'; ?>
    <div class="grid grid-cols-1 md:grid-cols-2 m-0 mx-auto py-6">
        <div class="h-[80vh] w-[45vh] m-0 mx-auto my-auto relative">
            <div class="relative w-full h-auto">
                <video id="preview" autoplay muted playsinline class="absolute top-0 left-0 opacity-0 w-full h-auto"></video>
                <canvas id="canvas" class="absolute top-0 left-0 w-full h-auto"></canvas>
            </div>
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="switchCameraBtn" class="bg-gray-500 text-white px-4 py-2 rounded-md">カメラ切替</button>
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" disabled class="bg-red-500 text-white px-4 py-2 rounded-md hidden">Stop</button>
            </div>
            <div class="absolute top-2 left-0 w-full flex justify-center gap-2 z-10">
                <select id="videoSource" class="bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">ビデオソースを選択</option>
                </select>
                <select id="audioSource" class="bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">オーディオソースを選択</option>
                </select>
            </div>
        </div>
        <div class="relative w-full h-full p-4 hidden md:block">
            <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full">
                書き起こし結果の表示
            </pre>
        </div>
    </div>

    <script>
        const csrfToken = '<?php echo $csrf_token; ?>';
    </script>

    <script type="module" src="./js/main.js"></script>
</body>
</html>