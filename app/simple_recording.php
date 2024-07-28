<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
</head>
<body>
    <div style="max-width: 540px; margin: 0 auto;">
        <div style="position: relative; width: 100%; height: auto;">
            <video id="preview" autoplay muted playsinline style="position: absolute; top: 0; left: 0; opacity: 0; width: 100%; height: auto;"></video>
            <canvas id="canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: auto;"></canvas>
        </div>
        <div style="position: absolute; bottom: 0; left: 0; width: 100%; display: flex; justify-content: center; gap: 10px; z-index: 100;">
            <button id="startBtn">Start Recording</button>
            <button id="stopBtn" disabled>Stop Recording</button>
        </div>
    </div>


    <script>
        let mediaRecorder;
        let preview = document.getElementById('preview');
        let canvas = document.getElementById('canvas');
        let ctx = canvas.getContext('2d');
        let startBtn = document.getElementById('startBtn');
        let stopBtn = document.getElementById('stopBtn');
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
                preview.style.width = '100%';
                preview.style.height = 'auto';
                preview.style.position = 'absolute';
                preview.style.top = '50%';
                preview.style.left = '50%';
                preview.style.transform = 'translate(-50%, -50%) rotate(90deg)';
            } else {
                preview.style.width = '100%';
                preview.style.height = 'auto';
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

        startBtn.addEventListener('click', startRecording);
        stopBtn.addEventListener('click', stopRecording);

        startPreview();  // プレビューを開始
    </script>
</body>
</html>