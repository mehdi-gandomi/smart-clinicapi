<?php
namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodPressure;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
class BloodPressureChart extends Widget
{
    protected static string $view = 'filament.resources.user.widgets.blood-pressure-chart';

    public $record;
    protected int | string | array $columnSpan = 'full';
    public function mount($record)
    {
        $this->record = $record;
    }

    protected function getViewData(): array
    {
        // Get current date
        $now = Carbon::now();

        // Find the most recent Saturday
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);

        // Find the next Friday
        $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay();

        // Get daily averages
        $data = BloodPressure::select(
                DB::raw("DATE(date) as day"),
                DB::raw("AVG(systolic) as avg_systolic"),
                DB::raw("AVG(diastolic) as avg_diastolic"),
                DB::raw("AVG(heart_rate) as avg_heart_rate")
            )
            ->where('user_id', $this->record->id)
            ->whereBetween('date', [$startOfWeek->startOfDay(), $endOfWeek->endOfDay()])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Prepare arrays for chart.js
        $labels = [];
        $systolic = [];
        $diastolic = [];

        // Fill all days of the week (even if no data)
        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->locale('fa')->isoFormat('dddd'); // Get weekday name in Farsi
            $dayData = $data->firstWhere('day', $day);
            $systolic[] = $dayData ? round($dayData->avg_systolic, 1) : null;
            $diastolic[] = $dayData ? round($dayData->avg_diastolic, 1) : null;
        }

        return [
            'bloodPressures' => $data,
            'labels' => $labels,
            'systolic' => $systolic,
            'diastolic' => $diastolic,
        ];
    }
}
