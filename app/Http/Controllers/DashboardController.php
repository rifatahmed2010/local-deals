<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BusinessRequest;
use App\Models\ContactInformation;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $total_users = User::where('is_active', true)->count();
        $total_deals = Deal::where('is_active', 1)->count();
        $total_business_requests = BusinessRequest::count();
        $total_messages = ContactInformation::count();
        return view('dashboard', ['total_users' => $total_users, 'total_deals' => $total_deals,'total_business_requests' => $total_business_requests,'total_messages' => $total_messages]);
    }

    public function terms()
    {
        return view('terms');
    }

    public function policy()
    {
        return view('policy');
    }
}
