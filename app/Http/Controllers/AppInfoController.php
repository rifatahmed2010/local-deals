<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppInfo;

class AppInfoController extends Controller
{
    public function insertInfo(Request $request)
    {
        $request->validate([
            'ios_message' => 'string',
            'android_message' => 'string',
            'ios_version' => 'string',
            'ios_build' => 'string',
            'android_version' => 'string',
            'android_build' => 'string',
        ]);

        $appInfo = AppInfo::latest()->first();

        $appInfo = \App\Models\AppInfo::create([
            'ios_message' => $request->ios_message ?? $appInfo->ios_message,
            'android_message' => $request->android_message ?? $appInfo->android_message,
            'ios_version' => $request->ios_version ?? $appInfo->ios_version,
            'ios_build' => $request->ios_build ?? $appInfo->ios_build,
            'android_version' => $request->android_version ?? $appInfo->android_version,
            'android_build' => $request->android_build ?? $appInfo->android_build,
        ]);
    
        return response()->json([
            'message' => 'App info inserted successfully.',
            'data' => $appInfo
        ]);
    }

    public function getInfo(Request $request)
    {

        $appInfo = AppInfo::latest()->first();

        if (!$appInfo) {
            return $this->returnError("Deal Not Found",401);
        }

        return $this->returnSuccess("Your Deal History",$appInfo);
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
