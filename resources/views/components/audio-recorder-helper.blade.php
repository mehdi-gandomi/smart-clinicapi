@php
    $id = $getId();
    $name = $getName();
    $statePath = $getStatePath();
    $uploadComponentId = $getUploadComponentId() ?? 'audio-upload';
@endphp

<div
    x-data="{
        isRecording: false,
        mediaRecorder: null,
        audioStream: null,
        audioUrl: null,
        startTime: null,
        timerInterval: null,
        duration: 0,
        audioChunks: [],
        mimeType: '',

        async startRecording() {
            try {
                this.audioStream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        echoCancellation: true,
                        noiseSuppression: true,
                        channelCount: 1 // Mono recording for smaller file size
                    }
                });

                // Try to find the best supported format with good compression
                const mimeTypes = [
                    'audio/webm;codecs=opus', // Good compression
                    'audio/webm',
                    'audio/ogg;codecs=opus',
                    'audio/ogg',
                    'audio/mp4;codecs=mp4a',
                    'audio/mp4',
                    'audio/aac',
                    'audio/wav',
                    '' // Browser default
                ];

                // Find first supported mime type
                this.mimeType = '';
                for (let type of mimeTypes) {
                    if (MediaRecorder.isTypeSupported(type)) {
                        this.mimeType = type;
                        console.log(`Using MIME type: ${type}`);
                        break;
                    }
                }

                // Set up recorder with options
                const options = {
                    mimeType: this.mimeType,
                    audioBitsPerSecond: 96000 // Lower bitrate for smaller file
                };

                this.mediaRecorder = new MediaRecorder(this.audioStream, options);

                this.audioChunks = [];
                this.startTime = Date.now();
                this.duration = 0;
                this.updateTimer();
                this.timerInterval = setInterval(() => this.updateTimer(), 1000);
                this.isRecording = true;

                this.mediaRecorder.ondataavailable = (event) => {
                    this.audioChunks.push(event.data);
                };

                this.mediaRecorder.onstop = async () => {
                    try {
                        // Create blob with the recorded audio
                        const audioBlob = new Blob(this.audioChunks, { type: this.mimeType });
                        this.audioUrl = URL.createObjectURL(audioBlob);

                        // Create a download link
                        const downloadLink = document.createElement('a');
                        downloadLink.href = this.audioUrl;

                        // Determine file extension based on MIME type
                        let fileExt = 'webm';
                        if (this.mimeType.includes('ogg')) fileExt = 'ogg';
                        else if (this.mimeType.includes('mp4') || this.mimeType.includes('aac')) fileExt = 'mp4';
                        else if (this.mimeType.includes('wav')) fileExt = 'wav';

                        downloadLink.download = `voice_answer.${fileExt}`;
                        downloadLink.textContent = 'دانلود فایل صوتی';
                        downloadLink.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';

                        // Add the download link to the page
                        const container = this.$refs.downloadContainer;
                        container.innerHTML = '';
                        container.appendChild(downloadLink);

                        // Set the duration
                        this.$refs.durationInput.value = this.duration;

                        // Create a File object with the correct MIME type
                        const currentDate = new Date();
                        const fileName = `voice_answer_${currentDate.getTime()}.${fileExt}`;
                        const audioFile = new File([audioBlob], fileName, { type: this.mimeType });

                        // Add the file to the upload component
                        this.addFileToUploader(audioFile);

                    } catch (error) {
                        console.error('Error processing audio:', error);
                        alert('خطا در پردازش صدا.');
                    }

                    clearInterval(this.timerInterval);
                };

                this.mediaRecorder.start(1000); // Capture in 1-second chunks for better reliability
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
            this.$refs.durationInput.value = '';
            this.$refs.downloadContainer.innerHTML = '';

            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
            }

            // Clear the upload component
            this.clearUploader();
        },

        addFileToUploader(file) {
            // Check if we're using FilePond
            const filepondInput = document.querySelector(`#${this.getUploaderElementId()} div.filepond--root`);
            if (filepondInput && window.FilePond) {
                const pond = window.FilePond.find(filepondInput);
                if (pond) {
                    pond.addFile(file);
                    return;
                }
            }

            // If not using FilePond, try to dispatch a Livewire event
            const uploadComponent = document.getElementById(this.getUploaderElementId());
            if (uploadComponent) {
                // For Livewire
                if (window.Livewire) {
                    const componentId = uploadComponent.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        // Create a fake change event
                        const event = new Event('change', { bubbles: true });
                        Object.defineProperty(event, 'target', {
                            writable: false,
                            value: { files: dataTransfer.files }
                        });

                        // Dispatch the event
                        uploadComponent.dispatchEvent(event);
                        return;
                    }
                }
            }

            console.error('Could not find upload component or FilePond instance');
        },

        clearUploader() {
            // Check if we're using FilePond
            const filepondInput = document.querySelector(`#${this.getUploaderElementId()} div.filepond--root`);
            if (filepondInput && window.FilePond) {
                const pond = window.FilePond.find(filepondInput);
                if (pond) {
                    pond.removeFiles();
                    return;
                }
            }

            // If not using FilePond, try to clear the input directly
            const uploadComponent = document.getElementById(this.getUploaderElementId());
            if (uploadComponent && uploadComponent.type === 'file') {
                uploadComponent.value = '';
            }
        },

        getUploaderElementId() {
            return '{{ $uploadComponentId }}';
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
        <audio x-ref="audioPlayer" controls class="w-full" x-bind:src="audioUrl"></audio>
        <div x-ref="downloadContainer" class="flex justify-center"></div>
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

    <input type="hidden" x-ref="durationInput" name="{{ $statePath }}_duration" />
</div>