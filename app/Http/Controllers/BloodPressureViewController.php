<?php

namespace App\Http\Controllers;

use App\Models\BloodPressure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BloodPressureViewController extends Controller
{
    public function show(Request $request)
    {
        $ids = $request->query('ids');
        
        if (!$ids) {
            return redirect()->route('dashboard');
        }

        $bloodPressures = BloodPressure::whereIn('id', explode(',', $ids))->get();
        
        if ($bloodPressures->isEmpty()) {
            return redirect()->route('dashboard');
        }

        $record = User::find($bloodPressures->first()->user_id);
        
        $all_sys_data = [];
        $all_dia_data = [];
        $all_hr_data = [];
        $dates = [];
        $maps = [];
        $pps = [];
        $cos = [];
        $cis = [];

        foreach ($bloodPressures as $bp) {
            $all_sys_data[] = $bp->systolic;
            $all_dia_data[] = $bp->diastolic;
            $all_hr_data[] = $bp->heart_rate;
            $dates[] = $bp->date;
            
            // Calculate MAP (Mean Arterial Pressure)
            $map = $bp->diastolic + (($bp->systolic - $bp->diastolic) / 3);
            $maps[] = $map;
            
            // Calculate PP (Pulse Pressure)
            $pp = $bp->systolic - $bp->diastolic;
            $pps[] = $pp;
            
            // Calculate CO (Cardiac Output) - Simplified formula
            $co = ($bp->heart_rate * $pp) / 1000;
            $cos[] = $co;
            
            // Calculate CI (Cardiac Index) - Assuming average body surface area of 1.73 m²
            $ci = $co / 1.73;
            $cis[] = $ci;
        }

        // Calculate averages
        $sys_avg = array_sum($all_sys_data) / count($all_sys_data);
        $dia_avg = array_sum($all_dia_data) / count($all_dia_data);
        $hr_avg = array_sum($all_hr_data) / count($all_hr_data);

        // Calculate standard deviations
        $sys_std = $this->calculateStandardDeviation($all_sys_data);
        $dia_std = $this->calculateStandardDeviation($all_dia_data);
        $hr_std = $this->calculateStandardDeviation($all_hr_data);
        $map_std = $this->calculateStandardDeviation($maps);
        $pp_std = $this->calculateStandardDeviation($pps);
        $co_std = $this->calculateStandardDeviation($cos);
        $ci_std = $this->calculateStandardDeviation($cis);

      

        return view('blood-pressure.view', compact(
            'record',
            'ids',
            'all_sys_data',
            'all_dia_data',
            'all_hr_data',
            'dates',
            'sys_avg',
            'dia_avg',
            'hr_avg',
            'sys_std',
            'dia_std',
            'hr_std',
            'maps',
            'pps',
            'cos',
            'cis',
            'map_std',
            'pp_std',
            'co_std',
            'ci_std'
        ));
    }

    private function calculateStandardDeviation($array)
    {
        $count = count($array);
        if ($count < 2) {
            return 0;
        }

        $mean = array_sum($array) / $count;
        $squaredDifferences = array_map(function($value) use ($mean) {
            return pow($value - $mean, 2);
        }, $array);

        return sqrt(array_sum($squaredDifferences) / ($count - 1));
    }
}