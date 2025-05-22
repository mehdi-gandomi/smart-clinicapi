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
use Illuminate\Support\Facades\Storage;
use App\Models\DoctorBloodPressureVoice;
use Illuminate\Support\Facades\Log;

class BloodPressureChart extends Widget
// implements HasForms
{

    // use InteractsWithForms;
    protected static string $view = 'filament.resources.user.widgets.blood-pressure-chart';
    public $record;



    protected int | string | array $columnSpan = 'full';

    public function mount($record): void
    {
        $this->record = $record;

    }

    // protected function getFormSchema(): array
    // {
    //     return [
    //         Select::make('selected_week')
    //             ->label('انتخاب هفته')
    //             ->options(collect($this->weeks)->pluck('label', 'key')->toArray())
    //             ->reactive()
    //             ->afterStateUpdated(function ($state, callable $set) {
    //                 if ($state) {
    //                     $week = collect($this->weeks)->firstWhere('key', $state);
    //                     if ($week) {
    //                         $set('from_date', $week['start']);
    //                         $set('to_date', $week['end']);
    //                     }
    //                 }
    //             })
    //             ->required(),

    //         DatePicker::make('from_date')
    //             ->label('از تاریخ')
    //             ->jalali()
    //             ->reactive()
    //             ->displayFormat("Y/m/d")
    //             ->afterStateUpdated(function ($state, callable $set, callable $get) {
    //                 $from = Carbon::parse($state);
    //                 $to = $get('to_date') ? Carbon::parse($get('to_date')) : null;

    //                 if ($to && $from->diffInDays($to) !== 6) {
    //                     Notification::make()
    //                         ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
    //                         ->danger()
    //                         ->send();
    //                 }
    //             })
    //             ->required(),

    //         DatePicker::make('to_date')
    //             ->label('تا تاریخ')
    //             ->jalali()
    //             ->reactive()
    //             ->displayFormat("Y/m/d")
    //             ->afterStateUpdated(function ($state, callable $set, callable $get) {
    //                 $to = Carbon::parse($state);
    //                 $from = $get('from_date') ? Carbon::parse($get('from_date')) : null;

    //                 if ($from && $from->diffInDays($to) !== 6) {
    //                     Notification::make()
    //                         ->title('بازه زمانی باید دقیقاً ۷ روز باشد')
    //                         ->danger()
    //                         ->send();
    //                 }
    //             })
    //             ->required(),
    //     ];
    // }



    protected function getViewData(): array
    {


        $bloodPressures = BloodPressure::where('user_id', $this->record->id)
            ->where("examined",0)
            ->orderBy('date')

            ->get();
        $ids=implode(",",$bloodPressures->pluck("id")->toArray());
        $sys_avg=$bloodPressures->avg("systolic");
        $dia_avg=$bloodPressures->avg("diastolic");
        $hr_avg=$bloodPressures->avg("heart_rate");
        $all_sys_data=[];
        $all_dia_data=[];
        $all_hr_data=[];
        $dates=[];
        $maps=[];
        $pps=[];
        $cis=[];
        $cos=[];
        foreach($bloodPressures as $bloodPressure){
            $all_sys_data[]=$bloodPressure->systolic;
            $all_dia_data[]=$bloodPressure->diastolic;
            $all_hr_data[]=$bloodPressure->heart_rate;
            $dates[]=\Carbon\Carbon::parse($bloodPressure->date)->toJalali()->formatJalaliDatetime();
        }
        $total=max(count($all_sys_data),count($all_hr_data),count($all_dia_data));

        for($i=0;$i<$total;$i++){
            if($all_dia_data[$i] == null && $all_sys_data[$i] == null){
                $map=null;
                $pp=null;
                $co=null;
                $ci=null;
            }
            else{
                $map=$all_dia_data[$i] + 0.333 * ($all_sys_data[$i] - $all_dia_data[$i]);
                $pp=$all_sys_data[$i] - $all_dia_data[$i];
                $co=$pp * $all_hr_data[$i] / 1000;
                $ci=$co / (sqrt(($this->record->weight ?? 60) * ($this->record->weight ?? 60) / 3600));
                $maps[]=$map;

                $pps[]=$pp;

                $cos[]=$co;

                $cis[]=$ci;
            }

        }

        $dia_std=$this->std_deviation($all_dia_data);
        $sys_std=$this->std_deviation($all_sys_data);
        $hr_std=$this->std_deviation($all_hr_data);

        $map_std=$this->std_deviation($maps);
        $pp_std=$this->std_deviation($pps);
        $ci_std=$this->std_deviation($cis);
        $co_std=$this->std_deviation($cos);

        return compact('maps','ids','dates','cos','cis','pps','sys_avg','hr_avg','dia_avg','dia_std','sys_std','hr_std','map_std','pp_std','ci_std','co_std','all_sys_data','all_dia_data','all_hr_data');
    }

    public function std_deviation($data)
   {
        if(count($data) < 1) return 0;
        $n = count($data);
        $mean = array_sum($data) / $n;
        $distance_sum = 0;
        foreach ($data as $i) {  $distance_sum += ($i - $mean) ** 2;}
        $variance = $distance_sum / $n;
        return number_format(sqrt($variance),1);
   }

 
}
