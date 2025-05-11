@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Contact Informations
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th width="280px">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($contact_informations as $ci)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$ci->name}}</td>
                        <td>{{$ci->email}}</td>
                        <td>{{$ci->subject}}</td>
                        <td>{{$ci->message}}</td>
                        <td>
                            <a class="btn btn-sm btn-info" href="{{ route('contact_informations.show',$ci->id) }}"><i class="fa fa-eye"></i></a>
                            <a href="{{route('contact_informations.destroy',['id'=>$ci->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');">Delete</a>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
