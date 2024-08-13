let mediaRecorder;
let sessionId = '';
let recognition;
let transcripts = [];
let startTime;
let recordingStartTime; // 撮影開始日時を保持する変数を追加

let currentStream;
let facingMode = 'user';

export async function startPreview() {
    const preview = document.getElementById('preview');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const width = 720;
    const height = 1280;
    const aspectRatio = isMobile ? height / width : width / height;

    await updateVideoAndAudioSourceList();

    const savedVideoSource = localStorage.getItem('selectedVideoSource');
    const savedAudioSource = localStorage.getItem('selectedAudioSource');

    let constraints = {
        audio: savedAudioSource ? { deviceId: { exact: savedAudioSource } } : true,
        video: {
            width: { ideal: width },
            aspectRatio: aspectRatio,
            facingMode: facingMode
        }
    };

    if (savedVideoSource) {
        constraints.video.deviceId = { exact: savedVideoSource };
    }

    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        preview.srcObject = currentStream;

        // オーディオソースを適用
        if (savedAudioSource) {
            const audioStream = await navigator.mediaDevices.getUserMedia({ audio: { deviceId: { exact: savedAudioSource } } });
            const audioTrack = audioStream.getAudioTracks()[0];
            const oldAudioTrack = currentStream.getAudioTracks()[0];
            if (oldAudioTrack) {
                currentStream.removeTrack(oldAudioTrack);
            }
            currentStream.addTrack(audioTrack);
        }

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
    } catch (error) {
        console.error('Error accessing media devices:', error);
    }
}

async function updateVideoAndAudioSourceList() {
    const videoSource = document.getElementById('videoSource');
    const audioSource = document.getElementById('audioSource');
    videoSource.innerHTML = '<option value="">ビデオソースを選択</option>';
    audioSource.innerHTML = '<option value="">オーディオソースを選択</option>';

    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        const audioDevices = devices.filter(device => device.kind === 'audioinput');

        const savedVideoSource = localStorage.getItem('selectedVideoSource');
        const savedAudioSource = localStorage.getItem('selectedAudioSource');

        videoDevices.forEach(device => {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.text = device.label || `カメラ ${videoSource.options.length}`;
            videoSource.appendChild(option);
            if (device.deviceId === savedVideoSource) {
                option.selected = true;
            }
        });

        audioDevices.forEach(device => {
            const option = document.createElement('option');
            option.value = device.deviceId;
            option.text = device.label || `マイク ${audioSource.options.length}`;
            audioSource.appendChild(option);
            if (device.deviceId === savedAudioSource) {
                option.selected = true;
            }
        });

        // 保存されたソースを適用
        if (savedVideoSource) {
            changeVideoSource(savedVideoSource);
        } else if (videoDevices.length > 0) {
            changeVideoSource(videoDevices[0].deviceId);
        }
        if (savedAudioSource) {
            changeAudioSource(savedAudioSource);
        } else if (audioDevices.length > 0) {
            changeAudioSource(audioDevices[0].deviceId);
        }
    } catch (error) {
        console.error('Error enumerating devices:', error);
    }
}

export function switchCamera() {
    facingMode = facingMode === 'user' ? 'environment' : 'user';
    startPreview();
}

export function changeVideoSource(deviceId) {
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
    const width = 720;
    const height = 1280;
    const aspectRatio = isMobile ? height / width : width / height;

    const constraints = {
        audio: true,
        video: {
            deviceId: { exact: deviceId },
            width: { ideal: width },
            height: { ideal: height },
            aspectRatio: aspectRatio
        }
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            currentStream = stream;
            const preview = document.getElementById('preview');
            preview.srcObject = stream;

            // プレビューのスタイルを更新
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

            // 選択されたソースをローカルストレージに保存
            localStorage.setItem('selectedVideoSource', deviceId);
        })
        .catch(error => {
            console.error('ビデオソースの変更エラー:', error);
        });
}

export function changeAudioSource(deviceId) {
    const constraints = {
        audio: { deviceId: { exact: deviceId } }
    };
    navigator.mediaDevices.getUserMedia(constraints)
        .then(stream => {
            const audioTracks = currentStream.getAudioTracks();
            audioTracks.forEach(track => track.stop());
            currentStream.removeTrack(audioTracks[0]);
            currentStream.addTrack(stream.getAudioTracks()[0]);

            // 選択されたソースをローカルストレージに保存
            localStorage.setItem('selectedAudioSource', deviceId);
        })
        .catch(error => {
            console.error('オーディオソースの変更エラー:', error);
        });
}

export async function startRecording() {
    const preview = document.getElementById('preview');
    
    // 在のストリームを使用
    const videoStream = preview.captureStream(30); // 30fpsを指定
    const combinedStream = new MediaStream([...videoStream.getVideoTracks(), ...currentStream.getAudioTracks()]);
    
    const options = { mimeType: 'video/webm; codecs=vp9', videoBitsPerSecond: 2500000 };
    mediaRecorder = new MediaRecorder(combinedStream, options);
    mediaRecorder.ondataavailable = handleDataAvailable;
    mediaRecorder.start(10000);

    sessionId = new Date().toISOString().replace(/[-:.]/g, "");
    recordingStartTime = new Date(); // 撮影開始日時を設定
    document.getElementById('startBtn').disabled = true;
    document.getElementById('startBtn').classList.add('hidden');
    document.getElementById('stopBtn').disabled = false;
    document.getElementById('stopBtn').classList.remove('hidden');

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
    document.getElementById('startBtn').classList.remove('hidden');
    document.getElementById('stopBtn').disabled = true;
    document.getElementById('stopBtn').classList.add('hidden');

    mediaRecorder.onstop = () => {
        mediaRecorder.ondataavailable = handleDataAvailable;
    };

    console.log(JSON.stringify(transcripts, null, 2));
    document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);

    // データベースに結果を保存 する関数を実行
    storeData(`${sessionId}.webm`, transcripts);
}

async function storeData(sessionId, transcripts) {
    const formData = new FormData();
    formData.append('src', sessionId);
    formData.append('transcripts', JSON.stringify(transcripts));
    formData.append('csrf_token', csrfToken);

    const response = await fetch('store.php', {
        method: 'POST',
        body: formData
    });

    if (!response.ok) {
        console.error('Failed to store data:', response.statusText);
    } else {
        const responseData = await response.json();
        if (responseData.error) {
            console.error('Error storing data:', responseData.error);
        } else {
            console.log('Data stored successfully:', responseData);
        }
    }
}

async function uploadChunk(chunk) {
    const formData = new FormData();
    console.log('uploadChunk', chunk.size);
    formData.append('videoChunk', chunk, `${sessionId}.webm`);
    formData.append('csrf_token', csrfToken);

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

const generateUUID = () => {
    const now = Date.now().toString(16);
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    }) + '-' + now;
};

function startSpeechRecognition() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.interimResults = true; // 一時的な認識結果を有効にする
    recognition.lang = 'ja-JP';

    // 一時的な結果を基に文節ごとの開始・終了時間を保持する
    let phrases = [];
    let isFirstPhraseAppeared = false; // 初めて文節が出力されかどうかを保持する変数

    recognition.onspeechstart = () => {
        console.log('speechstart');
        startTime = new Date().toISOString();
    };

    recognition.onresult = (event) => {

        // transcriptの開始offset
        const offsetTime = new Date(startTime).getTime() - recordingStartTime.getTime();

        const handleFirstPhrase = () => {
            isFirstPhraseAppeared = true;
            const newPhrase = event.results[0][0].transcript;
            const endTime = new Date().toISOString();
            const endOffset = new Date(endTime).getTime() - recordingStartTime.getTime();
            phrases.push({
                text: newPhrase,
                transcriptStartOffset: 0,
                transcriptEndOffset: endOffset - offsetTime
            });
        };

        const handleNewPhrase = () => {
            const currentTranscript = event.results[0][0].transcript;
            const totalLength = phrases.reduce((sum, phrase) => sum + phrase.text.length, 0);
            const newPhrase = currentTranscript.slice(totalLength);
            const previousPhraseEndOffset = phrases[phrases.length - 1].transcriptEndOffset;
            const endTime = new Date().toISOString();
            const endOffset = new Date(endTime).getTime() - recordingStartTime.getTime();
            phrases.push({
                text: newPhrase,
                transcriptStartOffset: previousPhraseEndOffset + 1,
                transcriptEndOffset: endOffset - offsetTime
            });
        };

        // 文節ごとに区切った結果表示を擬似的に実現するロック
        // 1. event.results.length が 初めて2 になった場合、初めて文節が出力されたと判断する
        // 2. その後、event.results.length が 1になるタイミングで再度保持る
        if (event.results.length === 2 && !isFirstPhraseAppeared) {
            handleFirstPhrase();
        }
        if (isFirstPhraseAppeared && event.results.length === 1) {
            handleNewPhrase();
        }

        const result = event.results[event.results.length - 1];
        if (result.isFinal) { // 確定になったタイミングでpush
            const transcript = result[0].transcript;
            const endTime = new Date().toISOString();
            const startOffset = new Date(startTime).getTime() - recordingStartTime.getTime();
            const endOffset = new Date(endTime).getTime() - recordingStartTime.getTime();
            transcripts.push({
                uuid: generateUUID(),
                startOffset: startOffset,
                endOffset: endOffset,
                transcript: transcript,
                phrases: phrases
            });

            console.log(JSON.stringify(transcripts, null, 2));
            document.getElementById('jsonDisplay').textContent = JSON.stringify(transcripts, null, 2);

            // 次に文節を保持するめに初期化
            phrases = [];
            isFirstPhraseAppeared = false;
        }
    };

    recognition.onend = () => {
        if (document.getElementById('stopBtn').disabled === false) {
            recognition.start();
        }
    };

    recognition.start();
}