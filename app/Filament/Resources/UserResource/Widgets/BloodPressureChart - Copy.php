<?php
namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\BloodPressure;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;

class BloodPressureChart extends Widget
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.bp-chart-widget';

    public $record;
    protected int | string | array $columnSpan = 'full';

    // Add filter properties
    public ?string $startDate = null;
    public ?string $endDate = null;

    public function __construct()
    {
        // Initialize forms here to avoid property not found error
        $this->setUp();
    }

    protected function setUp(): void
    {
        // Set up the forms
        $this->registerForms([
            'form' => $this->makeForm()
                ->statePath('data'),
        ]);
    }

    protected function makeForm(): Form
    {
        return Form::make()
            ->schema([
                DatePicker::make('startDate')
                    ->label('از تاریخ')
                    ->default(function () {
                        // Find the most recent Saturday (First day of week)
                        return Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
                    })
                    ->required(),
                DatePicker::make('endDate')
                    ->label('تا تاریخ')
                    ->default(function () {
                        // Find the next Friday (Last day of week)
                        return Carbon::now()->startOfWeek(Carbon::SATURDAY)->addDays(6)->format('Y-m-d');
                    })
                    ->required(),
            ]);
    }

    public function mount($record): void
    {
        $this->record = $record;
        // Set default values for date filters
        if (!$this->startDate) {
            $this->startDate = Carbon::now()->startOfWeek(Carbon::SATURDAY)->format('Y-m-d');
        }
        if (!$this->endDate) {
            $this->endDate = Carbon::now()->startOfWeek(Carbon::SATURDAY)->addDays(6)->format('Y-m-d');
        }

        $this->form->fill([
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function filter(): void
    {
        $data = $this->form->getState();
        $this->startDate = $data['startDate'];
        $this->endDate = $data['endDate'];
    }

    protected function getViewData(): array
    {
        // Parse dates for filtering
        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();

        // Get daily averages and maximums
        $data = BloodPressure::select(
                DB::raw("DATE(date) as day"),
                DB::raw("AVG(systolic) as avg_systolic"),
                DB::raw("AVG(diastolic) as avg_diastolic"),
                DB::raw("AVG(heart_rate) as avg_heart_rate"),
                DB::raw("MAX(systolic) as max_systolic"),
                DB::raw("MAX(diastolic) as max_diastolic")
            )
            ->where('user_id', $this->record->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Prepare arrays for chart.js
        $labels = [];
        $systolic = [];
        $diastolic = [];
        $maxSystolic = [];
        $maxDiastolic = [];

        // Create date period for all days in the selected range
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        // Fill all days in the selected date range (even if no data)
        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->locale('fa')->isoFormat('dddd'); // Get weekday name in Farsi
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
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }
}