@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Contact Information Detail
        </div>

        <div class="card-body">
            <table class="table table-sm table-responsive table-bordered table-striped table-hover">
                <tr>
                    <th>Name</th>
                    <td>{{$ci->name}}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{$ci->email}}</td>
                </tr>
                <tr>
                    <th>Subject</th>
                    <td>{{$ci->subject}}</td>

                </tr>

                <tr>
                    <th>Message</th>
                    <td>{{$ci->message}}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection
