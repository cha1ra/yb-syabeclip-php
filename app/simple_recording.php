<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen m-0">
    <div class="h-screen grid grid-cols-1 md:grid-cols-2 m-0 mx-auto">
        <div class="h-screen w-[56.25vh] m-0 mx-auto relative">
            <div class="relative w-full h-auto">
                <video id="preview" autoplay muted playsinline class="absolute top-0 left-0 opacity-0 w-full h-auto"></video>
                <canvas id="canvas" class="absolute top-0 left-0 w-full h-auto"></canvas>
            </div>
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" disabled class="bg-red-500 text-white px-4 py-2 rounded-md hidden">Stop</button>
            </div>
        </div>
        <div class="relative w-full h-screen p-4">
            <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full">
[
  {
    "startTime": "2024-07-29T03:09:27.019Z",
    "endTime": "2024-07-29T03:09:28.866Z",
    "startOffset": 1754,
    "endOffset": 3601,
    "transcript": "これでお願いします"
  },
  {
    "startTime": "2024-07-29T03:09:30.075Z",
    "endTime": "2024-07-29T03:09:31.719Z",
    "startOffset": 4810,
    "endOffset": 6454,
    "transcript": "これで行きますでしょうか"
  },
  {
    "startTime": "2024-07-29T03:09:32.849Z",
    "endTime": "2024-07-29T03:09:33.984Z",
    "startOffset": 7584,
    "endOffset": 8719,
    "transcript": "いいね そうですね"
  },
  {
    "startTime": "2024-07-29T03:09:34.943Z",
    "endTime": "2024-07-29T03:09:35.691Z",
    "startOffset": 9678,
    "endOffset": 10426,
    "transcript": "いいですね"
  }
]
            </pre>
        </div>
    </div>

    <script type="module" src="./js/main.js"></script>
</body>
</html>