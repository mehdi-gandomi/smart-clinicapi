<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodPressure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;

class BloodPressureChart extends Widget implements HasForms
{

    use InteractsWithForms;
    protected static string $view = 'filament.resources.user.widgets.blood-pressure-chart';
    public $record;

    public $from_date;
    public $to_date;

    protected int | string | array $columnSpan = 'full';

    public function mount($record): void
    {
        $this->record = $record;

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        // Set default filter values
        $this->from_date = $startOfWeek->toDateString();
        $this->to_date = $endOfWeek->toDateString();
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('from_date')
                ->label('از تاریخ')
                ->reactive()
                // ->afterStateUpdated(function ($state, callable $set, callable $get) {
                //     $from = \Carbon\Carbon::parse($state);
                //     $to = $get('to_date') ? \Carbon\Carbon::parse($get('to_date')) : null;

                //     if ($to && $from->diffInDays($to) !== 6) {
                //         // $set('to_date', null);
                //         \Filament\Notifications\Notification::make()
                //             ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
                //             ->danger()
                //             ->send();
                //     }
                // })
                ->required(),

            DatePicker::make('to_date')
                ->label('تا تاریخ')
                ->reactive()
                // ->afterStateUpdated(function ($state, callable $set, callable $get) {
                //     $to = \Carbon\Carbon::parse($state);
                //     $from = $get('from_date') ? \Carbon\Carbon::parse($get('from_date')) : null;

                //     if ($from && $from->diffInDays($to) !== 6) {
                //         // $set('to_date', null);
                //         \Filament\Notifications\Notification::make()
                //             ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
                //             ->danger()
                //             ->send();
                //     }
                // })
                ->required(),
        ];
    }



    protected function getViewData(): array
    {
        $start = Carbon::parse($this->from_date)->startOfDay();
        $end = Carbon::parse($this->to_date)->endOfDay();

        $data = BloodPressure::select(
                DB::raw("DATE(date) as day"),
                DB::raw("AVG(systolic) as avg_systolic"),
                DB::raw("AVG(diastolic) as avg_diastolic"),
                DB::raw("AVG(heart_rate) as avg_heart_rate"),
                DB::raw("MAX(systolic) as max_systolic"),
                DB::raw("MAX(diastolic) as max_diastolic")
            )
            ->where('user_id', $this->record->id)
            ->whereBetween('date', [$start, $end])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $labels = [];
        $systolic = [];
        $diastolic = [];
        $maxSystolic = [];
        $maxDiastolic = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->locale('fa')->isoFormat('dddd');
            $dayData = $data->firstWhere('day', $day);
            $systolic[] = $dayData ? round($dayData->avg_systolic, 1) : null;
            $diastolic[] = $dayData ? round($dayData->avg_diastolic, 1) : null;
            $maxSystolic[] = $dayData ? round($dayData->max_systolic, 1) : null;
            $maxDiastolic[] = $dayData ? round($dayData->max_diastolic, 1) : null;
        }

        return [
            'bloodPressures' => $data,
            'labels' => $labels,
            'systolic' => $systolic,
            'diastolic' => $diastolic,
            'maxSystolic' => $maxSystolic,
            'maxDiastolic' => $maxDiastolic,
        ];
    }
}
