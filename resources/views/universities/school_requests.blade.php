@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            School Request
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>School Name</th>
                    <th>Request By Email</th>
                    <th>Request By Phone Number</th>
                </tr>
                </thead>
                <tbody>
                @foreach($school_requests as $school_request)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$school_request->university}}</td>
                        <td>{{$school_request->email}}</td>
                        <td>{{$school_request->phone_number}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        new DataTable('#datatablesSimple', {
            layout: {
                bottomEnd: {
                    paging: {
                        firstLast: false
                    }
                }
            },
            searching: false,
            paging: false,
            language: {
                info: ""
            }
        });
    </script>


@endsection
