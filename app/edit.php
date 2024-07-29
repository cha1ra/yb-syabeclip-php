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
                    <source src='./uploads/20240729T134055760Z.webm' type='video/webm'>
                    Your browser does not support the video tag.
                </video>
                <canvas id="videoCanvas" width="1080" height="1920" class="absolute top-0 left-0" style="max-width: 360px;"></canvas>
            </div>
            <!-- <canvas id="videoCanvas" width="1080" height="1920" style="max-width: 360px;"></canvas> -->
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" class="bg-red-500 text-white px-4 py-2 rounded-md">Stop</button>
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
    "startTime": "2024-07-29T13:40:57.469Z",
    "endTime": "2024-07-29T13:41:02.192Z",
    "startOffset": 1709,
    "endOffset": 6432,
    "transcript": "スタートアップをやってて\n苦しかった事っていうのは\nいっぱいあるわけなんですよね"
  },
  {
    "startTime": "2024-07-29T13:41:05.066Z",
    "endTime": "2024-07-29T13:41:06.082Z",
    "startOffset": 9306,
    "endOffset": 10322,
    "transcript": "やっぱりなんだろうな",
    "zoom": 1.5
  },
  {
    "startTime": "2024-07-29T13:41:07.562Z",
    "endTime": "2024-07-29T13:41:09.968Z",
    "startOffset": 11802,
    "endOffset": 14208,
    "transcript": "自分の"
  },
  {
    "startTime": "2024-07-29T13:41:13.784Z",
    "endTime": "2024-07-29T13:41:20.461Z",
    "startOffset": 18024,
    "endOffset": 24701,
    "transcript": "好きな事っていうのできるって言うのは\nやっぱりいいところだと思うんですよ\nこういう風に起業するっていう時のね"
  },
  {
    "startTime": "2024-07-29T13:41:21.404Z",
    "endTime": "2024-07-29T13:41:29.268Z",
    "startOffset": 25644,
    "endOffset": 33508,
    "transcript": "やっぱり難しいのが全部自分の責任になるって言うのも\nまた これも事実なんですよね"
  },
  {
    "startTime": "2024-07-29T13:41:37.157Z",
    "endTime": "2024-07-29T13:41:38.448Z",
    "startOffset": 36059,
    "endOffset": 42688,
    "transcript": "成功した時は全部自分のものになるし\n逆に言うと何か失敗しても全部 責任"
  },
  {
    "startTime": "2024-07-29T13:41:40.345Z",
    "endTime": "2024-07-29T13:41:45.784Z",
    "startOffset": 44585,
    "endOffset": 50024,
    "transcript": "ここがやっぱり難しいところですし\n逆に面白いところでもあるという風に\n僕は思ってますね"
  }
]

document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);

const video = document.getElementById('videoElement');
const canvas = document.getElementById('videoCanvas');
const context = canvas.getContext('2d');
const seekBar = document.getElementById('seekBar');
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const downloadBtn = document.createElement('button');
downloadBtn.textContent = 'Download';
downloadBtn.className = 'bg-green-500 text-white px-4 py-2 rounded-md';
document.body.appendChild(downloadBtn);

const progress = document.createElement('progress');
progress.max = 100;
progress.value = 0;
document.body.appendChild(progress);

let currentTranscriptIndex = 0;
let isPlaying = false;
let mediaRecorder;
let recordedChunks = [];

const draw = () => {
    if (isPlaying) {
        const { transcript, zoom = 1 } = transcripts[currentTranscriptIndex];


        // 動画をズームして描画
        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;
        const zoomedWidth = videoWidth / zoom;
        const zoomedHeight = videoHeight / zoom;
        const offsetX = (videoWidth - zoomedWidth) / 2;
        const offsetY = (videoHeight - zoomedHeight) / 2;
        context.drawImage(video, offsetX, offsetY, zoomedWidth, zoomedHeight, 0, 0, canvas.width, canvas.height);


        const padding = 32;
        const fontSize = 64;
        const lines = transcript.split('\n');
        const lineHeight = fontSize + padding;
        const textHeight = lineHeight * lines.length;
        context.font = `${fontSize}px Arial`;
        context.fillStyle = "rgba(0, 0, 0, 0.5)"; // 黒透過背景
        const yPosition = canvas.height * (2 / 3); // 下から1/3の位置
        context.fillRect(0, yPosition - textHeight / 2, canvas.width, textHeight); // 背景の幅はcanvas幅いっぱいに
        context.fillStyle = "white";
        context.textAlign = "center"; // 文字を中央寄せ
        context.textBaseline = "middle"; // 文字の垂直方向を中央寄せ



        // 改行文字で分割して描画
        lines.forEach((line, index) => {
            context.fillText(line, canvas.width / 2, yPosition + index * lineHeight - (lines.length - 1) * lineHeight / 2);
        });

        requestAnimationFrame(draw);
    }
};

const playSegment = (index) => {
    if (index >= transcripts.length) {
        isPlaying = false;
        mediaRecorder.stop();
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

const playSegmentForRecording = (index) => {
    if (index >= transcripts.length) {
        isPlaying = false;
        mediaRecorder.stop();
        return;
    }

    const { startOffset, endOffset } = transcripts[index];
    video.currentTime = startOffset / 1000;
    video.play();

    const checkTime = () => {
        if (video.currentTime >= endOffset / 1000) {
            currentTranscriptIndex++;
            playSegmentForRecording(currentTranscriptIndex);
        } else {
            requestAnimationFrame(checkTime);
        }
    };

    checkTime();
};

const bgm = new Audio('./assets/bgm/Juno.mp3');
bgm.volume = 0.1;

startBtn.addEventListener('click', () => {
    if (!isPlaying) {
        isPlaying = true;
        currentTranscriptIndex = 0;
        playSegment(currentTranscriptIndex);
        draw();
        bgm.play(); // BGMを再生
    }
});

stopBtn.addEventListener('click', () => {
    isPlaying = false;
    video.pause();
    bgm.pause(); // BGMを停止
    bgm.currentTime = 0; // BGMを最初に戻す
});

downloadBtn.addEventListener('click', () => {
    const videoStream = video.captureStream(30); // フレームレートを30に設定
    const canvasStream = canvas.captureStream(30); // フレームレートを30に設定
    const audioTrack = videoStream.getAudioTracks()[0];

    const audioContext = new AudioContext();
    const destination = audioContext.createMediaStreamDestination();
    
    // BGMのソースノードを作成
    const bgmSourceNode = audioContext.createMediaElementSource(bgm);
    bgmSourceNode.connect(destination);
    bgmSourceNode.connect(audioContext.destination); // BGMを再生するために接続

    // 動画のオーディオトラックをオーディオコンテキストに接続
    const videoSourceNode = audioContext.createMediaStreamSource(new MediaStream([audioTrack]));
    videoSourceNode.connect(destination);

    const mixedStream = new MediaStream([...canvasStream.getVideoTracks(), ...destination.stream.getAudioTracks()]);

    mediaRecorder = new MediaRecorder(mixedStream, { mimeType: 'video/webm; codecs=vp9,opus', videoBitsPerSecond: 2500000 });
    mediaRecorder.ondataavailable = (event) => {
        if (event.data.size > 0) {
            recordedChunks.push(event.data);
        }
    };
    mediaRecorder.onstop = () => {
        const blob = new Blob(recordedChunks, { type: 'video/webm' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'recording.webm';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    };

    mediaRecorder.start();
    isPlaying = true; // 録画中は再生状態にする
    currentTranscriptIndex = 0;
    playSegmentForRecording(currentTranscriptIndex);
    draw();
    bgm.play(); // BGMを再生
});

video.addEventListener('timeupdate', () => {
    const value = (100 / video.duration) * video.currentTime;
    seekBar.value = value;

    // 録画の進行状況を更新
    const totalDuration = transcripts.reduce((acc, transcript) => acc + (transcript.endOffset - transcript.startOffset), 0);
    const currentDuration = transcripts.slice(0, currentTranscriptIndex).reduce((acc, transcript) => acc + (transcript.endOffset - transcript.startOffset), 0) + (video.currentTime * 1000 - transcripts[currentTranscriptIndex].startOffset);
    progress.value = (currentDuration / totalDuration) * 100;
});

seekBar.addEventListener('input', () => {
    const time = video.duration * (seekBar.value / 100);
    video.currentTime = time;
});

draw()
</script>

</body>
</html>
</html>