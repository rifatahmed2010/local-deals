<?php

namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\ClaimDeal;
use App\Models\DealClick;
use App\Models\PushNotification;
use App\Models\Tag;
use App\Models\University;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Post\StoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;


class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $deals = Deal::where('is_active', true)
            ->orderBy('position', 'asc')       // Order in ascending order
            ->orderBy('updated_at', 'desc') // Order by created_at in descending order
//            ->orderBy('id', 'desc')         // Order by ID in descending order
            ->get();

        return view('deals.index',['deals'=>$deals]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = Tag::all();
        $universities = University::all();
        return view('deals.create',['tags' => $tags,'universities' => $universities]);
    }

    public function store(Request $request)
    {
        $deal = new Deal();
        $deal->deal_title = $request->deal_title;
        $deal->description = $request->description;
        $deal->deal_category = $request->deal_category;
        $deal->deal_type = $request->deal_type;
        $deal->tag_name = is_array($request->tag_name) ? implode(',', $request->tag_name) : $request->tag_name;
        $deal->university_name = is_array($request->university_name) ? implode(',', $request->university_name) : $request->university_name;
        $deal->business_name = $request->business_name;
        $deal->location = $request->location;
        $deal->uses = "";
        $deal->total_saving = $request->total_saving;
        $deal->start_date = $request->start_date;
        $deal->expired_date = $request->expired_date;
        $deal->position = 1;

        if ($request->hasFile('deal_image_path') && $request->file('deal_image_path')->isValid()) {
            $deal->addMediaFromRequest('deal_image_path')->toMediaCollection('deal_image_path');
        }
        $deal->deal_image_path = "";
        $deal->save();

        $url = $deal->getFirstMediaUrl('deal_image_path', 'thumb');
        $deal->deal_image_path = $url;
        $deal->save();
        return redirect()->route('deals.index')->with('success', 'Deal Saved Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Deal $deal)
    {
        return view('deals.show',['deal'=>$deal]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deal $deal)
    {
        $tags = Tag::all();
        $universities = University::all();
        return view('deals.edit',['deal'=>$deal,'tags' => $tags,'universities' => $universities]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Deal $deal)
    {
        $deal->deal_title = $request->deal_title;
        $deal->description = $request->description;
        $deal->deal_category = $request->deal_category;
        $deal->deal_type = $request->deal_type;
        $deal->tag_name = is_array($request->tag_name) ? implode(',', $request->tag_name) : $request->tag_name;
        $deal->university_name = is_array($request->university_name) ? implode(',', $request->university_name) : $request->university_name;
        $deal->business_name = $request->business_name;
        $deal->location = $request->location;
        $deal->uses = "";
        $deal->total_saving = $request->total_saving;
        $deal->start_date = $request->start_date;
        $deal->expired_date = $request->expired_date;

        if ($request->hasFile('deal_image_path') && $request->file('deal_image_path')->isValid()) {
            $deal->media()->delete();
            $deal->addMediaFromRequest('deal_image_path')->toMediaCollection('deal_image_path');
        }
        $deal->save();

        if ($request->hasFile('deal_image_path') && $request->file('deal_image_path')->isValid()) {
            $url = $deal->getFirstMediaUrl('deal_image_path', 'thumb');
            $deal->deal_image_path = $url;
            $deal->save();
        }
        return redirect()->route('deals.index')->with('success', 'Deal Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $deal = Deal::find($request->id);
        $deal->is_active = 0;
        $deal->save();
        return redirect()->route('deals.index')->with('success', 'Data Deleted Successfully');

    }

    /*API Start*/

    /*Anonymous*/

    public function featureDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = 5;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $one_time_deals = Deal::where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->where('deal_type','!=','Self Care')
        ->skip($skip)->take($size)->orderByDesc('total_saving')->distinct()->get();
        return $this->returnSuccess("Feature Deal List",$one_time_deals);
    }

    public function dailyDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $daily_deals = Deal::select('deals.*')
            ->distinct()
            ->leftJoin('claim_deals', 'deals.id', '=', 'claim_deals.deal_id')
            ->where('deals.deal_type','Everyday Exclusives')
            ->where('deals.start_date','<=',$today)
            ->where('deals.expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('deals.id')->get();
        return $this->returnSuccess("Daily Deal List",$daily_deals);
    }
    public function oneTimeDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $one_time_deals = Deal::where('deal_type','One-Time')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("One Time Deal List",$one_time_deals);
    }

    public function monthlyDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $monthly_deals = Deal::where('deal_type','Monthly Specials')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Monthly Deal List",$monthly_deals);
    }

    public function weeklyDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $weekly_deals = Deal::where('deal_type','Weekly Wins')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Weekly Deal List",$weekly_deals);
    }

    public function limitedDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $limited_deals = Deal::where('deal_type','Self Care')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Limited Deal List",$limited_deals);
    }

    public function anytimeDealListA(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $anytime_deals = Deal::where('deal_type','Anytime')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Anytime Deal List",$anytime_deals);
    }

    public function searchDealListA(Request $request)
    {
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;

        if($request->dealCategory && $request->searchKey){
            $searchKey = $request->searchKey;
            $deals = Deal::where('deal_category',$request->dealCategory)
                ->where('description','like','%'.$searchKey.'%')
                ->where('is_active',1)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }elseif($request->searchKey){
            $searchKey = $request->searchKey;
            $deals = Deal::where('description','like','%'.$searchKey.'%')
                ->where('is_active',1)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }elseif ($request->dealCategory){
            $deals = Deal::where('deal_category',$request->dealCategory)
                ->where('is_active',1)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }
        return $this->returnSuccess("Search Deal List",$deals);
    }

    /*Anonymous*/

    public function featureDealList(Request $request)
    {
        $user = Auth::user();
        $size = 5;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $today = date("Y-m-d");

        $monthly_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereMonth('created_at',Carbon::now()->month)
            ->where('deal_type','=','Monthly Specials')->get()->pluck('deal_id');

        $weekly_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
            ->where('deal_type','=','Weekly Wins')->get()->pluck('deal_id');

        $limited_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->where('deal_type','=','Self Care')->get()->pluck('deal_id');

        $daily_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereDate('created_at','=',Carbon::today())
            ->where('deal_type','=','Everyday Exclusives')->get()->pluck('deal_id');


        $deals_claimed = array_merge($daily_deals_claimed->toArray(),$monthly_deals_claimed->toArray(),$weekly_deals_claimed->toArray(),$limited_deals_claimed->toArray());
        $feature_deals = Deal::where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->whereNotIn('deals.id',$deals_claimed)
            ->where('deal_type','!=','Self Care')
            ->skip($skip)->take($size)->orderByDesc('total_saving')->distinct()->get();
            return $this->returnSuccess("Feature Deal List",$feature_deals);
    }

    public function dailyDealList(Request $request)
    {
        $today = date("Y-m-d");
        $user = Auth::user();
        $deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereDate('created_at','=',Carbon::today())
            ->where('deal_type','=','Everyday Exclusives')->get()->pluck('deal_id');
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $daily_deals = Deal::select('deals.*')->distinct()
            ->leftJoin('claim_deals', 'deals.id', '=', 'claim_deals.deal_id')
            ->where('deals.deal_type','Everyday Exclusives')
            ->where('deals.start_date','<=',$today)
            ->where('deals.expired_date','>=',$today)
            ->where('is_active',1)
            ->whereNotIn('deals.id',$deals_claimed)
             ->skip($skip)->take($size)
             ->orderByDesc('deals.id')
             ->get();
        //echo $daily_deals;
        //exit;
        return $this->returnSuccess("Daily Deal List",$daily_deals);
    }
    public function oneTimeDealList(Request $request)
    {
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $today = date("Y-m-d");
        $skip = ($page - 1) * $size;
        $one_time_deals = Deal::where('deal_type','One-Time')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("One Time Deal List",$one_time_deals);
    }

    public function monthlyDealList(Request $request)
    {
        $user = Auth::user();
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $today = date("Y-m-d");
        $skip = ($page - 1) * $size;

        $deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereMonth('created_at',Carbon::now()->month)
            ->where('deal_type','=','Monthly Specials')->get()->pluck('deal_id');

        $monthly_deals = Deal::where('deal_type','Monthly Specials')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->whereNotIn('deals.id',$deals_claimed)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Monthly Deal List",$monthly_deals);
    }

    public function weeklyDealList(Request $request)
    {
        $user = Auth::user();
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $today = date("Y-m-d");
        $deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
            ->where('deal_type','=','Weekly Wins')->get()->pluck('deal_id');

        $weekly_deals = Deal::where('deal_type','Weekly Wins')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->whereNotIn('deals.id',$deals_claimed)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Weekly Deal List",$weekly_deals);
    }

    public function limitedDealList(Request $request)
    {
        $user = Auth::user();
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->where('deal_type','=','Self Care')->get()->pluck('deal_id');
        $limited_deals = Deal::where('deal_type','Self Care')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->whereNotIn('deals.id',$deals_claimed)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Limited Deal List",$limited_deals);
    }

    public function anytimeDealList(Request $request)
    {
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $anytime_deals = Deal::where('deal_type','Anytime')
            ->where('start_date','<=',$today)
            ->where('expired_date','>=',$today)
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();
        return $this->returnSuccess("Anytime Deal List",$anytime_deals);
    }

    public function searchDealList(Request $request)
    {
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $user =  auth('sanctum')->user();
        
        $monthly_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereMonth('created_at',Carbon::now()->month)
            ->where('deal_type','=','Monthly Specials')->get()->pluck('deal_id');

        $weekly_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereBetween('created_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])
            ->where('deal_type','=','Weekly Wins')->get()->pluck('deal_id');

        $limited_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->where('deal_type','=','Self Care')->get()->pluck('deal_id');

        $daily_deals_claimed = ClaimDeal::where('user_id',$user->id)
            ->whereDate('created_at','=',Carbon::today())
            ->where('deal_type','=','Everyday Exclusives')->get()->pluck('deal_id');

        $deals_claimed = array_merge($daily_deals_claimed->toArray(),$monthly_deals_claimed->toArray(),$weekly_deals_claimed->toArray(),$limited_deals_claimed->toArray());

        if($request->dealCategory && $request->searchKey){
            $searchKey = $request->searchKey;
            $deals = Deal::where('deal_category',$request->dealCategory)
                ->where('description','like','%'.$searchKey.'%')
                ->where('is_active',1)
                ->whereNotIn('deals.id',$deals_claimed)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }elseif($request->searchKey){
            $searchKey = $request->searchKey;
            $deals = Deal::where('description','like','%'.$searchKey.'%')
                ->where('is_active',1)
                ->whereNotIn('deals.id',$deals_claimed)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }elseif ($request->dealCategory){
            $deals = Deal::where('deal_category',$request->dealCategory)
                ->where('is_active',1)
                ->whereNotIn('deals.id',$deals_claimed)
                ->skip($skip)->take($size)->orderByDesc('id')->get();
        }

        return $this->returnSuccess("Exclusinve Deal List",$deals);
    }

    public function getDealById(Request $request) {

        $user = Auth::user();
        $deal = Deal::find($request->id);

        if($deal){
            $dealClick = new DealClick();
            $dealClick->user_id = $user->id;
            $dealClick->deal_id = $deal->id;
            $dealClick->save();
            return $this->returnSuccess("Deal Info",$deal);
        }else{
            return $this->returnError("Deal Not Found",401);
        }
    }

    public function dealClick(Request $request)
    {
        $user = Auth::check() ? Auth::user() : null;
        $deal = Deal::find($request->id);
        //print_r($user);exit;
        if($deal){
            $dealClick = new DealClick();
            $dealClick->user_id = $user ? $user->id : null;
            $dealClick->deal_id = $deal->id;
            $dealClick->save();
            return $this->returnSuccess("Deal Clicked Added",$dealClick);
        }else{
            return $this->returnError("Deal Not Found",401);
        }
    }

    public function notificationClick(Request $request){
        $notification = PushNotification::find($request->id);
        $notification->counts = $notification->counts + 1;
        $notification->save();
        return $this->returnSuccess("Notification Clicked Added",$notification);
    }

    public function claim_deal(Request $request){

        $claim_deal = new ClaimDeal();
        $user =  auth('sanctum')->user();
        $claim_deal->user_id = $user->id;
        $claim_deal->deal_id = $request->deal_id;
        $deal = Deal::find($request->deal_id);
        $claim_deal->deal_title = $deal->deal_title;
        $claim_deal->description = $deal->description;
        $claim_deal->deal_category = $deal->deal_category;
        $claim_deal->deal_type = $deal->deal_type;
        $claim_deal->total_saving = $deal->total_saving;
        $claim_deal->start_date = $deal->start_date;
        $claim_deal->expired_date = $deal->expired_date;
        $saved = $claim_deal->save();
        if($saved){
            return $this->returnSuccess("Deal claime applied successfully",$claim_deal);
        }else{
            return $this->returnError("Deal column failed",401);
        }

    }


    public function homeDeals(Request $request){
        $token = $request->bearerToken();
        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $user = $accessToken->tokenable;
                return $this->homeAuth($request,$user);
            }
        }else{
            return $this->homePublic($request);
        }
    }

    public function homeAuth(Request $request,$user)
    {

        $claimedDealIds = ClaimDeal::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->orderBy('created_at', 'desc')
            ->pluck('deal_id');

        $today = date("Y-m-d");
        $university = $request->university;
        $limitedTimeDeals = Deal::where('deal_type','Limited Time')
            ->where('expired_date', '>', $today)
            ->where('is_active', 1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();
        $limitedTimeDeals = $this->limitedTimeDealsProces($limitedTimeDeals);

        $limitedTimeDeals = $this->excludeDeals($user,$limitedTimeDeals);

        $newDeals = Deal::whereDate('created_at', '>=', Carbon::now()->subDays(7)->toDateString()) // Get deals created in the last 7 days
        ->where('expired_date', '>', $today)
            ->where('is_active', 1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->orderBy('created_at', 'desc') // Order by latest created_at
            ->get(); // Retrieve the results
        //print_r($newDeals);exit;
        $newDeals = $this->excludeDeals($user,$newDeals);

        $top10DealsIds = DB::table('claim_deals as a')
            ->leftJoin('deals as b', 'a.deal_id', '=', 'b.id')
            ->where('b.is_active', 1) // Only include active deals
            ->where('b.expired_date', '>', $today)
            ->when($university, function ($query, $university) {
                return $query->where('b.university_name', 'like', '%' . $university . '%');
            })
            ->whereDate('b.created_at', '>=', Carbon::now()->subDays(7)->toDateString())
            ->whereNotIn('b.id', $limitedTimeDeals->pluck('id'))
            ->selectRaw('count(a.id) as claim_count, a.deal_id') // Select the count of claims and deal_id
            ->groupBy('a.deal_id') // Group by deal_id
            ->orderByDesc('claim_count') // Order by the count of claims in descending order
            ->limit(10) // Limit the result to the top 10
            ->pluck('a.deal_id'); // Get only the deal_id values


        $newDealsIds = $newDeals->pluck('id')->toArray(); // Get an array of deal IDs from new deals
        $top10DealsIds = $top10DealsIds->toArray();

        // Get the actual deals using the top 10 deal IDs
        $top10Deals = Deal::whereIn('id', $top10DealsIds) // Get deals where id is in the array
        ->get(); // Retrieve the deals

        $top10Deals = $this->excludeDeals($user,$top10Deals);

        // Query for picked deals excluding the ones in $newDeals and $top10Deals
        $pickedForYouDeals = Deal::where('is_active', 1) // Filter active deals
        ->where('expired_date', '>', $today)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $newDealsIds) // Exclude the deals from $newDeals
            ->whereNotIn('id', $top10DealsIds) // Exclude the deals from $top10Deals
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->inRandomOrder() // Shuffle deals randomly
            ->limit(12) // Get only 12 deals
            ->get();

        $pickedForYouDeals = $this->excludeDeals($user,$pickedForYouDeals);

        $thingsToDoDeals = Deal::where('tag_name', 'like', '%Things to Do%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->get();

        $thingsToDoDeals = $this->excludeDeals($user,$thingsToDoDeals);

        $bogoDeals = Deal::where('tag_name', 'like', '%BOGO Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->get();

        $bogoDeals = $this->excludeDeals($user,$bogoDeals);

        $drinksDeals = Deal::where('tag_name', 'like', '%All Drinks Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->get();

        $drinksDeals = $this->excludeDeals($user,$drinksDeals);

        $foodDeals = Deal::where('tag_name', 'like', '%All Food Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $limitedTimeDeals->pluck('id'))
            ->get();

        $foodDeals = $this->excludeDeals($user,$foodDeals);

        $deals = [
            'limited_time' =>$limitedTimeDeals,
            'new_deals' => $newDeals,
            'picked_Deals' => $pickedForYouDeals,
            'top10_Deals' => $top10Deals,
            'things_Deals' => $thingsToDoDeals,
            'bogo_deals' => $bogoDeals,
            'drink_deals' => $drinksDeals,
            'food_deals' => $foodDeals,
        ];
        return $this->returnSuccess("Deal claime applied successfully",$deals);
    }

    public function homePublic(Request $request)
    {
        $today = date("Y-m-d");
        $university = $request->university;
        $limitedTimeDeals = Deal::where('deal_type','Limited Time')
            ->where('expired_date', '>', $today)
            ->where('is_active', 1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();

        $newDeals = Deal::whereDate('created_at', '>=', Carbon::now()->subDays(7)->toDateString()) // Get deals created in the last 7 days
        ->where('expired_date', '>', $today)
            ->where('is_active', 1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->orderBy('created_at', 'desc') // Order by latest created_at
            ->get(); // Retrieve the results


        $top10DealsIds = DB::table('claim_deals as a')
            ->leftJoin('deals as b', 'a.deal_id', '=', 'b.id')
            ->where('b.is_active', 1) // Only include active deals
            ->where('b.expired_date', '>', $today)
            ->when($university, function ($query, $university) {
                return $query->where('b.university_name', 'like', '%' . $university . '%');
            })
            ->whereDate('b.created_at', '>=', Carbon::now()->subDays(7)->toDateString())
            ->selectRaw('count(a.id) as claim_count, a.deal_id') // Select the count of claims and deal_id
            ->groupBy('a.deal_id') // Group by deal_id
            ->orderByDesc('claim_count') // Order by the count of claims in descending order
            ->limit(10) // Limit the result to the top 10
            ->pluck('a.deal_id'); // Get only the deal_id values


        $newDealsIds = $newDeals->pluck('id')->toArray(); // Get an array of deal IDs from new deals
        $top10DealsIds = $top10DealsIds->toArray();

        // Get the actual deals using the top 10 deal IDs
        $top10Deals = Deal::whereIn('id', $top10DealsIds) // Get deals where id is in the array
        ->get(); // Retrieve the deals

        // Query for picked deals excluding the ones in $newDeals and $top10Deals
        $pickedForYouDeals = Deal::where('is_active', 1) // Filter active deals
        ->where('expired_date', '>', $today)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->whereNotIn('id', $newDealsIds) // Exclude the deals from $newDeals
            ->whereNotIn('id', $top10DealsIds) // Exclude the deals from $top10Deals
            ->inRandomOrder() // Shuffle deals randomly
            ->limit(12) // Get only 12 deals
            ->get();


        $thingsToDoDeals = Deal::where('tag_name', 'like', '%Things to Do%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();
        $bogoDeals = Deal::where('tag_name', 'like', '%BOGO Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();
        $drinksDeals = Deal::where('tag_name', 'like', '%All Drinks Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();
        $foodDeals = Deal::where('tag_name', 'like', '%All Food Deals%')
            ->where('is_active',1)
            ->when($university, function ($query, $university) {
                return $query->where('university_name', 'like', '%' . $university . '%');
            })
            ->get();
        $deals = [
            'limited_time' =>$limitedTimeDeals,
            'new_deals' => $newDeals,
            'picked_Deals' => $pickedForYouDeals,
            'top10_Deals' => $top10Deals,
            'things_Deals' => $thingsToDoDeals,
            'bogo_deals' => $bogoDeals,
            'drink_deals' => $drinksDeals,
            'food_deals' => $foodDeals,
        ];
        return $this->returnSuccess("Deal claime applied successfully",$deals);
    }

    public function excludeDeals($user,$deals)
    {
        $claimedTodayIds = ClaimDeal::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today()) // Filters records created today
            ->orderBy('created_at', 'desc')
            ->pluck('deal_id')
            ->toArray();
        //print_r($claimedTodayIds);exit;
        $claimedThisWeek = ClaimDeal::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->startOfWeek()) // Filters records from the start of the week
            ->orderBy('created_at', 'desc')
            ->pluck('deal_id');
        //print_r($claimedThisWeek);exit;
        $claimedThisMonth = ClaimDeal::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->startOfMonth()) // Filters records from the start of the month
            ->orderBy('created_at', 'desc')
            ->pluck('deal_id')
            ->toArray();

        //print_r($claimedThisMonth);exit;
        $claimedLimitedIds = ClaimDeal::where('user_id', $user->id)
            ->where('deal_type', '=', 'Limited Time') // Filters records from the start of the month
            ->orderBy('created_at', 'desc')
            ->pluck('deal_id')
            ->toArray();

        //print_r($claimedLimitedIds);exit;
        //print_r($deals);
        $deals = collect($deals)->reject(function ($deal) use ($claimedTodayIds) {
            return $deal->deal_type == "Everyday Exclusives" && in_array($deal->id, (array) $claimedTodayIds);
        })->values();
        //print_r($deals);exit;
        $deals = collect($deals)->reject(function ($deal) use ($claimedThisMonth) {
            return $deal->deal_type == "Monthly Specials" && in_array($deal->id, (array) $claimedThisMonth);
        })->values();

        $deals = collect($deals)->reject(function ($deal) use ($claimedThisWeek) {
            return $deal->deal_type == "Weekly Wins" && in_array($deal->id, (array) $claimedThisWeek);
        })->values();

        $deals = collect($deals)->reject(function ($deal) use ($claimedLimitedIds) {
            return $deal->deal_type == "Limited Time" && in_array($deal->id, (array) $claimedLimitedIds);
        })->values();
        //print_r($deals);exit;
        return $deals;
    }

    public function limitedTimeDealsProces($deals){
        $now = Carbon::now();
        foreach ($deals as $deal){
            $expiredDate = Carbon::parse($deal->expired_date) // Parse the expired date
            ->startOfDay();
            $diff = $expiredDate->diff($now);
            $days = $diff->d;    // Difference in days
            $hours = $diff->h;   // Difference in hours
            $minutes = $diff->i; // Difference in minutes
            $expired_day = "";
            if($days>0){
                $expired_day = $days." day";
            }
            if($hours>0){
                $expired_day = $expired_day." ".$hours." hours";
            }
            if($minutes>0){
                $expired_day = $expired_day." ".$minutes." minutes";
            }
            $deal->expired_count = $expired_day;
        }
        return $deals;
    }

    public function dealsByTagName(Request $request){

        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $dealsByTagName = Deal::where('tag_name', 'like', '%' . $request->tag_name . '%')
            ->where('is_active',1)
            ->skip($skip)->take($size)->orderByDesc('id')->get();

        return $this->returnSuccess("Deals By Tag Name",$dealsByTagName);

    }

    public function searchDealsByTag(Request $request){
        $today = date("Y-m-d");
        $size = $request->size?$request->size:10;
        $page = $request->page?$request->page:1;
        $skip = ($page - 1) * $size;
        $tagName = $request->tag_name;
        $deals = [];
        if($tagName=='Biggest Savings'){
            $deals = Deal::where('tag_name', 'like', '%Biggest Savings%')
                ->where('is_active',1)
                ->where('expired_date', '>', $today)
                ->skip($skip)
                ->take($size)
                ->orderByDesc('total_saving')->get();
        }else if ($tagName=='Sips and Snacks' || $tagName=='Party Picks' || $tagName=='Sweet Treats' || $tagName=='BOGO Deals' || $tagName=='Lunch Deals'){
            $deals = Deal::where( 'tag_name', 'like', '%' . $request->tag_name . '%')
                ->where('is_active',1)
                ->where('expired_date', '>', $today)
                ->skip($skip)
                ->take($size)
                ->get();
        }
        return $this->returnSuccess("Deals List",$deals);

    }

    public function yourDealHistory(Request $request){
        $user = Auth::user();
        $deals = ClaimDeal::with('deal')->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->returnSuccess("Your Deal History",$deals);
    }

    public function updateDealOrder(Request $request)
    {
        $positions = $request->positions;

        // Update the position of each deal
        foreach ($positions as $position) {
            //Deal::where('position', $position['position'])->update(['position' => 99999]);
            Deal::where('id', $position['id'])->update(['position' => $position['position']]);
        }
        //return $this->returnSuccess("Deal Order Updated",$deal);
    }

    /*API ENd*/

    public function returnError($message,$code): \Illuminate\Http\JsonResponse
    {
        $message = [
            "error"=>$message,
            "code"=>$code
        ];
        return response()->json($message);
    }

    public function returnSuccess($message,$data): \Illuminate\Http\JsonResponse
    {
        $message = [
            "message"=>$message,
            "status"=>200,
            "success"=>true,
            "data"=>$data
        ];
        return response()->json($message);
    }
}
