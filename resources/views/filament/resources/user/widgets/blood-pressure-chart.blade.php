@php
    function cleanDecimal($number) {
        return ($number == (int)$number) ? (int)$number : $number;
    }
@endphp

<x-filament::widget>
    <div class="p-2 sm:p-4 space-y-4">
        {{-- Title --}}
        <h2 class="text-base sm:text-lg font-bold">میانگین و حداکثر فشار خون</h2>

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4">
            <form wire:submit.prevent="$refresh" class="space-y-4">
                {{-- Date Range Picker and Week Selector --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{ $this->form }}
                </div>

                {{-- Refresh Button --}}
                <div>
                    <x-filament::button type="submit" color="primary" class="w-full sm:w-auto">
                        بروزرسانی نمودار
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 whitespace-nowrap">تاریخ</th>
                            <th class="px-2 sm:px-4 py-2 whitespace-nowrap">سیستولیک</th>
                            <th class="px-2 sm:px-4 py-2 whitespace-nowrap">دیاستولیک</th>
                            <th class="px-2 sm:px-4 py-2 whitespace-nowrap">ضربان قلب</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($bloodPressures as $bp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($bp->date)->format('Y/m/d H:i') }}</td>
                                <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ cleanDecimal($bp->avg_systolic) }}</td>
                                <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ cleanDecimal($bp->avg_diastolic) }}</td>
                                <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ cleanDecimal($bp->avg_heart_rate) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-2 sm:px-4 py-2 text-center text-gray-500">اطلاعاتی ثبت نشده است</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Chart --}}
        <div class="bg-white rounded-lg shadow-sm p-2 sm:p-4">
            <div x-data x-init="
                setTimeout(() => {
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js is not loaded');
                        return;
                    }
                    if (window.bpChart) window.bpChart.destroy();
                    const ctx = document.getElementById('bpChart-{{ $this->getId() }}').getContext('2d');
                    const labels = {{ Js::from($labels) }};
                    const systolic = {{ Js::from($systolic) }};
                    const diastolic = {{ Js::from($diastolic) }};
                    const maxSystolic = {{ Js::from($maxSystolic) }};
                    const maxDiastolic = {{ Js::from($maxDiastolic) }};

                    window.bpChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [
                                {
                                    label: 'میانگین سیستولیک',
                                    data: systolic,
                                    borderColor: 'rgb(255, 99, 132)',
                                    fill: false,
                                    spanGaps: true,
                                },
                                {
                                    label: 'میانگین دیاستولیک',
                                    data: diastolic,
                                    borderColor: 'rgb(54, 162, 235)',
                                    fill: false,
                                    spanGaps: true,
                                },
                                {
                                    label: 'حداکثر سیستولیک',
                                    data: maxSystolic,
                                    borderColor: 'rgb(255, 0, 0)',
                                    borderDash: [5, 5],
                                    fill: false,
                                    spanGaps: true,
                                },
                                {
                                    label: 'حداکثر دیاستولیک',
                                    data: maxDiastolic,
                                    borderColor: 'rgb(0, 0, 255)',
                                    borderDash: [5, 5],
                                    fill: false,
                                    spanGaps: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { 
                                    position: 'top',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10,
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                title: { 
                                    display: true, 
                                    text: 'نمودار فشار خون',
                                    font: {
                                        size: 14
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    bodyFont: {
                                        size: 12
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: { 
                                        display: true, 
                                        text: 'روز هفته',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    reverse: true,
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                },
                                y: {
                                    title: { 
                                        display: true, 
                                        text: 'میلیمتر جیوه',
                                        font: {
                                            size: 12
                                        }
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }, 100);
            ">
                <div class="relative w-full" style="height: 300px;">
                    <canvas id="bpChart-{{ $this->getId() }}" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>
    </div>
</x-filament::widget>
