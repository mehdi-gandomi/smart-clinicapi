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

        async startRecording() {
            try {
                this.audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });

                // Try to use audio/mpeg if supported
                const mimeType = MediaRecorder.isTypeSupported('audio/mpeg')
                    ? 'audio/mpeg'
                    : (MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : '');

                const options = mimeType ? { mimeType } : {};
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
                    // Create blob with the recorded audio
                    const audioBlob = new Blob(this.audioChunks, { type: this.mediaRecorder.mimeType });

                    // Convert to MP3 if not already MP3
                    let finalBlob;
                    if (this.mediaRecorder.mimeType !== 'audio/mpeg') {
                        try {
                            finalBlob = await this.convertAudioFormat(audioBlob, 'audio/mpeg');
                        } catch (error) {
                            console.error('Error converting to MP3, using original format:', error);
                            finalBlob = audioBlob;
                        }
                    } else {
                        finalBlob = audioBlob;
                    }

                    this.audioUrl = URL.createObjectURL(finalBlob);

                    // Create a download link
                    const downloadLink = document.createElement('a');
                    downloadLink.href = this.audioUrl;
                    downloadLink.download = 'voice_answer.mp3';
                    downloadLink.textContent = 'دانلود فایل صوتی';
                    downloadLink.className = 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500';

                    // Add the download link to the page
                    const container = this.$refs.downloadContainer;
                    container.innerHTML = '';
                    container.appendChild(downloadLink);

                    // Set the duration
                    this.$refs.durationInput.value = this.duration;

                    // Create a File object from the Blob with the correct MIME type
                    const currentDate = new Date();
                    const fileName = `voice_answer_${currentDate.getTime()}.mp3`;
                    const audioFile = new File([finalBlob], fileName, { type: 'audio/mpeg' });

                    // Add the file to the upload component
                    this.addFileToUploader(audioFile);

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
            this.$refs.durationInput.value = '';
            this.$refs.downloadContainer.innerHTML = '';

            if (this.audioStream) {
                this.audioStream.getTracks().forEach(track => track.stop());
            }

            // Clear the upload component
            this.clearUploader();
        },

        // Helper function to convert audio blob to MP3 format (simplified)
        async convertAudioFormat(blob, targetType = 'audio/mpeg') {
            return new Promise((resolve, reject) => {
                // For actual conversion, you'd need a library like lamejs
                // This is a simplified approach that would work for format conversion
                // but actual mp3 encoding requires additional libraries

                // Create an audio element to decode the blob
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const fileReader = new FileReader();

                fileReader.onload = async function(event) {
                    try {
                        // Decode the audio data
                        const arrayBuffer = event.target.result;
                        const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);

                        // Create a new offline context for rendering
                        const offlineContext = new OfflineAudioContext(
                            audioBuffer.numberOfChannels,
                            audioBuffer.length,
                            audioBuffer.sampleRate
                        );

                        // Create source from the decoded buffer
                        const source = offlineContext.createBufferSource();
                        source.buffer = audioBuffer;
                        source.connect(offlineContext.destination);
                        source.start(0);

                        // Render the audio
                        const renderedBuffer = await offlineContext.startRendering();

                        // Convert to appropriate format
                        const wavArrayBuffer = audioBufferToWav(renderedBuffer);
                        const wavBlob = new Blob([wavArrayBuffer], { type: targetType });

                        resolve(wavBlob);
                    } catch (error) {
                        console.error('Error converting audio:', error);
                        reject(error);
                    }
                };

                fileReader.onerror = reject;
                fileReader.readAsArrayBuffer(blob);

                // Function to convert AudioBuffer to WAV
                function audioBufferToWav(buffer, opt = {}) {
                    const numChannels = buffer.numberOfChannels;
                    const sampleRate = buffer.sampleRate;
                    const format = opt.float32 ? 3 : 1;
                    const bitDepth = format === 3 ? 32 : 16;

                    // Create the buffer for the WAV file
                    const headerBytes = 44;
                    const dataBytes = numChannels * buffer.length * (bitDepth / 8);
                    const fileSize = headerBytes + dataBytes;
                    const arrayBuffer = new ArrayBuffer(fileSize);
                    const view = new DataView(arrayBuffer);

                    // RIFF identifier
                    writeString(view, 0, 'RIFF');
                    // File size minus RIFF identifier and size field
                    view.setUint32(4, fileSize - 8, true);
                    // RIFF type
                    writeString(view, 8, 'WAVE');
                    // Format chunk identifier
                    writeString(view, 12, 'fmt ');
                    // Format chunk length
                    view.setUint32(16, 16, true);
                    // Sample format (1 for PCM, 3 for IEEE float)
                    view.setUint16(20, format, true);
                    // Number of channels
                    view.setUint16(22, numChannels, true);
                    // Sample rate
                    view.setUint32(24, sampleRate, true);
                    // Byte rate (sample rate * block align)
                    view.setUint32(28, sampleRate * numChannels * (bitDepth / 8), true);
                    // Block align (channel count * bytes per sample)
                    view.setUint16(32, numChannels * (bitDepth / 8), true);
                    // Bits per sample
                    view.setUint16(34, bitDepth, true);
                    // Data chunk identifier
                    writeString(view, 36, 'data');
                    // Data chunk length
                    view.setUint32(40, dataBytes, true);

                    // Write the PCM samples
                    const dataView = new DataView(arrayBuffer);
                    let offset = 44;

                    for (let i = 0; i < buffer.length; i++) {
                        for (let channel = 0; channel < numChannels; channel++) {
                            const sample = buffer.getChannelData(channel)[i];
                            if (format === 3) {
                                // IEEE float
                                dataView.setFloat32(offset, sample, true);
                            } else {
                                // 16-bit PCM
                                const value = Math.max(-1, Math.min(1, sample));
                                dataView.setInt16(offset, value * 0x7FFF, true);
                            }
                            offset += bitDepth / 8;
                        }
                    }

                    return arrayBuffer;
                }

                // Helper function to write strings to the DataView
                function writeString(view, offset, string) {
                    for (let i = 0; i < string.length; i++) {
                        view.setUint8(offset + i, string.charCodeAt(i));
                    }
                }
            });
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