import { startPreview, startRecording, stopRecording, switchCamera, changeVideoSource } from './services/recordingService.js';
import { setupEventListeners } from './dom/setup.js';

setupEventListeners(startRecording, stopRecording, switchCamera, changeVideoSource);
startPreview();