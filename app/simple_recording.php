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
        <video id="preview" autoplay muted playsinline style="opacity: 0; width: 100%; height: auto;"></video>
        <canvas id="canvas" style="position: absolute; top: 0; left: 0;"></canvas>
    </div>
    <button id="startBtn">Start Recording</button>
    <button id="stopBtn" disabled>Stop Recording</button>
    <p>メモリ使用状況: <span id="memoryUsage">0 MB</span></p>

    <script>
        let mediaRecorder;
        let preview = document.getElementById('preview');
        let canvas = document.getElementById('canvas');
        let ctx = canvas.getContext('2d');
        let startBtn = document.getElementById('startBtn');
        let stopBtn = document.getElementById('stopBtn');
        let memoryUsageElement = document.getElementById('memoryUsage');
        let sessionId = '';

        async function startPreview() {
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

            // スマホでのプレビューを縦向きに回転
            if (isMobile) {
                preview.style.transform = 'rotate(90deg)';
                preview.style.transformOrigin = 'center center';
                preview.style.width = `${height}px`;
                preview.style.height = `${width}px`;
                preview.style.position = 'absolute';
                preview.style.top = '50%';
                preview.style.left = '50%';
                preview.style.transform = 'translate(-50%, -50%) rotate(90deg)';
            } else {
                preview.style.width = `${width}px`;
                preview.style.height = `${height}px`;
            }

            // 動画をキャンバスに描画
            async function draw() {
                if (preview.readyState === preview.HAVE_ENOUGH_DATA) {
                    ctx.save(); // 現在の描画状態を保存
                    ctx.scale(-1, 1); // 水平方向に反転
                    ctx.drawImage(preview, -canvas.width, 0, canvas.width, canvas.height); // 反転した位置に描画
                    ctx.restore(); // 描画状態を元に戻す
                }
                requestAnimationFrame(draw);
            }
            draw();
        }

        async function startRecording() {
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
            mediaRecorder.start(10000);  // Record in 10-second chunks

            sessionId = new Date().toISOString().replace(/[-:.]/g, "");
            startBtn.disabled = true;
            stopBtn.disabled = false;

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
            } else {
                memoryUsageElement.textContent = 'N/A';
            }
        }

        startBtn.addEventListener('click', startRecording);
        stopBtn.addEventListener('click', stopRecording);

        startPreview();  // プレビューを開始
    </script>
</body>
</html>