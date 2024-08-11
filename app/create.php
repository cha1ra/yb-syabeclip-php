<?php
require_once 'auth.php';
requireLogin();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen m-0 bg-slate-50">
    <div class="h-screen grid grid-cols-1 md:grid-cols-2 m-0 mx-auto">
        <div class="h-screen w-[56.25vh] m-0 mx-auto relative">
            <div class="relative w-full h-auto">
                <video id="preview" autoplay muted playsinline class="absolute top-0 left-0 opacity-0 w-full h-auto"></video>
                <canvas id="canvas" class="absolute top-0 left-0 w-full h-auto"></canvas>
            </div>
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" disabled class="bg-red-500 text-white px-4 py-2 rounded-md hidden">Stop</button>
                <select id="cameraSelect" class="bg-white text-black px-4 py-2 rounded-md">
                    <option value="">カメラを選択</option>
                </select>
            </div>
        </div>
        <div class="relative w-full h-screen p-4">
            <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full">
                書き起こし結果の表示
            </pre>
        </div>
    </div>

    <script type="module" src="./js/main.js"></script>
</body>
</html>