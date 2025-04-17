@php
    $id = $getId();
    $name = $getName();
    $statePath = $getStatePath();
@endphp

<div
    x-data="{
        isRecording: false,
        audioChunks: [],
        mediaRecorder: null,
        audioStream: null,
        audioUrl: null,
        startTime: null,
        timerInterval: null,
        duration: 0,
        init() {
            this.$watch('audioUrl', (value) => {
                if (value) {
                    this.$refs.audioPlayer.src = value;
                }
            });
        },
        async startRecording() {
            try {
                this.audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(this.audioStream);
                this.audioChunks = [];
                this.startTime = Date.now();
                this.duration = 0;
                this.updateTimer();
                this.timerInterval = setInterval(() => this.updateTimer(), 1000);
                this.isRecording = true;

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = () => {
                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/mp3' });
                    this.audioUrl = URL.createObjectURL(audioBlob);

                    // Create a File object from the Blob
                    const file = new File([audioBlob], 'voice_answer.mp3', { type: 'audio/mp3' });

                    // Create a new FileList containing our File
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    // Set the file input's files
                    this.$refs.fileInput.files = dataTransfer.files;

                    // Dispatch change event to notify Filament
                    this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));

                    // Set the duration
                    this.$refs.durationInput.value = this.duration;
                    this.$refs.durationInput.dispatchEvent(new Event('change', { bubbles: true }));

                    clearInterval(this.timerInterval);
                };

                this.mediaRecorder.start();
            } catch (error) {
                console.error('Error accessing microphone:', error);
                alert('خطا در دسترسی به میکروفون. لطفا دسترسی میکروفون را فعال کنید.');
            }
        },
        stopRecording() {
            if (this.mediaRecorder && this.isRecording) {
                this.mediaRecorder.stop();
                this.audioStream.getTracks().forEach(track => track.stop());
                this.isRecording = false;
                clearInterval(this.timerInterval);
            }
        },
        updateTimer() {
            const now = Date.now();
            this.duration = Math.floor((now - this.startTime) / 1000);
            const minutes = Math.floor(this.duration / 60);
            const seconds = this.duration % 60;
            this.$refs.timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        },
        deleteRecording() {
            this.audioUrl = null;
            this.audioChunks = [];
            this.duration = 0;
            this.$refs.timer.textContent = '00:00';
            this.$refs.fileInput.value = '';
            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            this.$refs.durationInput.value = '';
            this.$refs.durationInput.dispatchEvent(new Event('change', { bubbles: true }));
            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
            }
        }
    }"
    class="space-y-4"
>
    <div class="flex justify-between space-x-4">
        <button
            type="button"
            x-show="!isRecording"
            @click="startRecording"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            <svg class="w-5 h-5 ml-2 rtl:ml-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
            شروع ضبط
        </button>
        <button
            type="button"
            x-show="isRecording"
            @click="stopRecording"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
            <svg class="w-5 h-5 ml-2 rtl:ml-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
            </svg>
            توقف ضبط
        </button>
        <span x-ref="timer" class="text-lg font-mono bg-gray-100 px-3 py-1 rounded">00:00</span>
    </div>

    <div x-show="audioUrl" class="space-y-4">
        <audio x-ref="audioPlayer" controls class="w-full"></audio>
        <button
            type="button"
            @click="deleteRecording"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >
            <svg class="w-5 h-5 ml-2 rtl:ml-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            حذف ضبط
        </button>
    </div>

    <input type="file" x-ref="fileInput" name="{{ $name }}" accept="audio/mp3" class="hidden" />
    <input type="hidden" x-ref="durationInput" name="{{ $statePath }}_duration" />
</div>
