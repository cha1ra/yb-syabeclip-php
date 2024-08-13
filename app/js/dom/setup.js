export function setupEventListeners(startRecording, stopRecording, switchCamera, changeVideoSource) {
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    const videoSource = document.getElementById('videoSource');

    startBtn.addEventListener('click', startRecording);
    stopBtn.addEventListener('click', stopRecording);
    switchCameraBtn.addEventListener('click', switchCamera);
    videoSource.addEventListener('change', (event) => changeVideoSource(event.target.value));
}