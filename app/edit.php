<?php
require_once 'db.php';
// パラメータを取得
$id = $_GET['id'];
// パラメータがない場合は404エラー
if (!$id) {
    http_response_code(404);
    echo "404 Not Found";
    exit;
}
// SQLクエリを準備
$sql = "SELECT * FROM videos WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$video = $stmt->fetch(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Recorder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+1p&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/wavesurfer.js@7"></script>
    <script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/timeline.min.js"></script>
    <script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/zoom.min.js"></script>
    <script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/regions.min.js"></script>
    <script src="https://unpkg.com/wavesurfer.js@7/dist/plugins/hover.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

</head>
<body class="h-screen m-0 bg-slate-50">
    <div id="app" class="h-screen grid grid-cols-2 lg:grid-cols-3">
        <div class="h-screen col-span-1 p-4">
            <!-- <div class="h-screen w-[56.25vh] my-0 mx-auto relative"> -->
            <div>

                <div class="relative h-auto mx-auto mb-4" style="width: 360px; height: 640px;">
                    <video id="videoElement" ref="videoRef" width='320' height='240' controls>
                        <source src='./uploads/<?php echo $video['src']; ?>' type='video/webm'>
                        Your browser does not support the video tag.
                    </video>
                    <canvas id="videoCanvas" width="1080" height="1920" class="absolute top-0 left-0" style="max-width: 360px;"></canvas>
                </div>
    
                <div class="bottom-2 left-0 w-full flex justify-center gap-2 z-10">
                    <!-- BGMセレクトボックス -->
                    <select v-model="selectedBgm" class="bg-white border border-gray-300 rounded-md px-2 py-1">
                        <option value="">BGMなし</option>
                        <option value="Juno.mp3">Juno</option>
                        <!-- 他のBGMオプションをここに追加 -->
                    </select>
                    <!-- 再生速度 -->
                    <select v-model="playbackSpeed" @change="changePlaybackSpeed" class="bg-white border border-gray-300 rounded-md px-2 py-1">
                        <option value="0.5">0.5x</option>
                        <option value="0.75">0.75x</option>
                        <option value="1" selected>1x</option>
                        <option value="1.25">1.25x</option>
                        <option value="1.5">1.5x</option>
                        <option value="2">2x</option>
                    </select>
                    <!-- 再生 Start -->
                    <button @click="startPlayback" class="bg-slate-700 text-white px-4 py-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.841Z" />
                        </svg>
                    </button>
                    <!-- 一時停止 Pause -->
                    <button @click="pausePlayback" class="bg-slate-700 text-white px-4 py-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path d="M5.75 3a.75.75 0 0 0-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 0 0 .75-.75V3.75A.75.75 0 0 0 7.25 3h-1.5ZM12.75 3a.75.75 0 0 0-.75.75v12.5c0 .414.336.75.75.75h1.5a.75.75 0 0 0 .75-.75V3.75a.75.75 0 0 0-.75-.75h-1.5Z" />
                        </svg>
                    </button>
                    <!-- 停止 Stop -->
                    <button @click="stopPlayback" class="bg-red-500 text-white px-4 py-2 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path d="M5.25 3A2.25 2.25 0 0 0 3 5.25v9.5A2.25 2.25 0 0 0 5.25 17h9.5A2.25 2.25 0 0 0 17 14.75v-9.5A2.25 2.25 0 0 0 14.75 3h-9.5Z" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4 flex items-center space-x-4">
                    <button ref="downloadBtnRef" class="bg-green-500 text-white px-4 py-2 rounded-md">
                        DL
                    </button>
                    <progress ref="recordingProgressRef" max="100" value="0" class="w-full"></progress>
                </div>
            </div>
        </div>
        <div class="relative w-full h-screen p-4 col-span-1 lg:col-span-2">
            <div class="mb-4">
                <label for="titleInput" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="titleInput" v-model="title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="subtitleInput" class="block text-sm font-medium text-gray-700">Subtitle</label>
                <input type="text" id="subtitleInput" v-model="subTitle" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div>
                <div id="waveform"></div>
                <div id="wave-timeline"></div>
                <input type="range" id="zoomSlider" min="1" max="200" v-model="zoomLevel" class="w-full mt-4">
            </div>
            <div>
                現在のビデオの時間: {{ videoRef?.currentTime }}
            </div>
            <div>
                currentTranscriptIndex: {{ currentTranscriptIndex }} / {{ clips.length }}
            </div>
            <div id="clips" class="overflow-y-auto h-[calc(50vh)] border rounded-md p-4 mb-4 bg-white" tabindex="0" @keydown="handleKeyDown">
                <div 
                    v-for="(clip, index) in clips" 
                    :key="clip.uuid" 
                    :data-startOffset="clip.startOffset" 
                    :data-endOffset="clip.endOffset" 
                    class="bg-slate-50 border px-4 py-2 rounded-md mb-2 text-sm cursor-pointer" 
                    :class="{'bg-yellow-200': currentTranscriptIndex === index}"
                    @click="selectClip(clip)"
                >
                    <div class="mb-2">
                        <div v-show="currentTranscriptIndex === index">
                            <textarea :value="clip.transcript" @input="updateTranscript(index, $event.target.value)" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            <div class="mt-2 flex flex-wrap items-center gap-2 relative">
                                <template v-for="(phrase, phraseIndex) in clip.phrases" :key="phrase.transcriptStartOffset">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 cursor-pointer" @click="selectPhrase(clip, phrase)">
                                        {{ phrase.text }}
                                    </span>
                                    <div v-if="phraseIndex < clip.phrases.length - 1" class="h-6 w-0.5 bg-gray-300 mx-1 relative group">
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden group-hover:block bg-white shadow-md rounded px-2 py-1 text-xs">
                                            <button @click="splitClipAtPhrase(clip, phraseIndex)" class="text-blue-600 hover:text-blue-800">
                                                分割
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-show="currentTranscriptIndex !== index">
                            {{ clip.transcript }}
                        </div>
                    </div>
                    <div class="flex gap-2 items-center" v-if="currentTranscriptIndex === index">
                        <button @click="playClip(clip)" class="rounded bg-slate-700 text-white px-2 py-1 shadow-sm">
                            クリップだけ再生
                        </button>
                        <div class="border-l border-slate-700 h-full mx-3" style="height: 20px;"></div>
                        <button @click="duplicateClip(clip)" class="rounded bg-slate-700 text-white px-2 py-1 shadow-sm">
                            複製
                        </button>
                        <button @click="splitClip(clip)" class="rounded bg-slate-700 text-white px-2 py-1 shadow-sm">
                            分割
                        </button>
                        <button @click="deleteClip(clip)" class="rounded bg-slate-700 text-white px-2 py-1 shadow-sm">
                            削除
                        </button>
                        <div class="border-l border-slate-700 h-full mx-3" style="height: 20px;"></div>
                        <button @click="toggleZoom(clip)" 
                                :class="{'bg-yellow-900': !!clip.zoom && clip.zoom > 1, 'bg-slate-700': !clip.zoom || clip.zoom <= 1}" 
                                class="rounded text-white px-2 py-1 shadow-sm">
                            ズーム
                        </button>
                        <div class="border-l border-slate-700 h-full mx-3" style="height: 20px;"></div>
                        <button 
                            class="rounded text-white px-2 py-1 shadow-sm" 
                            @click="toggleTitle(clip)"
                            :class="{'bg-yellow-900': !!clip.title, 'bg-slate-700': !clip.title}"
                        >
                            タイトル
                        </button>
                    </div>
                </div>
            </div>
            <details>
                <summary class="bg-gray-500 text-white px-4 py-2 rounded-md cursor-pointer">JSON での表示</summary>
                <pre id="jsonDisplay" class="bg-gray-100 p-4 rounded-md overflow-auto h-full text-xs">{{ JSON.stringify(clips, null, 2) }}</pre>
            </details>
            <!-- オフセット -->
            <div class="mb-4">
                <label for="offsetInput" class="block text-sm font-medium text-gray-700">オフセット (ms)</label>
                <input type="number" id="offsetInput" v-model.number="offset" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <button @click="applyOffset" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-md">実施</button>
            </div>
        </div>
    </div>




<script type="module">
import { draw } from './js/services/drawingService.js';
const { createApp, ref, onMounted, watch, onUnmounted } = Vue;

function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

createApp({
    setup() {
        const clips = ref(JSON.parse(<?php echo json_encode($video['clips']); ?>));
        const title = ref("今回のテーマ");
        const subTitle = ref("アプリ制作の想い");
        const currentTranscriptIndex = ref(0);
        const isPlaying = ref(false);
        const mediaRecorder = ref(null);
        const recordedChunks = ref([]);
        const zoomLevel = ref(20);
        const offset = ref(-400);
        const selectedBgm = ref('');
        const playbackSpeed = ref('1');
        let regions, waveform, bgm;

        const videoRef = ref(null);
        const downloadBtnRef = ref(null);
        const recordingProgressRef = ref(null);

        // clipsの変更を監視して更新
        watch(clips, async (newClips) => {
            try {
                const formData = new FormData();
                formData.append('id', <?php echo $video['id']; ?>);
                formData.append('src', <?php echo json_encode($video['src']); ?>);
                formData.append('clips', JSON.stringify(newClips));

                const response = await fetch('update.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (!response.ok) {
                    throw new Error(result.error || '更新に失敗しました');
                }
                console.log('更新成功:', result);
            } catch (error) {
                console.error('更新エラー:', error);
            }
        }, { deep: true });

        // currentTranscriptIndexの変更を監視
        watch(currentTranscriptIndex, () => {
            drawRegions();
        });

        const startPlayback = () => {
            console.log('[info] startPlayback');
            if (!isPlaying.value) {
                isPlaying.value = true;
                playSegment(currentTranscriptIndex.value);
                draw(clips, currentTranscriptIndex, title, subTitle);
                if (selectedBgm.value) {
                    bgm.play(); // BGMを再生（選択されている場合のみ）
                }
            }
        };

        const pausePlayback = () => {
            console.log('[info] pausePlayback');
            isPlaying.value = false;
            videoRef.value.pause();
            if (bgm) {
                bgm.pause();
                bgm.currentTime = 0;
            }
        };

        const stopPlayback = () => {
            console.log('[info] stopPlayback');
            isPlaying.value = false;
            videoRef.value.pause();
            videoRef.value.currentTime = 0;
            currentTranscriptIndex.value = 0;
            if (bgm) {
                bgm.pause(); // BGMを停止
                bgm.currentTime = 0; // BGMを最初に戻す
            }
        };

        const drawRegions = () => {
            regions.clearRegions();
            clips.value.forEach((clip, index) => {
                regions.addRegion({
                    start: clip.startOffset / 1000,
                    end: clip.endOffset / 1000,
                    color: currentTranscriptIndex.value === index ? "rgba(254,206,56, 0.3)" : "rgba(0,0,0, 0.3)",
                    drag: currentTranscriptIndex.value === index,
                    resize: currentTranscriptIndex.value === index,
                });
            });
        };

        const selectClip = (clip) => {
            currentTranscriptIndex.value = clips.value.findIndex(c => c.uuid === clip.uuid);

            const startOffset = clip.startOffset;
            const endOffset = clip.endOffset;

        };

        const MIN_CLIP_DURATION = 10; // 最小クリップ間隔（ミリ秒）

        const playSegment = (index) => {
            if (index >= clips.value.length) {
                isPlaying.value = false;
                mediaRecorder.value?.stop();
                currentTranscriptIndex.value = 0;
                if (bgm) {
                    bgm.pause();
                    bgm.currentTime = 0;
                }
                return;
            }

            const { startOffset, endOffset } = clips.value[index];

            const setVideoTime = (time) => {
                videoRef.value.currentTime = time / 1000;
            };

            // indexが0ではない場合、前のクリップの終わりとの差分をチェックする。
            if (index > 0) {
                const prevClip = clips.value[index - 1];
                const gap = startOffset - prevClip.endOffset;
                if (gap > MIN_CLIP_DURATION) {
                    setVideoTime(startOffset);
                } else {
                    console.log('間隔が近いので移動しません');
                }
            } else {
                setVideoTime(startOffset);
            }

            const checkTime = () => {
                // 再生フラグがfalseになったら、ループを抜ける
                if (!isPlaying.value) {
                    return;
                }
                if (videoRef.value.currentTime >= endOffset / 1000) {
                    currentTranscriptIndex.value++;
                    playSegment(currentTranscriptIndex.value);
                } else {
                    requestAnimationFrame(checkTime);
                }
            };

            // シークが完了するのを待たずに再生を開始
            videoRef.value.play();
            checkTime();
        };

        const syncWaveform = () => {
            if (isPlaying.value) {
                const duration = waveform.getDuration();
                const currentTime = videoRef.value.currentTime;
                waveform.seekTo(currentTime / duration);
                requestAnimationFrame(syncWaveform);
            }
        };

        const duplicateClip = (clip) => {
            const newClip = { ...clip, uuid: generateUUID() };
            const index = clips.value.findIndex(c => c.uuid === clip.uuid);
            clips.value.splice(index + 1, 0, newClip);
        };

        const splitClip = (clip) => {
            const index = clips.value.findIndex(c => c.uuid === clip.uuid);
            const midOffset = (clip.startOffset + clip.endOffset) / 2;
            const midTranscriptIndex = Math.floor(clip.transcript.length / 2);

            const newClip = {
                ...clip,
                uuid: generateUUID(),
                startOffset: midOffset,
                endOffset: clip.endOffset,
                transcript: clip.transcript.slice(midTranscriptIndex).trim()
            };

            clip.endOffset = midOffset;
            clip.transcript = clip.transcript.slice(0, midTranscriptIndex).trim();

            clips.value.splice(index + 1, 0, newClip);
        };

        const deleteClip = (clip) => {
            const index = clips.value.findIndex(c => c.uuid === clip.uuid);
            if (index !== -1) {
                clips.value.splice(index, 1);
            }
        };

        const generateUUID = () => {
            const now = Date.now().toString(16);
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            }) + '-' + now;
        };

        const playClip = (clip) => {
            videoRef.value.currentTime = clip.startOffset / 1000;
            videoRef.value.play();

            const checkTime = () => {
                if (videoRef.value.currentTime >= clip.endOffset / 1000) {
                    videoRef.value.pause();
                } else {
                    requestAnimationFrame(checkTime);
                }
            };

            checkTime();
        };

        const applyOffset = () => {
            clips.value.forEach(clip => {
                clip.startOffset += offset.value;
                clip.endOffset += offset.value;
            });
            selectClip(clips.value[currentTranscriptIndex.value]);
        };

        const debouncedUpdate = debounce((index, value) => {
            clips.value[index].transcript = value;
        }, 300);

        const updateTranscript = (index, value) => {
            debouncedUpdate(index, value);
        };

        const toggleZoom = (clip) => {
            if (!clip.zoom || clip.zoom === 1.0) {
                clip.zoom = 1.5;
            } else {
                clip.zoom = 1.0;
            }
        };

        const toggleTitle = (clip) => {
            // clip.titleが存在しない場合はtrueにする
            if (!clip.title) {
                clip.title = true;
            } else {
                clip.title = !clip.title;
            }
        };

        const selectPhrase = (clip, phrase) => {
            const phraseStartTime = clip.startOffset + (phrase.transcriptStartOffset / clip.transcript.length) * (clip.endOffset - clip.startOffset);
            videoRef.value.currentTime = phraseStartTime / 1000;
        };

        const splitClipAtPhrase = (clip, phraseIndex) => {
            const index = clips.value.findIndex(c => c.uuid === clip.uuid);
            const splitPhrase = clip.phrases[phraseIndex];
            const nextPhrase = clip.phrases[phraseIndex + 1];
            
            // 分割する位置のオフセットを計算
            const splitOffset = clip.startOffset + splitPhrase.transcriptEndOffset;

            // phraseIndex までにあるフレーズを結合する
            const combinedTranscript = clip.phrases.slice(0, phraseIndex + 1).map(phrase => phrase.text).join('');

            const newClip = {
                ...clip,
                uuid: generateUUID(),
                startOffset: splitOffset + 1,
                endOffset: clip.endOffset,
                transcript: clip.transcript.slice(combinedTranscript.length),
                phrases: clip.phrases.slice(phraseIndex + 1).map(phrase => ({
                    ...phrase,
                    transcriptStartOffset: phrase.transcriptStartOffset - splitPhrase.transcriptEndOffset - 1,
                    transcriptEndOffset: phrase.transcriptEndOffset - splitPhrase.transcriptEndOffset - 1
                }))
            };

            clip.endOffset = splitOffset;
            clip.transcript = combinedTranscript;
            clip.phrases = clip.phrases.slice(0, phraseIndex + 1);

            clips.value.splice(index + 1, 0, newClip);
        };

        const moveClip = (direction) => {
            const newIndex = currentTranscriptIndex.value + direction;
            if (newIndex >= 0 && newIndex < clips.value.length) {
                // 最初のクリップで上矢印、または最後のクリップで下矢印の場合は何もしない
                if ((currentTranscriptIndex.value === 0 && direction === -1) || 
                    (currentTranscriptIndex.value === clips.value.length - 1 && direction === 1)) {
                    return;
                }
                currentTranscriptIndex.value = newIndex;
                selectClip(clips.value[newIndex]);
            }
        };

        const handleKeyDown = (event) => {
            // textareaにフォーカスがある場合は、矢印キーの挙動をスキップ
            if (event.target.tagName.toLowerCase() === 'textarea') {
                return;
            }

            switch (event.key) {
                case 'ArrowUp':
                case 'ArrowLeft':
                    event.preventDefault();
                    moveClip(-1);
                    break;
                case 'ArrowDown':
                case 'ArrowRight':
                    event.preventDefault();
                    moveClip(1);
                    break;
                case ' ':
                    event.preventDefault();
                    if (isPlaying.value) {
                        stopPlayback();
                    } else {
                        startPlayback();
                    }
                    break;
            }
        };

        const changePlaybackSpeed = () => {
            videoRef.value.playbackRate = parseFloat(playbackSpeed.value);
        };

        onMounted(() => {
            const canvas = document.getElementById('videoCanvas');
            const context = canvas.getContext('2d');
            const downloadBtn = downloadBtnRef.value;
            const recordingProgress = recordingProgressRef.value;

            downloadBtn.addEventListener('click', () => {
                const videoStream = videoRef.value.captureStream(30); // フレームレートを30に設定
                const canvasStream = canvas.captureStream(30); // フレームレートを30に設定
                const audioTrack = videoStream.getAudioTracks()[0];

                const audioContext = new AudioContext();
                const destination = audioContext.createMediaStreamDestination();
                
                // BGMのソースノードを作成
                if (selectedBgm.value) {
                    bgm = new Audio(`./assets/bgm/${selectedBgm.value}`);
                    bgm.volume = 0.1;
                    const bgmSourceNode = audioContext.createMediaElementSource(bgm);
                    bgmSourceNode.connect(destination);
                    bgmSourceNode.connect(audioContext.destination); // BGMを再生するために接続
                }

                // 動画のオーディオトラックをオーディオコンテキストに接続
                const videoSourceNode = audioContext.createMediaStreamSource(new MediaStream([audioTrack]));
                videoSourceNode.connect(destination);

                const mixedStream = new MediaStream([...canvasStream.getVideoTracks(), ...destination.stream.getAudioTracks()]);

                mediaRecorder.value = new MediaRecorder(mixedStream, { mimeType: 'video/webm; codecs=vp9,opus', videoBitsPerSecond: 2500000 });
                mediaRecorder.value.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.value.push(event.data);
                    }
                };
                mediaRecorder.value.onstop = () => {
                    const blob = new Blob(recordedChunks.value, { type: 'video/webm' });
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'recording.webm';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                };

                mediaRecorder.value.start();
                isPlaying.value = true; // 録画中は再生状態にする
                currentTranscriptIndex.value = 0;
                playSegment(currentTranscriptIndex.value);
                draw(clips, currentTranscriptIndex, title, subTitle);
                if (selectedBgm.value) {
                    bgm.play(); // BGMを再生
                }
            });

            videoRef.value.addEventListener('timeupdate', () => {
                // 録画の進行状況を更新
                const totalDuration = clips.value.reduce((acc, transcript) => acc + (transcript.endOffset - transcript.startOffset), 0);
                const currentDuration = clips.value.slice(0, currentTranscriptIndex.value).reduce((acc, transcript) => acc + (transcript.endOffset - transcript.startOffset), 0) + (videoRef.value.currentTime * 1000 - clips.value[currentTranscriptIndex.value].startOffset);
                recordingProgress.value = (currentDuration / totalDuration) * 100;
            });

            videoRef.value.addEventListener('loadeddata', () => {
                draw(clips, currentTranscriptIndex, title, subTitle);
            });

            // 波形表示のためのWaveSurfer.jsの設定
            regions = WaveSurfer.Regions.create();

            waveform = WaveSurfer.create({
                container: '#waveform',
                waveColor: 'violet',
                progressColor: 'purple',
                backend: 'MediaElement',
                url: './uploads/<?php echo $video['src']; ?>',
                plugins: [
                    WaveSurfer.Timeline.create({
                        container: '#wave-timeline'
                    }),
                    regions
                ]
            });

            regions.on('region-updated', (region) => {
                const { start, end } = region;
                const startOffset = start * 1000;
                const endOffset = end * 1000;

                const currentClip = clips.value[currentTranscriptIndex.value];

                // currentTranscriptIndex が 0 より大きい場合、一つ前のクリップの終わりとの差分をチェックする。
                console.log('currentTranscriptIndex.value', currentTranscriptIndex.value);
                if (currentTranscriptIndex.value > 0) {
                    const prevClip = clips.value[currentTranscriptIndex.value - 1];
                    const gap = currentClip.startOffset - prevClip.endOffset;
                    if (gap <= MIN_CLIP_DURATION) {
                        prevClip.endOffset = startOffset - 1;
                    }
                    console.log('一つ前のクリップの終わりとの差分', gap);
                }
                // currentTranscriptIndex が clips.value.length より小さい場合、一つ後のクリップの始めとの差分をチェックする。
                if (currentTranscriptIndex.value < clips.value.length - 1) {
                    const nextClip = clips.value[currentTranscriptIndex.value + 1];
                    const gap = nextClip.startOffset - currentClip.endOffset;
                    if (gap <= MIN_CLIP_DURATION) {
                        nextClip.startOffset = endOffset + 1;
                    }
                    console.log('一つ後のクリップの始めとの差分', gap);
                }

                currentClip.startOffset = startOffset;
                currentClip.endOffset = endOffset;

                // リージョンの再描画
                drawRegions();
            });

            const zoomSlider = document.getElementById('zoomSlider');
            zoomSlider.addEventListener('input', (event) => {
                const zoomLevel = Number(event.target.value);
                waveform.zoom(zoomLevel);
            });

            waveform.on('ready', () => {
                selectClip(clips.value[currentTranscriptIndex.value]);
                drawRegions();
            });

            window.addEventListener('keydown', handleKeyDown);

        });

        onUnmounted(() => {
            window.removeEventListener('keydown', handleKeyDown);
        });

        watch(selectedBgm, (newValue) => {
            if (bgm) {
                bgm.pause();
                bgm.currentTime = 0;
            }
            if (newValue) {
                bgm = new Audio(`./assets/bgm/${newValue}`);
                bgm.volume = 0.1;
                bgm.loop = true; // BGMをループ再生
            } else {
                bgm = null;
            }
        });

        return {
            clips,
            title,
            subTitle,
            currentTranscriptIndex,
            isPlaying,
            mediaRecorder,
            recordedChunks,
            zoomLevel,
            offset,
            selectedBgm,
            playbackSpeed,
            startPlayback,
            pausePlayback,
            stopPlayback,
            selectClip,
            duplicateClip,
            splitClip,
            deleteClip,
            playClip,
            applyOffset,
            updateTranscript,
            toggleZoom,
            toggleTitle,
            selectPhrase,
            splitClipAtPhrase,
            moveClip,
            handleKeyDown,
            changePlaybackSpeed,
            videoRef,
            downloadBtnRef,
            recordingProgressRef
        };
    }
}).mount('#app');
</script>

</body>
</html>