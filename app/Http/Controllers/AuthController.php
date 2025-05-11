<?php

namespace App\Http\Controllers;

use App\Models\ClaimDeal;
use App\Models\Deal;
use App\Models\Profile;
use App\Models\SchoolRequest;
use App\Models\University;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function login(Request $request)
        {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['role'] = 2;
        // Attempt to log in
        if (Auth::attempt($credentials)) {
            // Authentication was successful
            $user = Auth::user();
            // Check the user's role
            return redirect()->route('dashboard');
        } else {
            // Authentication failed; check which credential is incorrect
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            if ($user && !Auth::validate(['email' => $user->email, 'password' => $credentials['password']])) {
                // Password is incorrect
                throw ValidationException::withMessages([
                    'password' => trans('auth.password_incorrect'),
                ])->redirectTo(route('login'));
            } elseif ($user) {
                // Email is incorrect
                throw ValidationException::withMessages([
                    'email' => trans('auth.email_incorrect'),
                ])->redirectTo(route('login'));
            } else {
                // Both email and password are incorrect
                throw ValidationException::withMessages([
                    'failed' => trans('auth.failed'),
                ])->redirectTo(route('login'));
            }
        }
    }
    
    public function logout(Request $request){
        Auth::logout();
        return view('auth.login');
    }

    public function showRegistration(){
        return view('auth.registration');
    }

    /* Email Phone Number Send for getting verification Code */
    public function send_verification_code(Request $request)
    {
        if ($this->checkEmailExist($request->email)){
            return $this->returnError("Email already exists!",401);
        }else{
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            $uVerification = new UserVerification();
            $uVerification->email = $request->email;
            $uVerification->phone_number = $request->phone_number;
            $uVerification->verification_code = $verification_code;
            $expired_at = date('Y-m-d H:i:s', strtotime("+5 min"));
            $uVerification->expired_at = $expired_at;
            $uVerification->save();

            $receiver_number = $uVerification->phone_number;
            $message = "Bizzy Verification Code : ".$verification_code;

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiver_number,[
                'from' => $twilio_number,
                'body' => $message
            ]);
            return $this->returnSuccess("A verification code has been sent to your email.",$uVerification->verification_code);
        }
    }

    public function send_reset_code(Request $request)
    {
        $user = User::where('phone_number', $request->phone_number)->first();
        if (!$user){
            return $this->returnError("No account exists with this phone number",401);
        }else{
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            $uVerification = new UserVerification();
            $uVerification->email = $user->email;
            $uVerification->phone_number = $user->phone_number;
            $uVerification->verification_code = $verification_code;
            $expired_at = date('Y-m-d H:i:s', strtotime("+5 min"));
            $uVerification->expired_at = $expired_at;
            $uVerification->save();

            $receiver_number = $uVerification->phone_number;
            $message = "Bizzy Password Reset Code : ".$verification_code;

            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiver_number,[
                'from' => $twilio_number,
                'body' => $message
            ]);
            return $this->returnSuccess("A password reset code has been sent to your phone number.",$uVerification->verification_code);
        }
    }

    function checkEmailExist($email)
    {
        $user = User::where('email', $email)->first();
        return $user?true:false;
    }

    /* User sends email, phone number to get verification code */

    /*Final Signup*/
    function check_verification_code(Request $request)
    {
        $current_time = date('Y-m-d H:i:s');
        $userVerification = UserVerification::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->where('expired_at','>', $current_time)
            ->get();

        if (count($userVerification)==0){
            return $this->returnError("Validation Failed!",401);
        }else{
            return $this->returnSuccess("Validation Successful!.",1);
        }
    }

    function check_reset_code(Request $request)
    {
        $current_time = date('Y-m-d H:i:s');
        $userVerification = UserVerification::where('phone_number', $request->phone_number)
            ->where('verification_code', $request->verification_code)
            ->where('expired_at','>', $current_time)
            ->get();

        if (count($userVerification)==0){
            return $this->returnError("Validation Failed!",401);
        }else{
            return $this->returnSuccess("Validation Successful!.",1);
        }
    }

    public function signupapi(Request $request) {
        if ($this->checkEmailExist($request->email)){
            return $this->returnError("Email already exist",401);
        }
        if($request->password == $request->confirm_password){
            $user = new User;
            $user->full_name = $request->full_name;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            // university name from email
//            $emailParts = explode('@', $request->email);
//            if (count($emailParts) == 2) {
//                $universityName = explode('.', $emailParts[1])[0];
//                $universityName = strtoupper($universityName);
//            } else {
//                $universityName = null;
//            }

            $emailParts = explode('@', $request->email);
            if (count($emailParts) == 2) {
                $domainParts = explode('.', $emailParts[1]);
                $partsCount = count($domainParts);

                if ($partsCount >= 2) {
                    $universityName = strtoupper($domainParts[$partsCount - 2]);
                } else {
                    $universityName = null; // Handle case with unexpected domain format
                }
            }

            if ($universityName) {
                University::firstOrCreate(['name' => $universityName]);

            }

            if($user && Hash::check($request->password,$user->password)){
                $token = $user->createToken('api');
                $user->token = $token->plainTextToken;
                return $this->returnSuccess("Signup Successful",$user);
            }


        }else{
            return $this->returnError("User signup failed",401);
        }
    }

    public function loginapi(Request $request){
        $user = User::where('email',$request->email)->first();
        if(!$user){
            return $this->returnError("Email or password is invalid",401);
        }
        if($user && Hash::check($request->password,$user->password)){
            $token = $user->createToken('api');
            $user->token = $token->plainTextToken;
            return $this->returnSuccess("User Logged in Successfully",$user);
        }else{
            return $this->returnError("Email or password is invalid",401);
        }
    }

    public function logoutapi(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return $this->returnSuccess("User logged out successfully!",[]);
    }

    public function permanentlyDeleteProfile(Request $request){
        $user = $request->user();
        if($user){
            DB::table('claim_deals')->where('user_id',$user->id)->delete();
            DB::table('users')->where('id',$user->id)->delete();
            DB::table('user_verifications')->where('email',$user->email)->delete();
            DB::table('sessions')->where('user_id',$user->id)->delete();
            return $this->returnSuccess("User deleted successfully!",[]);
        }else{
            return $this->returnError("No user found!",401);
        }
    }

    public function profile(Request $request)
    {
        $auth_user = auth('sanctum')->user();
        $user = User::find($auth_user->id);
        $currentMonth = Carbon::now()->month;
        $deal_claims = DB::table('claim_deals')
            ->selectRaw('sum(total_saving) as amount_saved,count(user_id) deals_used, user_id')
            ->whereMonth('claim_deals.created_at', $currentMonth)
            ->groupBy('user_id')
            ->orderByDesc('amount_saved')
            ->get();

        $user_claims = [];
        for($i=0;$i<count($deal_claims);$i++){
            if($deal_claims[$i]->user_id==$user->id){
                $user_claims = $deal_claims[$i];
                break;
            }
        }

        $rank = $i+1;
        $profile = [
            'deal_used'=> $user_claims?$user_claims->deals_used:0,
            'amount_saved'=> $user_claims?$user_claims->amount_saved:0,
            'rank'=>$rank,
            'profile'=>$user
        ];
        return $profile;
    }

    public function leaderboard(Request $request)
    {
        $size = $request->size ? $request->size : 25;
        $page = $request->page ? $request->page : 1;
        $skip = ($page - 1) * $size;
        $auth_user = auth('sanctum')->user();
        $user = User::find($auth_user->id);

        // Convert current time to EST and get the month
        $currentMonth = Carbon::now()->month;

        $deal_claims = DB::table('claim_deals')
            ->join('users', 'users.id', '=', 'claim_deals.user_id')
            ->whereMonth('claim_deals.created_at', $currentMonth)
            ->selectRaw('SUM(total_saving) as amount_saved, COUNT(user_id) deals_used, full_name, user_id, profile_photo_path')
            ->groupBy('user_id', 'full_name', 'profile_photo_path')
            ->skip($skip)->take($size)
            ->orderByDesc('amount_saved')
            ->get();

        $user_claims = [];
        for($i=0;$i<count($deal_claims);$i++){
            if($deal_claims[$i]->user_id==$user->id){
                $user_claims = $deal_claims[$i];
                break;
            }
        }

        $rank = $i+1;
        $profile = [
            'deal_used'=> $user_claims?$user_claims->deals_used:0,
            'amount_saved'=> $user_claims?$user_claims->amount_saved:0,
            'rank'=>$rank,
            'leaderboard'=>$deal_claims
        ];
        return $profile;
    }

    /*Final Signup*/

    // public function signup(Request $request)
    // {
    //     if($request->password == $request->confirm_password){
    //         $user = new User;
    //         $user->full_name = $request->full_name;
    //         $user->email = $request->email;
    //         $user->phone_number = $request->phone_number;
    //         $user->password = Hash::make($request->password);
    //         $user->save();
    //         return redirect()->route('dashboard')->with('message', 'User created successfully');
    //     }else{
    //         return back()->with("message", "Password doesn't matches");
    //     }
    // }

public function signup(Request $request)
    {
        if($request->password == $request->confirm_password){
            $user = new User;
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);
            $user->role = 2;
            $user->save();
            return redirect()->route('dashboard')->with('message', 'User created successfully');
        }else{
            return back()->with("message", "Password doesn't matches");
        }
    }

    public function profileUpdate(Request $request)
    {
        $auth_user = auth('sanctum')->user();
        $user = User::find($auth_user->id);
        $user->full_name = $request->full_name?$request->full_name:$user->full_name;
        $user->email = $request->email?$user->email:$user->email;
        $user->phone_number = $request->phone_number?$user->phone_number:$user->phone_number;
        if(isset($request->password) && !empty($request->password) && $request->password!="" && $request->password!=null && ($request->confirm_password==$request->password))
        {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('profile_photo_path') && $request->file('profile_photo_path')->isValid()) {
            $user->media()->delete();
            $user->addMediaFromRequest('profile_photo_path')->toMediaCollection('profile_photo_path');
        }

        $user->save();
        if ($request->hasFile('profile_photo_path') && $request->file('profile_photo_path')->isValid()) {
            $url = $user->getFirstMediaUrl('profile_photo_path', 'thumb');
            $user->profile_photo_path = $url;
            $user->save();
        }
        return $this->returnSuccess("Profile Updated successfully!",$user);
    }

    public function pushTokenUpdate(Request $request)
    {
        $auth_user = auth('sanctum')->user();
        $user = User::find($auth_user->id);
        $user->push_token = $request->push_token;
        $user->save();
        return $this->returnSuccess("Token Updated successfully!",$user);
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('phone_number',$request->phone_number) -> first();
        if($user==null){
            return $this->returnError("No account found with this phone number!",401);
        }
        $verification = $this->check_reset_code($request);
        if($verification->original['code']==401){
            return $this->returnError("Verification code is invalid!",401);
        }

        if(isset($request->password) && !empty($request->password) && $request->password!="" && $request->password!=null && ($request->confirm_password==$request->password))
        {
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->returnSuccess("Password Reset Successfully!",$user);

        }else{
            return $this->returnError("Password / Confirm password doesn't match!",401);

        }
    }

    public function universities()
    {
        $universities = University::all();
        return $this->returnSuccess("University List!",$universities);
    }

    public function schoolRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'phone_number' => 'required',
            'university' => 'required|unique:school_requests,university',
        ]);

        if ($validator->fails()) {
            return $this->returnError("School request failed!",401);
        }

        $sr = new SchoolRequest();
        $sr->email = $request->email;
        $sr->phone_number = $request->phone_number;
        $sr->university = $request->university;
        $sr->save();

        return $this->returnSuccess("Request taken successfully!",$sr);
    }

    public function school_requests(){
        $schoolRequests = SchoolRequest::orderBy('id', 'desc')->get();
        return view('universities.school_requests',['school_requests' => $schoolRequests]);
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
            "code"=>200,
            "success"=>true,
            "data"=>$data
        ];
        return response()->json($message);
    }






}
