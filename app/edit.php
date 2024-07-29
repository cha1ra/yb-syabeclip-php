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
                <video id="videoElement" width='320' height='240' controls>
                    <source src='./uploads/20240729T030925265Z.webm' type='video/webm'>
                    Your browser does not support the video tag.
                </video>
                <!-- <canvas id="videoCanvas" width="1920" height="1080" class="absolute top-0 left-0"></canvas> -->
            </div>
            <canvas id="videoCanvas" width="1080" height="1920" style="max-width: 360px;"></canvas>
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" disabled class="bg-red-500 text-white px-4 py-2 rounded-md hidden">Stop</button>
            </div>
            <div class="absolute bottom-10 left-0 w-full flex justify-center gap-2 z-10">
                <input type="range" id="seekBar" min="0" max="100" value="0" class="w-full">
            </div>
        </div>
        <div class="relative w-full h-screen p-4">
            <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full"></pre>
        </div>
    </div>

<script>
const transcripts = [
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

document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);

const video = document.getElementById('videoElement');
const canvas = document.getElementById('videoCanvas');
const context = canvas.getContext('2d');
const seekBar = document.getElementById('seekBar');

const draw = () => {
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    requestAnimationFrame(draw);
};

video.addEventListener('play', draw);
video.addEventListener('pause', draw);
video.addEventListener('ended', draw);
draw();

video.addEventListener('timeupdate', () => {
    const value = (100 / video.duration) * video.currentTime;
    seekBar.value = value;
});

seekBar.addEventListener('input', () => {
    const time = video.duration * (seekBar.value / 100);
    video.currentTime = time;
});
</script>

</body>
</html>