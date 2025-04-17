@props(['statePath'])

<div
    x-data="{
        recording: false,
        audioChunks: [],
        audioBlob: null,
        audioUrl: null,
        duration: '00:00',
        timer: 0,
        timerInterval: null,
        mediaRecorder: null,
        error: null,
        wavesurfer: null,
        audioContext: null,
        analyser: null,
        dataArray: null,
        animationId: null,
        micStream: null,

        async init() {
            // Initialize WaveSurfer
            this.wavesurfer = WaveSurfer.create({
                container: this.$refs.waveform,
                waveColor: '#4CAF50',
                progressColor: '#1976D2',
                cursorColor: 'transparent',
                barWidth: 2,
                barGap: 1,
                height: 40,
                normalize: true,
                interact: false
            });
        },

        async startRecording() {
            try {
                this.recording = true;
                this.audioChunks = [];
                this.timer = 0;
                this.duration = '00:00';
                this.error = null;

                // Start timer
                this.timerInterval = setInterval(() => {
                    this.timer++;
                    this.duration = this.formatTime(this.timer);
                }, 1000);

                // Get audio stream
                this.micStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(this.micStream);

                // Set up audio visualization
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const source = this.audioContext.createMediaStreamSource(this.micStream);
                this.analyser = this.audioContext.createAnalyser();
                this.analyser.fftSize = 128;
                source.connect(this.analyser);

                this.dataArray = new Uint8Array(this.analyser.frequencyBinCount);

                // Start visualization
                this.startVisualization();

                this.mediaRecorder.ondataavailable = (e) => {
                    this.audioChunks.push(e.data);
                };

                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/mp3' });
                    this.audioUrl = URL.createObjectURL(audioBlob);
                    this.audioBlob = audioBlob;

                    // Load recorded audio into WaveSurfer
                    this.wavesurfer.loadBlob(audioBlob);

                    // Create a File object from the Blob with duration in filename
                    const file = new File([audioBlob], `voice-answer-${this.duration}.mp3`, { type: 'audio/mp3' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    // Update the hidden file input
                    const fileInput = this.$refs.fileInput;
                    fileInput.files = dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                };

                this.mediaRecorder.start();
            } catch (error) {
                console.error('Error accessing microphone:', error);
                this.error = 'لطفا به میکروفون دسترسی دهید';
                this.recording = false;
            }
        },

        startVisualization() {
            const canvas = this.$refs.visualizer;
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;

            const draw = () => {
                if (!this.recording) {
                    cancelAnimationFrame(this.animationId);
                    return;
                }

                // Get frequency data
                this.analyser.getByteFrequencyData(this.dataArray);

                // Clear canvas
                ctx.clearRect(0, 0, width, height);

                // Draw visualization
                const barWidth = (width / this.analyser.frequencyBinCount) * 2.5;
                let barHeight;
                let x = 0;

                for (let i = 0; i < this.analyser.frequencyBinCount; i++) {
                    barHeight = (this.dataArray[i] / 255) * height;

                    ctx.fillStyle = '#4CAF50';
                    ctx.fillRect(x, height - barHeight, barWidth - 1, barHeight);

                    x += barWidth;
                }

                this.animationId = requestAnimationFrame(draw);
            };

            draw();
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                this.recording = false;
                this.mediaRecorder.stop();
                clearInterval(this.timerInterval);

                // Stop visualization
                cancelAnimationFrame(this.animationId);

                // Stop all tracks in the stream
                if (this.micStream) {
                    this.micStream.getTracks().forEach(track => track.stop());
                }

                // Clean up audio context
                if (this.audioContext && this.audioContext.state !== 'closed') {
                    this.audioContext.close();
                }
            }
        },

        formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            seconds = seconds % 60;
            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },

        deleteRecording() {
            this.audioUrl = null;
            this.audioBlob = null;
            this.duration = '00:00';
            this.timer = 0;
            this.error = null;

            // Clear waveform
            this.wavesurfer.empty();

            // Clear the file input
            this.$refs.fileInput.value = '';
            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));

            // Stop any ongoing recording
            if (this.mediaRecorder && this.mediaRecorder.state === 'recording') {
                this.stopRecording();
            }
        }
    }"
    x-init="init"
    class="space-y-4"
>
    <div class="flex items-center gap-4 bg-gray-100 p-4 rounded-lg">
        <!-- Record Button -->
        <button
            type="button"
            x-show="!recording && !audioUrl"
            @click="startRecording"
            class="flex items-center justify-center w-12 h-12 rounded-full bg-red-500 hover:bg-red-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 transform hover:scale-105"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="#000" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
        </button>

        <!-- Recording Status Area -->
        <div x-show="recording" class="flex-1 flex items-center gap-4">
            <!-- Recording Animation -->
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></div>
                <span class="text-black font-medium">در حال ضبط</span>
            </div>

            <!-- Visualization Area -->
            <div class="flex-1 relative h-12 bg-black/5 rounded-lg overflow-hidden">
                <!-- Live Recording Visualizer -->
                <canvas
                    x-ref="visualizer"
                    class="w-full h-full absolute inset-0"
                    width="600"
                    height="48"
                ></canvas>

                <!-- Timer -->
                <div class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/90 px-2 py-1 rounded text-sm font-medium text-gray-600">
                    <span x-text="duration"></span>
                </div>
            </div>

            <!-- Stop Button -->
            <button
                type="button"
                @click="stopRecording"
                class="flex items-center justify-center w-12 h-12 rounded-full bg-red-500 hover:bg-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-400"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="#000">
                    <rect x="6" y="6" width="12" height="12" />
                </svg>
            </button>
        </div>

        <!-- Recorded Audio Area -->
        <div x-show="!recording && audioUrl" class="flex-1 flex items-center gap-4">
            <!-- Waveform -->
            <div x-ref="waveform" class="flex-1 h-12"></div>

            <!-- Duration -->
            <div class="px-2 py-1 bg-white rounded text-sm font-medium text-gray-600">
                <span x-text="duration"></span>
            </div>

            <!-- Delete Button -->
            <button
                type="button"
                @click="deleteRecording"
                class="flex items-center justify-center w-10 h-10 rounded-full text-red-500 hover:bg-red-50 transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="error" class="text-red-600 text-sm" x-text="error"></div>

    <!-- Hidden File Input -->
    <input
        type="file"
        :name="statePath"
        x-ref="fileInput"
        class="hidden"
        accept="audio/mp3,audio/wav"
    >
</div>

<!-- Add WaveSurfer.js -->
<script src="https://unpkg.com/wavesurfer.js@7/dist/wavesurfer.min.js"></script>
