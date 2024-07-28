import { startPreview, startRecording, stopRecording } from './services/recordingService.js';
import { setupEventListeners } from './dom/setup.js';

setupEventListeners(startRecording, stopRecording);
startPreview();
