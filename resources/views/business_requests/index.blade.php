@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Business Requests
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Business Name</th>
                    <th>Category</th>
                    <th>Contact Information</th>
                    <th>Address</th>
                    <th width="280px">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($business_requests as $br)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$br->business_name}}</td>
                        <td>{{$br->category}}</td>
                        <td>{{$br->contact_information}}</td>
                        <td>{{$br->address}}</td>
                        <td>
                            <a class="btn btn-sm btn-info" href="{{ route('business_requests.show',$br->id) }}"><i class="fa fa-eye"></i></a>
                            <a href="{{route('business_requests.destroy',['id'=>$br->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');">Delete</a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
