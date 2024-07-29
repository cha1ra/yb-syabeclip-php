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
                    <source src='./uploads/20240729T101803725Z.webm' type='video/webm'>
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
    "startTime": "2024-07-29T10:18:04.871Z",
    "endTime": "2024-07-29T10:18:06.266Z",
    "startOffset": 1145,
    "endOffset": 2540,
    "transcript": "こんにちは"
  },
  {
    "startTime": "2024-07-29T10:18:07.216Z",
    "endTime": "2024-07-29T10:18:09.289Z",
    "startOffset": 3490,
    "endOffset": 5563,
    "transcript": "こんにちは じゃない そうです"
  },
  {
    "startTime": "2024-07-29T10:18:11.387Z",
    "endTime": "2024-07-29T10:18:13.575Z",
    "startOffset": 7661,
    "endOffset": 9849,
    "transcript": "なかなか難しいものですね"
  },
  {
    "startTime": "2024-07-29T10:18:14.677Z",
    "endTime": "2024-07-29T10:18:20.176Z",
    "startOffset": 10951,
    "endOffset": 16450,
    "transcript": "というところで今日も難しいことを一緒にやっていこうと思います"
  }
]

document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);

const video = document.getElementById('videoElement');
const canvas = document.getElementById('videoCanvas');
const context = canvas.getContext('2d');
const seekBar = document.getElementById('seekBar');
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');

let currentTranscriptIndex = 0;
let isPlaying = false;

const draw = () => {
    if (isPlaying) {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        // テキストを描画
        const { transcript } = transcripts[currentTranscriptIndex];
        const padding = 32;
        const fontSize = 64;
        const textHeight = fontSize + padding * 2;
        context.font = `${fontSize}px Arial`;
        context.fillStyle = "rgba(0, 0, 0, 0.5)"; // 黒透過背景
        const yPosition = canvas.height * (2 / 3); // 下から1/3の位置
        context.fillRect(0, yPosition - textHeight / 2, canvas.width, textHeight); // 背景の幅はcanvas幅いっぱいに
        context.fillStyle = "white";
        context.textAlign = "center"; // 文字を中央寄せ
        context.textBaseline = "middle"; // 文字の垂直方向を中央寄せ
        context.fillText(transcript, canvas.width / 2, yPosition);
        requestAnimationFrame(draw);
    }
};

const playSegment = (index) => {
    if (index >= transcripts.length) {
        isPlaying = false;
        return;
    }

    const { startOffset, endOffset } = transcripts[index];
    video.currentTime = startOffset / 1000;
    video.play();

    const checkTime = () => {
        if (video.currentTime >= endOffset / 1000) {
            video.pause();
            currentTranscriptIndex++;
            playSegment(currentTranscriptIndex);
        } else {
            requestAnimationFrame(checkTime);
        }
    };

    checkTime();
};

startBtn.addEventListener('click', () => {
    if (!isPlaying) {
        isPlaying = true;
        currentTranscriptIndex = 0;
        playSegment(currentTranscriptIndex);
        draw();
    }
});

stopBtn.addEventListener('click', () => {
    isPlaying = false;
    video.pause();
});

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