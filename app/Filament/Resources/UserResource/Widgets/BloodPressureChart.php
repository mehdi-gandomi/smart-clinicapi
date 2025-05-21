<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodPressure;
use Filament\Forms\Components\Select;
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

    public $selected_week = null;
    public $weeks = [];
    public $from_date;
    public $to_date;

    protected int | string | array $columnSpan = 'full';

    public function mount($record): void
    {
        $this->record = $record;
        $this->weeks = $this->getWeeksWithData();

        // Set default dates to current week
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SATURDAY);
        $endOfWeek = $startOfWeek->copy()->addDays(6);

        $this->from_date = $startOfWeek->toDateString();
        $this->to_date = $endOfWeek->toDateString();

        // Default to the most recent week if available
        if (!$this->selected_week && count($this->weeks)) {
            $this->selected_week = $this->weeks[0]['key'];
            $this->from_date = $this->weeks[0]['start'];
            $this->to_date = $this->weeks[0]['end'];
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('selected_week')
                ->label('انتخاب هفته')
                ->options(collect($this->weeks)->pluck('label', 'key')->toArray())
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $week = collect($this->weeks)->firstWhere('key', $state);
                        if ($week) {
                            $set('from_date', $week['start']);
                            $set('to_date', $week['end']);
                        }
                    }
                })
                ->required(),

            DatePicker::make('from_date')
                ->label('از تاریخ')
                ->jalali()
                ->reactive()
                ->displayFormat("Y/m/d")
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $from = Carbon::parse($state);
                    $to = $get('to_date') ? Carbon::parse($get('to_date')) : null;

                    if ($to && $from->diffInDays($to) !== 6) {
                        Notification::make()
                            ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
                            ->danger()
                            ->send();
                    }
                })
                ->required(),

            DatePicker::make('to_date')
                ->label('تا تاریخ')
                ->jalali()
                ->reactive()
                ->displayFormat("Y/m/d")
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $to = Carbon::parse($state);
                    $from = $get('from_date') ? Carbon::parse($get('from_date')) : null;

                    if ($from && $from->diffInDays($to) !== 6) {
                        Notification::make()
                            ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
                            ->danger()
                            ->send();
                    }
                })
                ->required(),
        ];
    }

    /**
     * Get all week ranges (Saturday to Friday) with blood pressure data for the user.
     * Returns array: [['key' => '2024-05-04_2024-05-10', 'label' => '1403/02/15 - 1403/02/21'], ...]
     */
    public function getWeeksWithData(): array
    {
        $dates = BloodPressure::where('user_id', $this->record->id)
            ->orderBy('date')
            ->pluck('date');
        if ($dates->isEmpty()) return [];

        $weeks = [];
        foreach ($dates as $date) {
            $carbon = Carbon::parse($date);
            $startOfWeek = $carbon->copy()->startOfWeek(Carbon::SATURDAY)->startOfDay();
            $endOfWeek = $startOfWeek->copy()->addDays(6)->endOfDay();
            $key = $startOfWeek->toDateString() . '_' . $endOfWeek->toDateString();
            $label = verta($startOfWeek)->format('Y/m/d') . ' - ' . verta($endOfWeek)->format('Y/m/d');
            $weeks[$key] = [
                'key' => $key,
                'label' => $label,
                'start' => $startOfWeek->toDateString(),
                'end' => $endOfWeek->toDateString(),
            ];
        }
        // Remove duplicates and sort descending (most recent first)
        $weeks = array_values(array_reverse(array_unique($weeks, SORT_REGULAR)));
        return $weeks;
    }

    protected function getViewData(): array
    {
        $start = Carbon::parse($this->from_date)->startOfDay();
        $end = Carbon::parse($this->to_date)->endOfDay();

        $data = BloodPressure::select(
                DB::raw("DATE(date) as day"),
                DB::raw("date"),
                DB::raw("AVG(systolic) as avg_systolic"),
                DB::raw("AVG(diastolic) as avg_diastolic"),
                DB::raw("AVG(heart_rate) as avg_heart_rate"),
                DB::raw("MAX(systolic) as max_systolic"),
                DB::raw("MAX(diastolic) as max_diastolic")
            )
            ->where('user_id', $this->record->id)
            ->whereBetween('date', [$start, $end])
            ->groupBy('day')
            ->groupBy('date')
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
            'weeks' => $this->weeks,
            'selected_week' => $this->selected_week,
        ];
    }
}
