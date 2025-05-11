@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Business Request Detail
        </div>

        <div class="card-body">
            <table class="table table-sm table-responsive table-bordered table-striped table-hover">
                <tr>
                    <th>Business Name</th>
                    <td>{{$br->business_name}}</td>
                </tr>
                <tr>
                    <th>Business Category</th>
                    <td>{{$br->category}}</td>
                </tr>
                <tr>
                    <th>Contact Information</th>
                    <td>{{$br->contact_information}}</td>

                </tr>

                <tr>
                    <th>Address</th>
                    <td>{{$br->address}}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection
