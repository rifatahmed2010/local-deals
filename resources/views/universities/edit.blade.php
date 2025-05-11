@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">

            <h3><i class="fas fa-table me-1"></i>University Create</h3>
        </div>

        <div class="card-body">
            <form action="{{route('universities.update',['university'=>$university])}}" enctype="multipart/form-data" method="POST">
                {{csrf_field()}}
                {{ method_field('PUT') }}

                <div class="mb-3">
                    <label class="custom-control-label">University Name</label>
                    <input class="form-control" type="text" name="name" value="{{$university->name}}">
                </div>
                {{--                <div class="col-md-12 mb-3">--}}
                {{--                    <label class="custom-control-label">Location</label>--}}
                {{--                    <input class="form-control" type="text" name="location"/>--}}
                {{--                </div>--}}
                <button class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

@endsection