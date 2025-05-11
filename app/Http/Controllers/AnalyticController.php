<?php

namespace App\Http\Controllers;

use App\Models\analytic;
use App\Http\Controllers\Controller;
use App\Models\DealClick;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $analytics = [];
        return view('analytics.index',['analytics'=>$analytics]);
    }

    public function deal()
    {
        if (request()->has('start_date') && request()->has('end_date')) {
            $startDate = request('start_date') ? Carbon::parse(request('start_date'))->startOfDay() : null;
            $endDate = request('end_date') ? Carbon::parse(request('end_date'))->endOfDay() : null;
            $deal_analytics = DB::table('claim_deals')
                ->leftJoin('deals', 'deals.id', '=', 'claim_deals.deal_id')
                ->selectRaw('count(claim_deals.id) as deal_used, claim_deals.deal_id, deals.description, deals.university_name, deals.business_name, deals.is_active, deals.start_date, deals.expired_date')
                ->whereBetween('claim_deals.created_at', [$startDate, $endDate])
                ->groupBy('claim_deals.deal_id', 'deals.description', 'deals.university_name', 'deals.business_name', 'deals.is_active', 'deals.start_date', 'deals.expired_date')
                ->orderByDesc('deal_used');
            $deal_analytics = $deal_analytics->get();
        }else{
            $deal_analytics = DB::table('claim_deals')
                ->join('deals', 'deals.id', '=', 'claim_deals.deal_id')
                ->selectRaw('count(claim_deals.id) as deal_used, claim_deals.deal_id, deals.description, deals.university_name, deals.business_name, deals.is_active, deals.start_date, deals.expired_date')
                ->groupBy('claim_deals.deal_id', 'deals.description', 'deals.university_name', 'deals.business_name', 'deals.is_active', 'deals.start_date', 'deals.expired_date')
                ->orderByDesc('deal_used');
            $deal_analytics = $deal_analytics->get();
        }


        foreach ($deal_analytics as $deal_analytic) {
            $deal_analytic->trends = $this->trends($deal_analytic->deal_id);
        }
        return view('analytics.deal',['deal_analytics'=>$deal_analytics]);
    }

    public function trends($dealId)
    {
        $dealClaims = DB::table('claim_deals')
            ->select(
                'deal_id',
                DB::raw('YEARWEEK(created_at, 1) as week_number'),
                DB::raw('COUNT(*) as claim_count')
            )
            ->whereRaw('YEARWEEK(created_at, 1) IN (YEARWEEK(CURDATE(), 1), YEARWEEK(CURDATE(), 1) - 1)')
            ->groupBy('deal_id', 'week_number');

        $results = DB::table(DB::raw("(SELECT DISTINCT deal_id FROM claim_deals) as d"))
            ->leftJoinSub($dealClaims, 'this_week', function ($join) {
                $join->on('d.deal_id', '=', 'this_week.deal_id')
                    ->whereRaw('this_week.week_number = YEARWEEK(CURDATE(), 1)');
            })
            ->leftJoinSub($dealClaims, 'last_week', function ($join) {
                $join->on('d.deal_id', '=', 'last_week.deal_id')
                    ->whereRaw('last_week.week_number = YEARWEEK(CURDATE(), 1) - 1');
            })
            ->select(
                'd.deal_id',
                DB::raw('COALESCE(this_week.claim_count, 0) AS this_week_avg_claim'),
                DB::raw('COALESCE(last_week.claim_count, 0) AS last_week_avg_claim')
            )
            ->where('d.deal_id', $dealId) // Filter for a specific deal_id
            ->first();

        $today = Carbon::now();
        $startOfWeek = $today->startOfWeek(); // Default is Monday
        $daysPassed = $today->diffInDays($startOfWeek) + 1; // Include today

//        if($dealId==113){
//            echo $daysPassed;
//            echo $today->diffInDays($startOfWeek);
//            echo $startOfWeek;
//            echo "<br>";
//            echo $daysPassed;
//           echo "<pre>";echo $daysPassed; print_r($results);exit;
//        }
        $avgDiff = ($results->this_week_avg_claim/$daysPassed)-($results->last_week_avg_claim/7);

        if(($results->last_week_avg_claim/7)!=0){
            $percentage = ($avgDiff/($results->last_week_avg_claim/7))*100;
        }else{
            $percentage = $avgDiff*100;
        }

        return round($percentage,2);
    }

    public function dealClick(){
        $clickCount =DB::table('deal_clicks as a')
            ->leftJoin('deals as b', 'a.deal_id', '=', 'b.id')
            ->select('b.deal_title', DB::raw('COUNT(a.id) as total_click'))
            ->groupBy('a.deal_id','b.deal_title')
            ->get();

        //$dealClick = DealClick::all();

        return view('analytics.click',['dealClicks' => $clickCount]);

    }

    public function user()
    {
        $deal_analytics = DB::table('claim_deals')
            ->join('users', 'claim_deals.user_id', '=', 'users.id')
            ->leftJoin('sessions', 'users.id', '=', 'sessions.user_id')  // Join with sessions table
            ->selectRaw('count(claim_deals.id) as deal_used, sum(claim_deals.total_saving) as total_saving,users.id, users.full_name,MAX(users.updated_at) updated_at,MAX(sessions.last_activity) as last_login')
            ->groupBy('users.id', 'users.full_name')
            ->orderByDesc('total_saving')
            ->get();
        foreach ($deal_analytics as $deal) {
            $deal->last_login = $deal->last_login ? Carbon::parse($deal->last_login)->diffForHumans() : '';
            $deal->total_clicks = DealClick::where('user_id', $deal->id)->count();
        }
        return view('analytics.user',['deal_analytics'=>$deal_analytics]);
    }


    public function business()
    {
        $dealStats = DB::table('deals as a')
            ->leftJoin('claim_deals as b', 'a.id', '=', 'b.deal_id')
            ->leftJoin('deal_clicks as c', 'a.id', '=', 'c.deal_id')
            ->selectRaw('a.business_name, a.deal_title, COUNT(b.id) as claims, COUNT(c.id) as clicks')
            ->groupBy('a.business_name', 'a.deal_title')
            ->orderBy('a.business_name')
            ->get();
        return view('analytics.business',['dealStats' => $dealStats]);
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
    public function show(analytic $analytic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(analytic $analytic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, analytic $analytic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(analytic $analytic)
    {
        //
    }
}
