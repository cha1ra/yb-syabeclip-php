export function setupEventListeners(startRecording, stopRecording, switchCamera, changeVideoSource, changeAudioSource) {
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    const videoSource = document.getElementById('videoSource');
    const audioSource = document.getElementById('audioSource');

    startBtn.addEventListener('click', startRecording);
    stopBtn.addEventListener('click', stopRecording);
    switchCameraBtn.addEventListener('click', switchCamera);
    videoSource.addEventListener('change', (event) => changeVideoSource(event.target.value));
    audioSource.addEventListener('change', (event) => changeAudioSource(event.target.value));
}