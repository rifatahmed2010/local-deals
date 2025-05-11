<?php

namespace App\Http\Controllers;
use App\Models\BusinessRequest;
use App\Models\ClaimDeal;
use App\Models\ContactInformation;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        return view ('users.index',['users'=>$users]);
    }

    public function edit($id){
        $user = User::where('id',$id)->get();
        //return view('users.edit',['user'=>$user[0]);
    }

    public function update(Request $request){
        $user = User::find($request->id);
        $user->save();
    }

    public function destroy(Request $request)
    {
        $user = User::where('id',$request->id);
        $claims = ClaimDeal::where('user_id',$request->id)->delete();
        ContactInformation::where('user_id',$request->id)->delete();
        BusinessRequest::where('user_id',$request->id)->delete();
        $user->delete();
        $users = User::all();
        return view ('users.index',['users'=>$users]);
    }
}
