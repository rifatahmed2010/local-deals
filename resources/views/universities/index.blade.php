@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Universities
            <a href="{{route("universities.create")}}" style="float: right">
                <button class="btn btn-sm btn-primary">Add University</button>
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Name</th>
{{--                    <th>Location</th>--}}
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($universities as $university)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$university->name}}</td>
{{--                        <td>{{$university->location}}</td>--}}
                        <td>
                            <div style="display: flex; gap: 5px;">
{{--                                <a class="btn btn-sm btn-info" href="{{ route('deals.show',$deal->id) }}" title="Detail"><i class="fa fa-eye"></i></a>--}}
                                <a class="btn btn-sm btn-primary" href="{{route('universities.edit',['university'=>$university])}}" title="Edit"><i class="fa fa-pencil"></i></a>
                                <form action="{{ route('universities.destroy', $university->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this university?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
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
