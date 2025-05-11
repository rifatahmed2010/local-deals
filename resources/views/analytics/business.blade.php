@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Business Anylytic
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatablesSimple">
                <thead>
                <th>SL</th>
                <th>Business Name</th>
                <th>Deal Title</th>
                <th>Total Clicked</th>
                <th>Total Claimed</th>

                </thead>
                <tbody>
                @foreach($dealStats as $dealStat)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$dealStat->business_name}}</td>
                        <td>{{$dealStat->deal_title}}</td>
                        <td>{{$dealStat->clicks}}</td>
                        <td>{{$dealStat->claims}}</td>

                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection
