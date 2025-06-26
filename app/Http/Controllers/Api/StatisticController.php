<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerFollowUp;

class StatisticController extends Controller
{
    public function followUpStats(Request $request){
        $query = CustomerFollowUp::query();

        // If "date" is present in query, use it — otherwise default to today
        if ($request->has('date')) {
            $dates = explode(' - ', $request->date);

            if (count($dates) === 2) {
                try {
                    $start = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                    $end = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid date format'], 422);
                }
            }
        } else {
            // Default: today only
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $total = $query->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $sent = (clone $query)->where('status', 'sent')->count();
        $failed = (clone $query)->where('status', 'failed')->count();

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }

}
