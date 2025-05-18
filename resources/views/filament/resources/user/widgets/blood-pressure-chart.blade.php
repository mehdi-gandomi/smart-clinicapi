{{-- resources/views/filament/widgets/bp-chart-widget.blade.php --}}

<x-filament::widget>
    <div class="p-2 sm:p-4">
        <h2 class="text-base sm:text-lg font-bold mb-4">میانگین فشار خون هفته جاری</h2>

        {{-- Blood Pressure Data Table --}}
        <div class="mb-4 overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-[400px] w-full text-xs sm:text-sm text-right">
                <thead>
                    <tr class="border-b">
                        <th class="px-2 sm:px-4 py-2">تاریخ</th>
                        <th class="px-2 sm:px-4 py-2">سیستولیک</th>
                        <th class="px-2 sm:px-4 py-2">دیاستولیک</th>
                        <th class="px-2 sm:px-4 py-2">ضربان قلب</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bloodPressures as $bp)
                        <tr class="border-b">
                            <td class="px-2 sm:px-4 py-2">{{ \Carbon\Carbon::parse($bp->date)->format('Y/m/d H:i') }}</td>
                            <td class="px-2 sm:px-4 py-2">{{ $bp->avg_systolic }}</td>
                            <td class="px-2 sm:px-4 py-2">{{ $bp->avg_diastolic }}</td>
                            <td class="px-2 sm:px-4 py-2">{{ $bp->avg_heart_rate }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 sm:px-4 py-2 text-center">اطلاعاتی ثبت نشده است</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Chart --}}
        <div
            class="w-full max-w-full sm:max-w-2xl mx-auto"
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

                    new Chart(ctx, {
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
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' },
                                title: { display: true, text: 'میانگین فشار خون هفته جاری' }
                            },
                            scales: {
                                x: {
                                    title: { display: true, text: 'روز هفته' },
                                    reverse: true // Show Saturday first
                                },
                                y: { title: { display: true, text: 'میلیمتر جیوه' } }
                            }
                        }
                    });
                }, 100);
            "
        >
            <div class="relative w-full" style="height:220px;">
                <canvas id="bpChart-{{ $this->getId() }}" class="w-full h-full" style="max-width:100%;"></canvas>
            </div>
        </div>
    </div>
</x-filament::widget>