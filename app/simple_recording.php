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

    <script type="module" src="./js/main.js"></script>
</body>
</html>