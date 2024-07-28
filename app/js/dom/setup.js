export function setupEventListeners(startRecording, stopRecording) {
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');

    startBtn.addEventListener('click', startRecording);
    stopBtn.addEventListener('click', stopRecording);
}
