<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Pressure Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/3.1.0/chartjs-plugin-annotation.min.js"></script>
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
                                            <p class="text-sm text-gray-600">Date:</p>
                                            <p class="font-medium">${new Date(bp.date).toLocaleDateString('fa-IR')}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Time:</p>
                                            <p class="font-medium">${new Date(bp.date).toLocaleTimeString('fa-IR')}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Systolic Blood Pressure:</p>
                                            <p class="font-medium">${bp.systolic} mmHg</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Diastolic Blood Pressure:</p>
                                            <p class="font-medium">${bp.diastolic} mmHg</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Heart Rate:</p>
                                            <p class="font-medium">${bp.heart_rate} bpm</p>
                                        </div>
                                        ${bp.notes ? `
                                            <div class="col-span-2">
                                                <p class="text-sm text-gray-600">Notes:</p>
                                                <p class="font-medium">${bp.notes}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        dataContainer.innerHTML = html;
                    } else {
                        dataContainer.innerHTML = '<p class="text-red-500">Error in retrieving data</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    dataContainer.innerHTML = '<p class="text-red-500">Error in retrieving data</p>';
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

            // Initialize the chart
            const chartElement = document.getElementById('line-chart');
            if (chartElement) {
                new Chart(chartElement, {
                    type: 'line',
                    data: {
                        labels: @json($dates),
                        datasets: [
                            {
                                data: @json($all_dia_data),
                                label: 'DBP',
                                borderColor: '#3cba9f',
                                fill: false
                            },
                            {
                                data: @json($all_sys_data),
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
            }
        });
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-center mb-6">ABPM Result</h1>
            
            <div class="patient-table mb-6">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-orange-100">
                            <th colspan="5" class="p-3 text-right">Patient</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" class="p-3 border-b">
                                Name: {{ $record->name }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="p-3 border-b">
                                Age: {{ $record->age }}
                            </td>
                            <td colspan="2" class="p-3 border-b">
                                Gender: {{ $record->sex == 1 ? 'Male' : 'Female' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="abpm-table mb-6">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-orange-100">
                            <th colspan="5" class="p-3 text-right">ABPM Statistics</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-3 border-b"></td>
                            <td class="p-3 border-b">Mean</td>
                            <td class="p-3 border-b">Std. Dev</td>
                            <td class="p-3 border-b">Max</td>
                            <td class="p-3 border-b">Min</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">SYS (mmHg)</td>
                            <td class="p-3 border-b">{{ round($sys_avg, 1) }}</td>
                            <td class="p-3 border-b">{{ round($sys_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($all_sys_data), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($all_sys_data), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">DIA (mmHg)</td>
                            <td class="p-3 border-b">{{ round($dia_avg, 1) }}</td>
                            <td class="p-3 border-b">{{ round($dia_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($all_dia_data), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($all_dia_data), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">HR (bpm)</td>
                            <td class="p-3 border-b">{{ round($hr_avg, 1) }}</td>
                            <td class="p-3 border-b">{{ round($hr_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($all_hr_data), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($all_hr_data), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">MAP (mmHg)</td>
                            <td class="p-3 border-b">{{ round(array_sum($maps) / count($maps), 1) }}</td>
                            <td class="p-3 border-b">{{ round($map_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($maps), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($maps), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">PP (mmHg)</td>
                            <td class="p-3 border-b">{{ round(array_sum($pps) / count($pps), 1) }}</td>
                            <td class="p-3 border-b">{{ round($pp_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($pps), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($pps), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">CO (l/min)</td>
                            <td class="p-3 border-b">{{ round(array_sum($cos) / count($cos), 1) }}</td>
                            <td class="p-3 border-b">{{ round($co_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($cos), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($cos), 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">CI (l/min/m2)</td>
                            <td class="p-3 border-b">{{ round(array_sum($cis) / count($cis), 1) }}</td>
                            <td class="p-3 border-b">{{ round($ci_std, 1) }}</td>
                            <td class="p-3 border-b">{{ round(max($cis), 1) }}</td>
                            <td class="p-3 border-b">{{ round(min($cis), 1) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="chart-container mb-6" style="height: 60vh;">
                <canvas id="line-chart"></canvas>
            </div>

            <div class="result-table mb-6">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-orange-100">
                            <th colspan="3" class="p-3 text-right">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-3 border-b"></td>
                            <td class="p-3 border-b font-bold">Office</td>
                            <td class="p-3 border-b font-bold">Total</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">SYS/DIA (mmgHg)</td>
                            <td class="p-3 border-b">{{ $all_sys_data[0] }} / {{ $all_dia_data[0] }}</td>
                            <td class="p-3 border-b">{{ round($sys_avg, 1) }} / {{ round($dia_avg, 1) }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 border-b">HR (bpm)</td>
                            <td class="p-3 border-b">{{ $all_hr_data[0] }}</td>
                            <td class="p-3 border-b">{{ round($hr_avg, 1) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            
        </div>
    </div>

    <!-- Blood Pressure Data Modal -->
    <div id="bloodPressureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Blood Pressure Information</h3>
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
</body>
</html>