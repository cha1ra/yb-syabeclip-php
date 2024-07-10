<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
</head>
<body>
    <h1>Video Recorder</h1>
    <div style="position: relative;">
        <video id="preview" autoplay muted style="opacity: 0; width: 100%; height: auto;"></video>
        <canvas id="canvas" style="position: absolute; top: 0; left: 0;"></canvas>
    </div>
    <button id="startBtn">Start Recording</button>
    <button id="stopBtn" disabled>Stop Recording</button>
    <p>メモリ使用状況: <span id="memoryUsage">0 MB</span></p>
    <label for="audioSource">音声ソース:</label>
    <select id="audioSource"></select>
    <label for="videoSource">ビデオソース:</label>
    <select id="videoSource"></select>
    <label for="videoQuality">ビデオ品質:</label>
    <select id="videoQuality">
        <option value="low">低画質</option>
        <option value="hd">高画質</option>
        <option value="square">正方形</option>
        <option value="portrait">スマホ動画</option>
    </select>

    <script src="./js/face-api.min.js"></script>
    <script>
        let mediaRecorder;
        let preview = document.getElementById('preview');
        let canvas = document.getElementById('canvas');
        let ctx = canvas.getContext('2d');
        let startBtn = document.getElementById('startBtn');
        let stopBtn = document.getElementById('stopBtn');
        let memoryUsageElement = document.getElementById('memoryUsage');
        let audioSource = document.getElementById('audioSource');
        let videoSource = document.getElementById('videoSource');
        let videoQuality = document.getElementById('videoQuality');
        let sessionId = '';

        async function loadModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
        }

        function loadSettings() {
            if (localStorage.getItem('audioSource')) {
                audioSource.value = localStorage.getItem('audioSource');
            }
            if (localStorage.getItem('videoSource')) {
                videoSource.value = localStorage.getItem('videoSource');
            }
            if (localStorage.getItem('videoQuality')) {
                videoQuality.value = localStorage.getItem('videoQuality');
            }
        }

        function saveSettings() {
            localStorage.setItem('audioSource', audioSource.value);
            localStorage.setItem('videoSource', videoSource.value);
            localStorage.setItem('videoQuality', videoQuality.value);
        }

        async function getDevices() {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const audioDevices = devices.filter(device => device.kind === 'audioinput');
            const videoDevices = devices.filter(device => device.kind === 'videoinput');

            audioDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Microphone ${audioSource.length + 1}`;
                audioSource.appendChild(option);
            });

            videoDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Camera ${videoSource.length + 1}`;
                videoSource.appendChild(option);
            });

            loadSettings();
        }

        async function startPreview() {
            const audioSourceId = audioSource.value;
            const videoSourceId = videoSource.value;
            const quality = videoQuality.value;
            let constraints = {
                audio: { deviceId: audioSourceId ? { exact: audioSourceId } : undefined },
                video: { deviceId: videoSourceId ? { exact: videoSourceId } : undefined }
            };

            switch (quality) {
                case 'hd':
                    constraints.video.width = { ideal: 1280 };
                    constraints.video.height = { ideal: 720 };
                    canvas.width = 1280;
                    canvas.height = 720;
                    preview.style.width = '1280px';
                    preview.style.height = '720px';
                    break;
                case 'square':
                    constraints.video.width = { ideal: 720 };
                    constraints.video.height = { ideal: 720 };
                    canvas.width = 720;
                    canvas.height = 720;
                    preview.style.width = '720px';
                    preview.style.height = '720px';
                    break;
                case 'portrait':
                    constraints.video.width = { ideal: 720 };
                    constraints.video.height = { ideal: 1280 };
                    canvas.width = 720;
                    canvas.height = 1280;
                    preview.style.width = '720px';
                    preview.style.height = '1280px';
                    break;
                default:
                    constraints.video.width = { ideal: 320 };
                    constraints.video.height = { ideal: 240 };
                    canvas.width = 320;
                    canvas.height = 240;
                    preview.style.width = '320px';
                    preview.style.height = '240px';
            }

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            preview.srcObject = stream;

            // 動画をキャンバスに描画
            async function draw() {
                if (preview.readyState === preview.HAVE_ENOUGH_DATA) {
                    const videoAspectRatio = preview.videoWidth / preview.videoHeight;
                    const canvasAspectRatio = canvas.width / canvas.height;
                    let sx, sy, sWidth, sHeight;

                    if (videoAspectRatio > canvasAspectRatio) {
                        sHeight = preview.videoHeight;
                        sWidth = sHeight * canvasAspectRatio;
                        sx = (preview.videoWidth - sWidth) / 2;
                        sy = 0;
                    } else {
                        sWidth = preview.videoWidth;
                        sHeight = sWidth / canvasAspectRatio;
                        sx = 0;
                        sy = (preview.videoHeight - sHeight) / 2;
                    }

                    ctx.drawImage(preview, sx, sy, sWidth, sHeight, 0, 0, canvas.width, canvas.height);

                    // 顔認識
                    const detections = await faceapi.detectAllFaces(preview, new faceapi.TinyFaceDetectorOptions());
                    if (detections.length > 0) {
                        console.log(canvas, detections)
                        faceapi.draw.drawDetections(canvas, detections);
                    } else {
                        console.log('No detections');
                    }
                }
                // 100msまってから再描画
                requestAnimationFrame(draw);
            }
            draw();
        }

        async function startRecording() {
            const audioSourceId = audioSource.value;
            const videoSourceId = videoSource.value;
            const quality = videoQuality.value;
            let constraints = {
                audio: { deviceId: audioSourceId ? { exact: audioSourceId } : undefined },
                video: { deviceId: videoSourceId ? { exact: videoSourceId } : undefined }
            };

            switch (quality) {
                case 'hd':
                    constraints.video.width = { ideal: 1280 };
                    constraints.video.height = { ideal: 720 };
                    break;
                case 'square':
                    constraints.video.width = { ideal: 720 };
                    constraints.video.height = { ideal: 720 };
                    break;
                case 'portrait':
                    constraints.video.width = { ideal: 720 };
                    constraints.video.height = { ideal: 1280 };
                    break;
                default:
                    constraints.video.width = { ideal: 320 };
                    constraints.video.height = { ideal: 240 };
            }

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            preview.srcObject = stream;

            const combinedStream = new MediaStream([...stream.getVideoTracks(), ...stream.getAudioTracks()]);
            mediaRecorder = new MediaRecorder(combinedStream);
            mediaRecorder.ondataavailable = handleDataAvailable;
            mediaRecorder.start(10000);  // Record in 10-second chunks

            sessionId = new Date().toISOString().replace(/[-:.]/g, "");
            startBtn.disabled = true;
            stopBtn.disabled = false;
            videoQuality.disabled = true;

            // メモリ使用状況の表示を開始
            setInterval(updateMemoryUsage, 1000);
        }

        function handleDataAvailable(event) {
            if (event.data.size > 0) {
                uploadChunk(event.data);  // Upload each chunk to the server
            }
        }

        function stopRecording() {
            mediaRecorder.stop();
            startBtn.disabled = false;
            stopBtn.disabled = true;
            videoQuality.disabled = false;

            // 最後のチャンクを保存
            mediaRecorder.onstop = () => {
                mediaRecorder.ondataavailable = handleDataAvailable;
            };
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

        function updateMemoryUsage() {
            if (performance.memory) {
                console.log('メモリ更新');
                const usedJSHeapSize = performance.memory.usedJSHeapSize / 1024 / 1024;  // Convert to MB
                memoryUsageElement.textContent = `${Math.round(usedJSHeapSize)} MB`;
            }
        }

        startBtn.addEventListener('click', () => {
            startRecording();
            saveSettings();
        });
        stopBtn.addEventListener('click', stopRecording);
        videoQuality.addEventListener('change', startPreview);

        getDevices();
        loadModels().then(startPreview);  // モデルをロードしてからプレビューを開始
    </script>
</body>
</html>