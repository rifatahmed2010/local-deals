<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\FavouriteDeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteDealController extends Controller
{
    public function addFavourite(Request $request){

        $deal = Deal::find($request->deal_id);
        if(!$deal){
            return $this->returnSuccess("No deal found to add!",$deal);
        }
        $existingFavourite = FavouriteDeal::where('deal_id', $request->deal_id)->where('user_id', Auth::id())->first();
        if($existingFavourite && $existingFavourite->is_active==0){
            $existingFavourite->is_active = 1;
            $existingFavourite->save();
            return $this->returnSuccess('Favourite added successfully',$existingFavourite);
        }else if($existingFavourite && $existingFavourite->is_active==1){
            return $this->returnSuccess('Already added to favourite',$existingFavourite);
        }

        $favourite = new FavouriteDeal();
        $favourite->user_id = Auth::user()->id;
        $favourite->deal_id = $request->deal_id;
        $favourite->is_active = 1;
        $favourite->save();
        if($favourite){
            return $this->returnSuccess('Favourite added successfully',$favourite);
        }else{
            return $this->returnError('Something went wrong');
        }
    }

    public function favouriteListByUser(Request $request){
        $deals = Auth::user()
            ->favouriteDeals()
            ->where('is_active', 1)
            ->with('deal')
            ->get();
        return $this->returnSuccess('Favourite List',$deals);
    }
    public function removeFavourite(Request $request){
        $deal_id = $request->input('deal_id');
        $favourite = FavouriteDeal::where('deal_id',$deal_id)->where('user_id',Auth::user()->id)->first();
        if($favourite){
            $favourite->is_active = 0;
            $favourite->save();
            return $this->returnSuccess("Favourite removed",$favourite);
        }else{
            return $this->returnSuccess("No deal found to remove!",$favourite);
        }

    }

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
