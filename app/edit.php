<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1p&display=swap" rel="stylesheet">
</head>
<body class="h-screen m-0">
    <div class="h-screen grid grid-cols-1 md:grid-cols-2 m-0 mx-auto">
        <div class="h-screen w-[56.25vh] m-0 mx-auto relative">
            <div class="relative w-full h-auto">
                <video id="videoElement" width='320' height='240' controls>
                    <source src='./uploads/20240729T163749467Z.webm' type='video/webm'>
                    Your browser does not support the video tag.
                </video>
                <canvas id="videoCanvas" width="1080" height="1920" class="absolute top-0 left-0" style="max-width: 360px;"></canvas>
            </div>
            <div class="w-full h-10 bg-gray-300">
                <canvas id="audioCanvas" width="1080" height="100"></canvas>
            </div>
            <!-- <canvas id="videoCanvas" width="1080" height="1920" style="max-width: 360px;"></canvas> -->
            <div class="absolute bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                <button id="startBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md">Start</button>
                <button id="stopBtn" class="bg-red-500 text-white px-4 py-2 rounded-md">Stop</button>
            </div>
        </div>
        <div class="relative w-full h-screen p-4">
            <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full"></pre>
        </div>
    </div>

<script>
const transcripts = [
  {
    "startTime": "2024-07-29T16:37:52.211Z",
    "endTime": "2024-07-29T16:37:56.931Z",
    "startOffset": 2744,
    "endOffset": 7464,
    "transcript": "だって私がこのショート動画アプリを作った理由"
  },
  {
    "startTime": "2024-07-29T16:37:57.867Z",
    "endTime": "2024-07-29T16:37:59.576Z",
    "startOffset": 8400,
    "endOffset": 10109,
    "transcript": "についてお話をします"
  },
  {
    "startTime": "2024-07-29T16:38:02.411Z",
    "endTime": "2024-07-29T16:38:06.736Z",
    "startOffset": 12944,
    "endOffset": 17269,
    "transcript": "このショート動画アプリを作った理由は"
  },
  {
    "startTime": "2024-07-29T16:38:08.499Z",
    "endTime": "2024-07-29T16:38:10.821Z",
    "startOffset": 19032,
    "endOffset": 21354,
    "transcript": "動画の作るっていうところの"
  },
  {
    "startTime": "2024-07-29T16:38:14.650Z",
    "endTime": "2024-07-29T16:38:19.253Z",
    "startOffset": 25183,
    "endOffset": 29786,
    "transcript": "元々の意義っていうのを皆さんと考えたい"
  },
  {
    "startTime": "2024-07-29T16:38:21.077Z",
    "endTime": "2024-07-29T16:38:24.186Z",
    "startOffset": 31610,
    "endOffset": 34719,
    "transcript": "そのためにこのような仕組み っていうのを作りました"
  },
  {
    "startTime": "2024-07-29T16:38:25.918Z",
    "endTime": "2024-07-29T16:38:28.843Z",
    "startOffset": 36451,
    "endOffset": 39376,
    "transcript": "ショート動画って元々は"
  },
  {
    "startTime": "2024-07-29T16:38:32.147Z",
    "endTime": "2024-07-29T16:38:37.065Z",
    "startOffset": 42680,
    "endOffset": 47598,
    "transcript": "自分が取りたいなと思った タイミングに取れる"
  },
  {
    "startTime": "2024-07-29T16:38:39.235Z",
    "endTime": "2024-07-29T16:38:42.671Z",
    "startOffset": 49768,
    "endOffset": 53204,
    "transcript": "それが元々 証拠とかはずだったはずなんですよ"
  },
  {
    "startTime": "2024-07-29T16:38:45.015Z",
    "endTime": "2024-07-29T16:38:48.854Z",
    "startOffset": 55548,
    "endOffset": 59387,
    "transcript": "だけど今の形だと"
  },
  {
    "startTime": "2024-07-29T16:38:49.898Z",
    "endTime": "2024-07-29T16:38:52.748Z",
    "startOffset": 60431,
    "endOffset": 63281,
    "transcript": "ショート動画用の台本を練る"
  },
  {
    "startTime": "2024-07-29T16:38:55.867Z",
    "endTime": "2024-07-29T16:38:55.867Z",
    "startOffset": 66400,
    "endOffset": 66400,
    "transcript": "ショート動画用の撮影をする"
  },
  {
    "startTime": "2024-07-29T16:38:57.315Z",
    "endTime": "2024-07-29T16:38:58.361Z",
    "startOffset": 67848,
    "endOffset": 68894,
    "transcript": "そして 編集"
  },
  {
    "startTime": "2024-07-29T16:38:59.964Z",
    "endTime": "2024-07-29T16:39:07.101Z",
    "startOffset": 70497,
    "endOffset": 77634,
    "transcript": "このような形で結局動画にするまでにいっぱい時間をかけないといろんな人に見てもらえない"
  },
  {
    "startTime": "2024-07-29T16:39:08.043Z",
    "endTime": "2024-07-29T16:39:10.451Z",
    "startOffset": 78576,
    "endOffset": 80984,
    "transcript": "こんな状況があります"
  },
  {
    "startTime": "2024-07-29T16:39:11.256Z",
    "endTime": "2024-07-29T16:39:13.888Z",
    "startOffset": 81789,
    "endOffset": 84421,
    "transcript": "この状況を私は"
  },
  {
    "startTime": "2024-07-29T16:39:15.198Z",
    "endTime": "2024-07-29T16:39:15.198Z",
    "startOffset": 85731,
    "endOffset": 85731,
    "transcript": "変えたい"
  },
  {
    "startTime": "2024-07-29T16:39:18.317Z",
    "endTime": "2024-07-29T16:39:22.242Z",
    "startOffset": 88850,
    "endOffset": 92775,
    "transcript": "だからこそこんなアプリケーションを作っています"
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
    context.font = `${fontSize}px 'M PLUS 1p'`;
    context.fillStyle = "rgba(0, 0, 0, 0.5)"; // 黒透過背景
    const yPosition = canvas.height * (3 / 4); // 下から1/4の位置
    context.fillRect(0, yPosition - textHeight / 2, canvas.width, textHeight); // 背景の幅はcanvas幅いっぱいに
    context.fillStyle = "white";
    context.textAlign = "center"; // 文字を中央寄せ
    context.textBaseline = "middle"; // 文字の垂直方向を中央寄せ
    // 改行文字で分割して描画
    lines.forEach((line, index) => {
        context.fillText(line, canvas.width / 2, yPosition + index * lineHeight - (lines.length - 1) * lineHeight / 2);
    });

    // 左上にタイトルテロップを描画
    const title = "今回のテーマ"
    const titleFontSize = 36;
    const tiltlePadding = 8;

    const subTitle = "スタートアップをやってみてどうですか？";
    const subTitleFontSize = 48;
    
    context.imageSmoothingEnabled = true; // フォントをスムーズにする
    context.font = `${subTitleFontSize}px 'M PLUS 1p'`;
    const subTitleTextWidth = context.measureText(subTitle).width;
    const rectWidth = subTitleTextWidth + padding * 2;
    const rectHeight = subTitleFontSize + padding * 2;
    const rectX = padding;
    const rectY = 200;

    // 影をつける
    context.shadowColor = "rgba(0, 0, 0, 0.5)";
    context.shadowBlur = 10;
    context.shadowOffsetX = 5;
    context.shadowOffsetY = 5;


    context.textAlign = "left"; // 文字を左寄せ
    context.textBaseline = "top"; // 文字の垂直方向を中央寄せ


    context.font = `${titleFontSize}px 'M PLUS 1p'`;
    const titleTextWidth = context.measureText(title).width;
    const titleRectWidth = titleTextWidth + tiltlePadding * 2;
    const titleRectHeight = titleFontSize + tiltlePadding * 2;

    // タイトルを描画
    context.font = `${titleFontSize}px 'M PLUS 1p'`;
    context.fillStyle = "black"; // 黒背景
    context.fillRect(rectX + padding - tiltlePadding, rectY - titleFontSize - tiltlePadding * 3, titleRectWidth, titleRectHeight);
    context.fillStyle = "white";
    context.fillText(title, rectX + padding, rectY - titleFontSize - tiltlePadding * 2); 

    // 角丸の矩形を描画する関数
    function drawRoundedRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + width - radius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
        ctx.lineTo(x + width, y + height - radius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
        ctx.lineTo(x + radius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
    }

    // 角丸の矩形を描画
    const radius = 20; // 角丸の半径
    context.fillStyle = "rgba(255, 255, 255, 1)"; // 白背景
    drawRoundedRect(context, rectX, rectY, rectWidth, rectHeight, radius);
    context.fill();
    context.font = `${subTitleFontSize}px 'M PLUS 1p'`;
    context.shadowColor = "transparent"; // 影をリセット
    context.fillStyle = "black";
    context.fillText(subTitle, rectX + padding, rectY + padding); 

    requestAnimationFrame(draw);
};

const playSegment = (index) => {
    if (index >= transcripts.length) {
        isPlaying = false;
        mediaRecorder.stop();
        return;
    }

    const { startOffset, endOffset } = transcripts[index];
    video.currentTime = startOffset / 1000;

    const checkTime = () => {
        if (video.currentTime >= endOffset / 1000) {
            currentTranscriptIndex++;
            playSegment(currentTranscriptIndex);
        } else {
            requestAnimationFrame(checkTime);
        }
    };

    // シークが完了するのを待たずに再生を開始
    video.play();
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

video.addEventListener('loadeddata', () => {
    draw();
});

</script>

</body>
</html>
</html>