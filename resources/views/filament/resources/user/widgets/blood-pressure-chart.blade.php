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

</script>
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
                                {{ $sys_avg }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $sys_std }} </td>
                            <td class="border-end-0 border-start-0">
                                {{ max_number($all_sys_data) }}
                            </td>
                            <td class=" border-start-0">
                                {{ min_number($all_sys_data) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                DIA (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $dia_avg }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $dia_std }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ max_number($all_dia_data) }}
                            </td>
                            <td class=" border-start-0">
                                {{ min_number($all_dia_data) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                HR (bpm)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $hr_avg }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $hr_std }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ max_number($all_hr_data) }}
                            </td>
                            <td class=" border-start-0">
                                {{ min_number($all_hr_data) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="border-end-0">
                                MAP (mmHg)
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ avg($maps) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $map_std }}
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
                                {{ avg($pps) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $pp_std }}
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
                                {{ avg($cos) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $co_std }}
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
                                {{ avg($cis) }}
                            </td>
                            <td class="border-end-0 border-start-0">
                                {{ $ci_std }}
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
        </div>
        </div>

    </div>

</x-filament::widget>
