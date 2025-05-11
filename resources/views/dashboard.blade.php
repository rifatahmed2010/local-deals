@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('bread_controller')
    <a href="#">Dashboard</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total User</div>
                <div class="card-body bg-blue-700">
                    {{$total_users}}
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total Deals</div>
                <div class="card-body">
                    {{$total_deals}}
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Contact Information</div>
                <div class="card-body">
                    {{$total_messages}}
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Business Requests</div>
                <div class="card-body">
                    {{$total_business_requests}}
                </div>
            </div>
        </div>
    </div>

@endsection
