@php

    function avg($arr,$count=null)
    {
        $count=$count ? $count:count($arr);
        $sum = 0;
        foreach ($arr as $item) {
            $sum += $item;
        }
        return number_format($sum / $count, 1);
    }
    function min_number($arr){
        $arr = array_diff($arr, array(null));
        return min($arr);
    }
    function max_number($arr){
        $arr = array_diff($arr, array(null));
        return max($arr);
    }




@endphp

<x-filament::widget>

      <style>
        /* Core table styling */
.abpm-table .table, .patient-table .table, .result-table .table {
    width: 100%;
}

table {
    caption-side: bottom;
    border-collapse: collapse;
}

caption {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    color: var(--bs-secondary-color);
    text-align: left;
}

th {
    text-align: inherit;
    text-align: -webkit-match-parent;
}

thead, tbody, tfoot, tr, td, th {
    border-color: inherit;
    border-style: solid;
    border-width: 0;
}

.table {
    --bs-table-color: var(--bs-body-color);
    --bs-table-bg: transparent;
    --bs-table-border-color: var(--bs-border-color);
    --bs-table-accent-bg: transparent;
    width: 100%;
    margin-bottom: 1rem;
    color: var(--bs-table-color);
    vertical-align: top;
    border-color: var(--bs-table-border-color);
}

.table > :not(caption) > * > * {
    padding: 0.5rem 0.5rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: var(--bs-border-width);
    box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
}

.table > tbody {
    vertical-align: inherit;
}

.table > thead {
    vertical-align: bottom;
}

.table-bordered > :not(caption) > * {
    border-width: var(--bs-border-width) 0;
}

.table-bordered > :not(caption) > * > * {
    border-width: 0 var(--bs-border-width);
}

/* Used header style */
.table-header {
    background: #fde9d9;
}

/* Border utilities that are used */
.border-bottom-0 {
    border-bottom: 0 !important;
}

.border-top-0 {
    border-top: 0 !important;
}

.border-end-0 {
    border-right: 0 !important;
}

.border-start-0 {
    border-left: 0 !important;
}

.border-0 {
    border: 0 !important;
}

.p-0 {
    padding: 0 !important;
}

/* Chart specific styling */
.chart-table {
    width: 100%;
}

#line-chart {
    border: 1px solid #000;
    height: 60vh;
}

/* List styles used in bottom-texts */
.bottom-texts li {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

/* Layout styles */
.text-center {
    text-align: center;
}

/* Print-specific styles */
@media print {
    .table-bordered, .table-bordered tr, .table-bordered td, .table-bordered th {
        border-width: 2px !important;
    }

    .patient-table, .abpm-table, .result-table {
        width: calc(100% + 6px) !important;
    }

    @page {
        size: A4;
        margin-top: 0;
        margin-bottom: 0;
    }

    body {
        padding-top: 72px;
        padding-bottom: 72px;
        color-adjust: exact;
        font-size: 22px !important;
    }

    table {
        table-layout: fixed !important;
        margin-bottom: 0 !important;
    }

    #line-chart {
        border: 2px solid #000 !important;
        height: 45vh !important;
    }

    .result-table {
        font-size: 17px !important;
    }

    .table > :not(caption) > * > * {
        padding: .4rem .4rem;
    }

    .container {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
    }
}
      </style>
<script>
    function showBloodPressureData(ids) {
        const modal = document.getElementById('bloodPressureModal');
        const dataContainer = document.getElementById('bloodPressureData');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Fetch blood pressure data
        fetch(`/api/blood-pressure/data?ids=${ids}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let html = '';
                    data.data.forEach(bp => {
                        html += `
                            <div class="border rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">تاریخ:</p>
                                        <p class="font-medium">${new Date(bp.date).toLocaleDateString('fa-IR')}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">ساعت:</p>
                                        <p class="font-medium">${new Date(bp.date).toLocaleTimeString('fa-IR')}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">فشار سیستول:</p>
                                        <p class="font-medium">${bp.systolic} mmHg</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">فشار دیاستول:</p>
                                        <p class="font-medium">${bp.diastolic} mmHg</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">ضربان قلب:</p>
                                        <p class="font-medium">${bp.heart_rate} bpm</p>
                                    </div>
                                    ${bp.notes ? `
                                        <div class="col-span-2">
                                            <p class="text-sm text-gray-600">یادداشت:</p>
                                            <p class="font-medium">${bp.notes}</p>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                    dataContainer.innerHTML = html;
                } else {
                    dataContainer.innerHTML = '<p class="text-red-500">خطا در دریافت اطلاعات</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                dataContainer.innerHTML = '<p class="text-red-500">خطا در دریافت اطلاعات</p>';
            });
    }

    function closeBloodPressureModal() {
        const modal = document.getElementById('bloodPressureModal');
        modal.classList.add('hidden');
    }

    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('bloodPressureModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeBloodPressureModal();
                }
            });
        }
    });
</script>
    @if(count($all_dia_data) > 0)
    <div class="container">
        <div class="print-area">
            <h1 class="text-center">ABPM Result</h1>
        <div class="result-wrap ">
            <div class="patient-table">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-header">
                            <th colspan="5">Patient</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom-0">
                            <td colspan="2" class="border-end-0 border-bottom-0">
                                Name: {{ $record->name }}
                            </td>

                        </tr>
                        <tr class="border-top-0">
                            <td colspan="2" class="border-end-0 border-top-0">
                                Age: {{ $record->age }}
                            </td>
                            <td colspan="2" class="border-start-0 border-end-0 border-top-0">
                                Gender: {{ $record->sex == 1 ? 'Male' : 'Female' }}
                            </td>

                        </tr>
                    </tbody>

                </table>

            </div>
            <div class="abpm-table">
                <table class="table table-bordered">
                <thead>
                        <tr class="table-header">
                            <th colspan="5"> ABPM Statistics</th>

                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td class="border-end-0">

                            </td>
                            <td class="border-end-0 border-start-0">
                                Mean
                            </td>
                            <td class="border-end-0 border-start-0">
                                Std. Dev
                            </td>
                            <td class="border-end-0 border-start-0">
                                Max
                            </td>
                            <td class=" border-start-0">
                                Min
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                SYS (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($sys_avg, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($sys_std, 1) }} </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(max_number($all_sys_data), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ round(min_number($all_sys_data), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                DIA (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($dia_avg, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($dia_std, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(max_number($all_dia_data), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ round(min_number($all_dia_data), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                HR (bpm)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($hr_avg, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($hr_std, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(max_number($all_hr_data), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ round(min_number($all_hr_data), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                MAP (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(avg($maps), 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($map_std, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ number_format(max_number($maps), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ number_format(min_number($maps), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                PP (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(avg($pps), 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($pp_std, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ number_format(max_number($pps), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ number_format(min_number($pps), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                CO (l/min)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(avg($cos), 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($co_std, 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ number_format(max_number($cos), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ number_format(min_number($cos), 1) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                CI (l/min/m2)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round(avg($cis), 1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ round($ci_std,1) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ number_format(max_number($cis), 1) }}
                            </td>
                            <td class=" border-start-0">
                                {{ number_format(min_number($cis), 1) }}
                            </td>
                        </tr>

                </tbody>

            </table>
                <table class="chart-table" >
                    <tbody>
                        <tr class="border-0">
                            <td class="border-0 p-0" colspan="5" x-data="@js(Js::from(["dates" => $dates,'diaData'=>$all_dia_data,'sysData'=>$all_sys_data]))">
                                <canvas id="line-chart" x-init="

                                const chartElement = document.getElementById('line-chart');

                    // Check if chart element exists
                    if (!chartElement) {
                        console.error('Chart canvas element not found');
                        return;
                    }

                    // Check if Chart.js is loaded
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js is not loaded');
                        return;
                    }

                    // Clean up any existing chart instance
                    if (this.myChart) {
                        this.myChart.destroy();
                    }



                    // Register the annotation plugin if needed
                    if (Chart.registry && typeof Chart.registry.getPlugin === 'function' &&
                        Chart.registry.getPlugin('annotation') === undefined) {
                        // Using the global ChartAnnotation if available
                        if (window.ChartAnnotation) {
                            Chart.register(window.ChartAnnotation);
                        } else {
                            console.warn('Chart.js annotation plugin is not available');
                        }
                    }

                    // Create the chart
                    this.myChart = new Chart(chartElement, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [
                                {
                                    data: diaData,
                                    label: 'DBP',
                                    borderColor: '#3cba9f',
                                    fill: false
                                },
                                {
                                    data: sysData,
                                    label: 'SBP',
                                    borderColor: '#e43202',
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'ABPM'
                                },
                                annotation: {
                                    annotations: {
                                        line1: {
                                            type: 'line',
                                            yMin: 120,
                                            yMax: 120,
                                            borderColor: 'rgb(0, 0, 0)',
                                            borderWidth: 2,
                                            borderDash: [10,5]
                                        },
                                        line2: {
                                            type: 'line',
                                            yMin: 80,
                                            yMax: 80,
                                            borderColor: 'rgb(255, 0, 0)',
                                            borderWidth: 2,
                                            borderJoinStyle: 'round',
                                            borderCapStyle: 'round',
                                            borderDash: [10,5]
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    display: true,
                                    title: {
                                        display: true
                                    }
                                },
                                y: {
                                    grid: {
                                        display: false
                                    },
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'mmHg'
                                    },
                                    suggestedMin: 20,
                                    suggestedMax: 240,
                                    ticks: {
                                        stepSize: 20
                                    }
                                }
                            }
                        }
                    });
            "></canvas>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="result-table">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-header">
                            <th colspan="7">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border-end-0">
                            </td>
                            <td class="border-end-0 border-start-0" style="font-weight:bold">
                                Office
                            </td>
                            <td class="border-end-0 border-start-0" style="font-weight:bold">
                                Total
                            </td>
                        </tr>
                        <tr>
                           <td class="border-end-0">
                           SYS/DIA (mmgHg)
                           </td>
                           <td class="border-end-0 border-start-0">
                           {{$all_sys_data[0]}} / {{$all_dia_data[0]}}
                           </td>
                           <td class="border-end-0 border-start-0">
                               {{$sys_avg}} / {{$dia_avg}}
                           </td>
                       </tr>
                        <tr>
                            <td class="border-end-0">
                                HR (bpm)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{$all_hr_data[0]}}
                            </td>
                            <td class="border-end-0 border-start-0">
                            {{$hr_avg}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Voice Recording Section -->
            <div class="mt-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-semibold mb-4">ثبت توضیحات صوتی</h3>
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
                            voiceNotes: '',

                            async startRecording() {
                                try {
                                    this.audioUrl = null;
                                    this.voiceNotes = '';
                                    this.audioStream = await navigator.mediaDevices.getUserMedia({
                                        audio: {
                                            echoCancellation: true,
                                            noiseSuppression: true,
                                            channelCount: 1
                                        }
                                    });

                                    const mimeTypes = [
                                        'audio/webm;codecs=opus',
                                        'audio/webm',
                                        'audio/ogg;codecs=opus',
                                        'audio/ogg',
                                        'audio/mp4;codecs=mp4a',
                                        'audio/mp4',
                                        'audio/aac',
                                        'audio/wav',
                                        ''
                                    ];

                                    this.mimeType = '';
                                    for (let type of mimeTypes) {
                                        if (MediaRecorder.isTypeSupported(type)) {
                                            this.mimeType = type;
                                            console.log(`Using MIME type: ${type}`);
                                            break;
                                        }
                                    }

                                    const options = {
                                        mimeType: this.mimeType,
                                        audioBitsPerSecond: 96000
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
                                            const audioBlob = new Blob(this.audioChunks, { type: this.mimeType });
                                            this.audioUrl = URL.createObjectURL(audioBlob);
                                            console.log('Audio URL set:', this.audioUrl);
                                        } catch (error) {
                                            console.error('Error processing audio:', error);
                                            alert('خطا در پردازش صدا.');
                                        }

                                        clearInterval(this.timerInterval);
                                    };

                                    this.mediaRecorder.start(1000);
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

                            async saveRecording() {
                                try {
                                    const preferredMimeType = MediaRecorder.isTypeSupported('audio/mp3') ? 'audio/mp3' : 'audio/webm';
                                    const audioBlob = new Blob(this.audioChunks, { type: preferredMimeType });

                                    const extension = preferredMimeType.split('/')[1];
                                    const formData = new FormData();
                                    formData.append('voice', audioBlob, `voice.${extension}`);
                                    formData.append('user_id', '{{$this->record->id}}');
                                    formData.append('blood_pressure_ids', '{{$ids}}');
                                    formData.append('notes', this.voiceNotes);

                                    const response = await fetch('/blood-pressure/upload-voice', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                        },
                                        body: formData
                                    });

                                    const result = await response.json();
                                    if (result.success) {
                                        alert('توضیحات صوتی با موفقیت ذخیره شد');
                                        this.resetRecording();
                                    } else {
                                        alert('خطا در ذخیره توضیحات صوتی');
                                    }
                                } catch (error) {
                                    console.error('Error saving recording:', error);
                                    alert('خطا در ذخیره ضبط صدا');
                                }
                            },

                            resetRecording() {
                                this.audioUrl = null;
                                this.audioChunks = [];
                                this.duration = 0;
                                this.voiceNotes = '';
                                this.$refs.timer.textContent = '00:00';

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
                                x-show="!isRecording && !audioUrl"
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

                        <div x-show="audioUrl" class="space-y-4 mt-4">
                            <audio x-ref="audioPlayer" controls class="w-full" x-bind:src="audioUrl"></audio>
                            <div class="mt-4">
                                <label for="voice_notes" class="block text-sm font-medium text-gray-700 mb-2">یادداشت پزشک</label>
                                <textarea
                                    id="voice_notes"
                                    x-model="voiceNotes"
                                    rows="3"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="توضیحات خود را اینجا وارد کنید..."
                                ></textarea>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button
                                    type="button"
                                    @click="saveRecording()"
                                    class="inline-flex items-center px-6 py-3 font-bold rounded-lg bg-orange-600 hover:bg-orange-700 focus:outline-none transition-colors"
                                >
                                    <svg class="w-5 h-5 ml-2 rtl:ml-reverse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    ارسال پاسخ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

      

    </div>
    @else
                                    <div class="container text-center">
                                        <h4>اطلاعاتی یافت نشد</h4>
                                        <h4>بیمار هنوز فشار خون جدیدی ثبت نکرده است</h4>
                                    </div>
    @endif
      <!-- Last Doctor Voice Recordings Section -->
      <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-lg font-semibold mb-4">توضیحات صوتی قبلی</h3>
                <div class="space-y-4">
                    

                    @if($voices->count() > 0)
                        @foreach($voices as $voice)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <span class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($voice->created_at)->toJalali()->formatJalaliDateTime() }}
                                        </span>
                                        <button 
                                            type="button"
                                            onclick="showBloodPressureData('{{ $voice->blood_pressure_ids }}')"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md  bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            مشاهده فشار خون
                                        </button>
                                    </div>
                                </div>
                                <audio controls class="w-full">
                                    <source src="{{ asset('storage/' . $voice->voice_path) }}" type="audio/mpeg">
                                    مرورگر شما از پخش صدا پشتیبانی نمی‌کند.
                                </audio>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500">
                            هیچ توضیح صوتی قبلی وجود ندارد
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Blood Pressure Data Modal -->
        <div id="bloodPressureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">اطلاعات فشار خون</h3>
                    <button onclick="closeBloodPressureModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="bloodPressureData" class="space-y-4">
                    <!-- Data will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</x-filament::widget>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('voiceRecorder', () => ({
            isRecording: false,
            mediaRecorder: null,
            audioStream: null,
            audioUrl: null,
            startTime: null,
            timerInterval: null,
            duration: 0,
            audioChunks: [],
            mimeType: '',
            voiceNotes: '',

            async startRecording() {
                try {
                    this.audioUrl = null;
                    this.voiceNotes = '';
                    this.audioStream = await navigator.mediaDevices.getUserMedia({
                        audio: {
                            echoCancellation: true,
                            noiseSuppression: true,
                            channelCount: 1
                        }
                    });

                    const mimeTypes = [
                        'audio/webm;codecs=opus',
                        'audio/webm',
                        'audio/ogg;codecs=opus',
                        'audio/ogg',
                        'audio/mp4;codecs=mp4a',
                        'audio/mp4',
                        'audio/aac',
                        'audio/wav',
                        ''
                    ];

                    this.mimeType = '';
                    for (let type of mimeTypes) {
                        if (MediaRecorder.isTypeSupported(type)) {
                            this.mimeType = type;
                            console.log(`Using MIME type: ${type}`);
                            break;
                        }
                    }

                    const options = {
                        mimeType: this.mimeType,
                        audioBitsPerSecond: 96000
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
                            const audioBlob = new Blob(this.audioChunks, { type: this.mimeType });
                            this.audioUrl = URL.createObjectURL(audioBlob);
                            console.log('Audio URL set:', this.audioUrl);
                        } catch (error) {
                            console.error('Error processing audio:', error);
                            alert('خطا در پردازش صدا.');
                        }

                        clearInterval(this.timerInterval);
                    };

                    this.mediaRecorder.start(1000);
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

            async saveRecording() {
                try {
                    const preferredMimeType = MediaRecorder.isTypeSupported('audio/mp3') ? 'audio/mp3' : 'audio/webm';
                    const audioBlob = new Blob(this.audioChunks, { type: preferredMimeType });

                    const extension = preferredMimeType.split('/')[1];
                    const formData = new FormData();
                    formData.append('voice', audioBlob, `voice.${extension}`);
                    formData.append('user_id', '{{$this->record->id}}');
                    formData.append('blood_pressure_ids', '{{$ids}}');
                    formData.append('notes', this.voiceNotes);

                    const response = await fetch('/blood-pressure/upload-voice', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (result.success) {
                        alert('توضیحات صوتی با موفقیت ذخیره شد');
                        this.resetRecording();
                    } else {
                        alert('خطا در ذخیره توضیحات صوتی');
                    }
                } catch (error) {
                    console.error('Error saving recording:', error);
                    alert('خطا در ذخیره ضبط صدا');
                }
            },

            resetRecording() {
                this.audioUrl = null;
                this.audioChunks = [];
                this.duration = 0;
                this.voiceNotes = '';
                this.$refs.timer.textContent = '00:00';

                if (this.audioStream) {
                    this.audioStream.getTracks().forEach(track => track.stop());
                }
            }
        }));
    });
</script>
@endpush