<!DOCTYPE html>
<html lang="en">
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
        "startTime": "2024-07-28T15:40:34.671Z",
        "endTime": "2024-07-28T15:40:35.863Z",
        "transcript": "さて"
    },
    {
        "startTime": "2024-07-28T15:40:36.847Z",
        "endTime": "2024-07-28T15:40:39.438Z",
        "transcript": "これで話してみようかな というところなんですけども"
    },
    {
        "startTime": "2024-07-28T15:40:40.632Z",
        "endTime": "2024-07-28T15:40:42.343Z",
        "transcript": "でこれ どうかなと"
    },
    {
        "startTime": "2024-07-28T15:40:45.192Z",
        "endTime": "2024-07-28T15:40:49.298Z",
        "transcript": "いう感じで これでうまくいければ"
    },
    {
        "startTime": "2024-07-28T15:40:50.125Z",
        "endTime": "2024-07-28T15:40:53.102Z",
        "transcript": "だいぶ 取るのが速くなるかな という風に思ってますと"
    },
    {
        "startTime": "2024-07-28T15:41:02.273Z",
        "endTime": "2024-07-28T15:41:04.374Z",
        "transcript": "タイトルの方が早くなるかな と思ってます"
    },
    {
        "startTime": "2024-07-28T15:41:19.586Z",
        "endTime": "2024-07-28T15:41:22.175Z",
        "transcript": "これでどうかなと思ってますが どうでしょうか"
    }
]
            </pre>
        </div>
    </div>

    <script type="module" src="./js/main.js"></script>
</body>
</html>