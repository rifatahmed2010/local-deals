<?php

namespace App\Http\Controllers;

use App\Models\ContactInformation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contactInformations = ContactInformation::with('user')->get();
        return view('contact_informations.index', ['contact_informations' => $contactInformations]);
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
    public function show(ContactInformation $contactInformation)
    {
        return view('contact_informations.show', ['ci' => $contactInformation]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactInformation $contactInformation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactInformation $contactInformation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactInformation $contactInformation)
    {
        //
    }

    public function addContactInformation(Request $request){
        $user = Auth::user();
        $contactInformation = new ContactInformation();
        $contactInformation->name = $request->input('name');
        $contactInformation->email = $request->input('email');
        $contactInformation->subject = $request->input('subject');
        $contactInformation->message = $request->input('message');
        $contactInformation->user_id = $user->id;
        $contactInformation->save();
        return $this->returnSuccess("Your Email recorded Successfully",$contactInformation);
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
