@php
    function cleanDecimal($number) {
        return ($number == (int)$number) ? (int)$number : $number;
    }
@endphp

<x-filament::widget>
    <div class="p-4 space-y-4">
        {{-- Title --}}
        <h2 class="text-lg font-bold">میانگین و حداکثر فشار خون</h2>

        {{-- Date Filter --}}
        <div>

            <form wire:submit.prevent="$refresh">
                {{ $this->form }}
                <x-filament::button type="submit" color="primary" class="mt-2">
                    بروزرسانی نمودار
                </x-filament::button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead>
                    <tr class="border-b">
                        <th class="px-4 py-2">تاریخ</th>
                        <th class="px-4 py-2">سیستولیک</th>
                        <th class="px-4 py-2">دیاستولیک</th>
                        <th class="px-4 py-2">ضربان قلب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bloodPressures as $bp)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($bp->day)->format('Y/m/d') }}</td>
                            <td class="px-4 py-2">{{ cleanDecimal($bp->avg_systolic) }}</td>
                            <td class="px-4 py-2">{{ cleanDecimal($bp->avg_diastolic) }}</td>
                            <td class="px-4 py-2">{{ cleanDecimal($bp->avg_heart_rate) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center">اطلاعاتی ثبت نشده است</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Chart --}}
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
                        plugins: {
                            legend: { position: 'top' },
                            title: { display: true, text: 'نمودار فشار خون' },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            x: {
                                title: { display: true, text: 'روز هفته' },
                                reverse: true
                            },
                            y: {
                                title: { display: true, text: 'میلیمتر جیوه' }
                            }
                        }
                    }
                });
            }, 100);
        ">
            <canvas id="bpChart-{{ $this->getId() }}" height="150"></canvas>
        </div>
    </div>
</x-filament::widget>
