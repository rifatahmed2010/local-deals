<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PushNotification;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
class PushNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function sendHTTP2Push($http2ch, $url, $http2_server, $jwt, $bundleid, $message, $token)
    {
        curl_setopt_array($http2ch, array(
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => "$url/3/device/$token",
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => array(
                "apns-topic: {$bundleid}",
                "authorization: bearer $jwt"
            ),
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => 1
        ));

        $result = curl_exec($http2ch);

        if ($result === FALSE) {
            throw new Exception("Curl failed: ".curl_error($http2ch));
        }

        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
        return $status;

    }

    public function base64($data) {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    public function sendNotification($token,$notification_message,$deal)
    {
        /**
         * @param $http2ch          the curl connection
         * @param $http2_server     the Apple server url
         * @param $apple_cert       the path to the certificate
         * @param $app_bundle_id    the app bundle id
         * @param $message          the payload to send (JSON)
         * @param $token            the token of the device
         * @return mixed            the status code
         */

        $keyfile = 'AuthKey_GG9P5QL4L3.p8';               # <- Your AuthKey file
        $keyid = 'GG9P5QL4L3';                            # <- Your Key ID
        $teamid = 'Q8YCT49UJB';                           # <- Your Team ID (see Developer Portal)
        $bundleid = 'com.bizzy.deals';                # <- Your Bundle ID
        $url = 'https://api.push.apple.com';  # <- development url, or use http://api.development.push.apple.com for development environment
        $deal->message = $notification_message;
        
        //$payloadmsg[0] = $deal->deal_id,
        //$payloadmsg[1] = $deal->message;
        
        // echo $deal->message;
        // exit();
        
        $message = '{"aps":{"alert":{"title" : "'.$deal->message.'"},"sound":"default", "badge":1},"deal_id" : '.$deal->id.'}';
        
        //echo json_encode($message);
        //exit();
        
        //$message = json_encode($message);
        
        //$message = '{"aps":{"alert":"Hi there! Sijan.","sound":"default", "badge":1}}';
        
        
        
        $key = openssl_pkey_get_private('file://'.$keyfile);
        $header = ['alg'=>'ES256','kid'=>$keyid];
        $claims = ['iss'=>$teamid,'iat'=>time()];

        $header_encoded = $this->base64($header);
        $claims_encoded = $this->base64($claims);

        $signature = '';
        openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

        // only needed for PHP prior to 5.5.24
        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }
        //$token = "d1bef4e31e226f3e623c5e55aad745c08a4006f69a2963859e14dfed2d4b43e7";
        $http2ch = curl_init();
        $http2_server = 'https://api.push.apple.com';
        $status = $this->sendHTTP2Push($http2ch, $url, $http2_server, $jwt, $bundleid, $message, $token);

        if($status == 200) {
            echo '{"status": '.$status.',"message":"Push notification send successfully."}';
        } else {
            echo '{"status": '.$status.',"message":"Push notification not send."}';
        }




// close connection
        curl_close($http2ch);
    }

    public function index()
    {
        $notifications = PushNotification::all()->sortByDesc('id');
        return view('push_notifications.index',['notifications'=>$notifications]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('push_notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tokens = User::whereNotNull('push_token')->pluck('push_token')->toArray();

        $deal = Deal::where('id',$request->deal_id)->first();
        $notification = new PushNotification();

        if($request->sending_policy == 'now') {
            $notification->deal_id = $request->deal_id;
            $notification->message = $request->message;
            $notification->is_sent = 1;
            $notification->sending_policy = $request->sending_policy;
            $notification->scheduled_date = $request->scheduled_date;
            $notification->counts= 0;
            $notification->save();
            foreach ($tokens as $token) {
                $this->sendNotification($token,$notification->message,$deal);
            }
        }else{
            $notification->deal_id = $request->deal_id;
            $notification->message = $request->message;
            $notification->is_sent = 0;
            $notification->sending_policy = $request->sending_policy;
            $notification->scheduled_date = $request->scheduled_date;
            $notification->counts= 0;
            $notification->save();
        }

        return redirect()->route('push_notifications.index')->with('success', 'Push Notification Saved Successfully');
    }

    public function sendScheduledNotification()
    {
        $notifications = PushNotification::where('is_sent', 0)
            ->where('sending_policy', 'later')
            ->where('scheduled_date', '<', Carbon::now())
            ->get();
        $tokens = User::whereNotNull('push_token')->pluck('push_token')->toArray();
        foreach ($notifications as $notification) {
            $notification->is_sent = 1;
            $notification->save();
            echo $notification->id;
            $deal = Deal::where('id',$notification->deal_id)->first();
            foreach ($tokens as $token) {
                $this->sendNotification($token,$notification->message,$deal);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(pushNotification $push_notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(pushNotification $push_notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pushNotification $push_notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(pushNotification $push_notification)
    {
        //
    }
}
