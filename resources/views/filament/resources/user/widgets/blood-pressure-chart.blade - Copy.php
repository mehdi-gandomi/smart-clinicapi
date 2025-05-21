{{-- resources/views/filament/widgets/bp-chart-widget.blade.php --}}
<x-filament::widget>
    <div class="p-4">
        <h2 class="text-lg font-bold mb-4">میانگین و حداکثر فشار خون</h2>

        {{-- Date Filters --}}
        <div class="mb-4">
            <form wire:submit.prevent="filter" class="flex flex-wrap gap-3 items-end">
                {{ $this->form }}
                <div>
                    <x-filament::button type="submit">
                        فیلتر
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Blood Pressure Data Table --}}
        <div class="mb-4 overflow-x-auto">
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
                            <td class="px-4 py-2">{{ round($bp->avg_systolic, 1) }}</td>
                            <td class="px-4 py-2">{{ round($bp->avg_diastolic, 1) }}</td>
                            <td class="px-4 py-2">{{ round($bp->avg_heart_rate, 1) }}</td>
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
        <div
            x-data="{}"
            x-init="
                setTimeout(() => {
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js is not loaded');
                        return;
                    }
                    const ctx = document.getElementById('bpChart-{{ $this->getId() }}').getContext('2d');
                    const labels = {{ Js::from($labels) }};
                    const systolic = {{ Js::from($systolic) }};
                    const diastolic = {{ Js::from($diastolic) }};
                    const maxSystolic = {{ Js::from($maxSystolic) }};
                    const maxDiastolic = {{ Js::from($maxDiastolic) }};

                    let chartInstance = window.bpChart{{ $this->getId() }};
                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    window.bpChart{{ $this->getId() }} = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
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
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    fill: false,
                                    spanGaps: true,
                                },
                                {
                                    label: 'حداکثر دیاستولیک',
                                    data: maxDiastolic,
                                    borderColor: 'rgb(0, 0, 255)',
                                    borderWidth: 2,
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
                                title: {
                                    display: true,
                                    text: 'میانگین و حداکثر فشار خون - ' + '{{ \Carbon\Carbon::parse($startDate)->format("Y/m/d") }}' + ' تا ' + '{{ \Carbon\Carbon::parse($endDate)->format("Y/m/d") }}'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                x: {
                                    title: { display: true, text: 'روز' },
                                },
                                y: { title: { display: true, text: 'میلیمتر جیوه' } }
                            }
                        }
                    });
                }, 100);
            "
        >
            <canvas id="bpChart-{{ $this->getId() }}" height="150"></canvas>
        </div>
    </div>
</x-filament::widget>