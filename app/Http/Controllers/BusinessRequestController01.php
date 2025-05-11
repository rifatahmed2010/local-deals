<?php

namespace App\Http\Controllers;

use App\Models\BusinessRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $businessRequests = BusinessRequest::with('user')->get();
        return view('business_requests.index',['business_requests' => $businessRequests]);
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
    public function show(BusinessRequest $businessRequest)
    {
        return view('business_requests.show',['br' => $businessRequest]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessRequest $businessRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessRequest $businessRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $br = BusinessRequest::find($request->id);
        $br->delete();
        return redirect()->route('business_requests.index')->with('success', 'Data Deleted Successfully');

    }

    public function addBusinessRequest(Request $request)
    {
        $user = Auth::user();
        $businessRequest = new BusinessRequest();
        $businessRequest->business_name = $request->input('business_name');
        $businessRequest->category = $request->input('category');
        $businessRequest->contact_information = $request->input('contact_information');
        $businessRequest->address = $request->input('address');
        $businessRequest->user_id = $user->id;
        $businessRequest->save();
        return $this->returnSuccess("Your Business Request recorded Successfully",$businessRequest);
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
