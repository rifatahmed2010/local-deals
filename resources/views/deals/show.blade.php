@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Deal Detail
        </div>

        <div class="card-body">
            <img src="{{$deal->getFirstMediaUrl('deal_image_path', 'thumb')}}" style="width: 250px;height: 250px; display: block;margin-left: auto;margin-right: auto;">
            <table class="table table-sm table-responsive table-bordered table-striped table-hover">
                <tr>
                    <th>Deal Title</th>
                    <td>{{$deal->deal_title}}</td>
                    <th>Deal Description</th>
                    <td>{{$deal->description}}</td>
                </tr>
                <tr>
                    <th>Deal Category</th>
                    <td>{{$deal->deal_category}}</td>
                    <th>Deal Type</th>
                    <td>{{$deal->deal_type}}</td>
                </tr>
                <tr>
                    <th>Business Name</th>
                    <td>{{$deal->business_name}}</td>
                    <th>Location</th>
                    <td>{{$deal->location}}</td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{$deal->start_date}}</td>
                    <th>Expired Date</th>
                    <td>{{$deal->expired_date}}</td>
                </tr>
                <tr>
                    <th>Total Savings</th>
                    <td>{{$deal->total_saving}}</td>
                    <th>Tag Name</th>
                    <td>{{ $deal->tag_name }}</td>
                </tr>

            </table>
        </div>
    </div>
@endsection
