<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PushNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('charts.deal_chart');
        //return response()->json($data);
    }

    public function timeBasedPerformance()
    {
        return view('charts.timebased_performance');
    }

    public function monthlyClaims($start_date, $end_date)
    {
        $dealClaims = DB::table('claim_deals')
            ->selectRaw("DATE_FORMAT(created_at, '%b %y') as label, COUNT(*) as deal_claim")
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('label')
            ->orderByRaw("STR_TO_DATE(label, '%b %y')")
            ->get();

        return $dealClaims;
    }

    public function dailyClaims($start_date, $end_date)
    {
        $dealClaims = DB::table('claim_deals')
            ->selectRaw('DATE(created_at) as label, COUNT(*) as deal_claim')
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('label')
            ->orderBy('label')
            ->get();
        return $dealClaims;
    }

    public function monthlyDealClicks($start_date, $end_date)
    {
        $dealClicks = DB::table('deal_clicks')
            ->selectRaw("DATE_FORMAT(created_at, '%b %y') as label, COUNT(*) as clicks")
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('label')
            ->orderByRaw("STR_TO_DATE(label, '%b %y')")
            ->get();
        return $dealClicks;

    }
    public function dailyDealClicks($start_date, $end_date)
    {
        $dealClicks = DB::table('deal_clicks')
            ->selectRaw("DATE(created_at) as label, count(*) as clicks")
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('label')
            ->orderBy('label')
            ->get();
        return $dealClicks;
    }

    public function getChartData(Request $request)
    {
        $claims = [];
        $clicks = [];
        $interval = $request->interval;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if($interval == 'Monthly'){
            $claims = $this->monthlyClaims($start_date, $end_date);
            $clicks = $this->monthlyDealClicks($start_date, $end_date);
        }elseif ($interval == 'Daily') {
            $claims = $this->dailyClaims($start_date, $end_date);
            $clicks = $this->dailyDealClicks($start_date, $end_date);
        }elseif ($interval == 'Weekly') {

        }
        //print_r($claims);exit;
        return response()->json([
            'clmlabels' => $claims->pluck('label')->toArray(),
            'claims' => $claims->pluck('deal_claim'), // Y-axis values (Clicks)
            'clklabels' => $clicks->pluck('label')->toArray(),
            'clicks' => $clicks->pluck('clicks'), // Y-axis values (Clicks)
        ]);
    }

    public function gettimeBasedPerformance(Request $request)
    {
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();
        $claims = [];
        $claims = DB::table('claim_deals')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%l %p') AS hour_in_est"), // Format hour without timezone conversion
                DB::raw('COUNT(*) AS total_claims')
            )
            ->whereBetween('created_at', [$start_date, $end_date])
            ->groupBy('hour_in_est')
            ->orderBy(DB::raw('HOUR(MIN(created_at))'), 'ASC') // Order by hour directly
            ->get();


        return response()->json([
            'clmlabels' => $claims->pluck('hour_in_est')->toArray(),
            'claims' => $claims->pluck('total_claims'), // Y-axis values (Clicks)
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
