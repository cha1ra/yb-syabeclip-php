let mediaRecorder;
let sessionId = '';
let recognition;
let transcripts = [];
let startTime;

export async function startPreview() {
    const preview = document.getElementById('preview');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const isMobile = window.innerWidth < window.innerHeight;
    const width = 720;
    const height = 1280;
    const aspectRatio = isMobile ? height / width : width / height;

    let constraints = {
        audio: true,
        video: {
            width: { ideal: width },
            aspectRatio: aspectRatio
        }
    };

    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    preview.srcObject = stream;

    canvas.width = width;
    canvas.height = height;

    if (isMobile) {
        preview.style.transform = 'translate(-50%, -50%) rotate(90deg)';
        preview.style.transformOrigin = 'center center';
        preview.style.width = '100%';
        preview.style.height = 'auto';
        preview.style.position = 'absolute';
        preview.style.top = '50%';
        preview.style.left = '50%';
    } else {
        preview.style.width = '100%';
        preview.style.height = 'auto';
    }

    async function draw() {
        if (preview.readyState === preview.HAVE_ENOUGH_DATA) {
            ctx.save();
            ctx.scale(-1, 1);
            ctx.drawImage(preview, -canvas.width, 0, canvas.width, canvas.height);
            ctx.restore();
        }
        requestAnimationFrame(draw);
    }
    draw();
}

export async function startRecording() {
    const preview = document.getElementById('preview');
    const isMobile = window.innerWidth < window.innerHeight;
    const width = 720;
    const height = 1280;
    const aspectRatio = isMobile ? height / width : width / height;

    let constraints = {
        audio: true,
        video: {
            width: { ideal: width },
            aspectRatio: aspectRatio
        }
    };

    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    preview.srcObject = stream;

    const combinedStream = new MediaStream([...stream.getVideoTracks(), ...stream.getAudioTracks()]);
    mediaRecorder = new MediaRecorder(combinedStream);
    mediaRecorder.ondataavailable = handleDataAvailable;
    mediaRecorder.start(10000);

    sessionId = new Date().toISOString().replace(/[-:.]/g, "");
    document.getElementById('startBtn').disabled = true;
    document.getElementById('stopBtn').disabled = false;

    startSpeechRecognition();
}

function handleDataAvailable(event) {
    if (event.data.size > 0) {
        uploadChunk(event.data);
    }
}

export function stopRecording() {
    mediaRecorder.stop();
    recognition.stop();
    document.getElementById('startBtn').disabled = false;
    document.getElementById('stopBtn').disabled = true;

    mediaRecorder.onstop = () => {
        mediaRecorder.ondataavailable = handleDataAvailable;
    };

    console.log(JSON.stringify(transcripts, null, 2));
    document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);
}

async function uploadChunk(chunk) {
    const formData = new FormData();
    console.log('uploadChunk', chunk.size);
    formData.append('videoChunk', chunk, `${sessionId}.webm`);

    const response = await fetch('save.php', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        console.error('Failed to upload chunk:', response.statusText);
    } else {
        console.log('Chunk uploaded successfully');
    }
}

function startSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    // recognition.continuous = true;
    // 一時的な認識結果を無効にする
    recognition.interimResults = false;
    recognition.lang = 'ja-JP';

    recognition.onspeechstart = () => {
        console.log('speechstart');
        startTime = new Date().toISOString();
    };

    recognition.onresult = (event) => {
        const result = event.results[event.results.length - 1];
        if (result.isFinal) { // 確定になったタイミングでpush
            const transcript = result[0].transcript;
            const endTime = new Date().toISOString();
            transcripts.push({
                startTime: startTime,
                endTime: endTime,
                transcript: transcript
            });
            console.log(JSON.stringify(transcripts, null, 2));
            document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);
        }
    };

    recognition.onend = () => {
        if (document.getElementById('stopBtn').disabled === false) {
            recognition.start();
        }
    };

    recognition.start();
}