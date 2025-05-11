@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Deal Update
        </div>

        <div class="card-body">
            <form action="{{route('deals.update',['deal'=>$deal])}}" enctype="multipart/form-data" method="POST">
                {{csrf_field()}}
                {{ method_field('PUT') }}
                <div class="mb-3">
                    <label class="custom-control-label">Deal Title</label>
                    <input class="form-control" type="text" name="deal_title" value="{{$deal->deal_title}}">
                </div>
                <div class="mb-3">
                    <label class="custom-control-label">Deal Description</label>
                    <input class="form-control" type="text" name="description" value="{{$deal->description}}">
                </div>

                <div class="mb-3">
                    <label class="custom-control-label">Business Name</label>
                    <input type="text" class="form-control" name="business_name" value="{{$deal->business_name}}">
                </div>

                <div class="mb-3">
                    <label class="custom-control-label">Location</label>
                    <input type="text" class="form-control" name="location" value="{{$deal->location}}">
                </div>

                <div class="mb-3">
                    <label class="custom-control-label">Total $ Savings</label>
                    <input type="number" class="form-control" name="total_saving" value="{{$deal->total_saving}}">
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="custom-control-label">Deal Category</label>
                        <select class="form-select" name="deal_category" value="{{$deal->deal_category}}">
                            <option value="Food" <?php if($deal->deal_category=='Food') echo "selected"; ?>>Food</option>
                            <option value="Night Out" <?php if($deal->deal_category=='Night Out') echo "selected"; ?>>Night Out</option>
                            <option value="Things To Do" <?php if($deal->deal_category=='Things To Do') echo "selected"; ?>>Things To Do</option>
                            <option value="Other" <?php if($deal->deal_category=='Other') echo "selected"; ?>>Other</option>
                        </select>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="custom-control-label">Deal Type</label>
                        <select class="form-select" name="deal_type">
                            <option value="Everyday Exclusives" <?php if($deal->deal_type=='Everyday Exclusives') echo "selected"; ?>>Everyday Exclusives</option>
                            <option value="Monthly Specials" <?php if($deal->deal_type=='Monthly Specials') echo "selected"; ?>>Monthly Specials</option>
                            <option value="Weekly Wins" <?php if($deal->deal_type=='Weekly Wins') echo "selected"; ?>>Weekly Wins</option>
                            <option value="Limited Time" <?php if($deal->deal_type=='Limited Time') echo "selected"; ?>>Limited Time</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="custom-control-label">Tag Name</label>
                        <select class="form-select select2" multiple="multiple" name="tag_name[]" id="tag_name">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->name }}"
                                        {{ in_array($tag->name, explode(',', $deal->tag_name)) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-6">
                        <label class="custom-control-label">University / College</label>
                        <select class="form-select select2" multiple="multiple" name="university_name[]" id="university_name">
                            @foreach($universities as $university)
                                <option value="{{ $university->name}}"
                                        {{ in_array( $university->name, explode(',', $deal->university_name)) ? 'selected' : '' }}>
                                    {{ $university->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label class="custom-control-label">Start Date</label>
                        <input type="date"  value="{{$deal->start_date}}" class="form-control" name="start_date">
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="custom-control-label">Expired Date</label>
                        <input type="date" value="{{$deal->expired_date}}" class="form-control" name="expired_date">
                    </div>
                </div>
                <img id="newImg" src="{{ $deal->deal_image_path }}" style="width: 70px; height: 50px;"/>
                <div class="mb-3">
                    <label class="custom-control-label">Deal Image</label>
                    <input oninput = "newImg.src = window.URL.createObjectURL(this.files[0])" type="file" class="form-control" name="deal_image_path">
                </div>
                <button class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <!-- jQuery & Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#tag_name').select2({
                placeholder: "Enter tags",
                allowClear: true
            });

            $('#university_name').select2({
                placeholder: "Enter tags",
                allowClear: true
            });
        });
    </script>
@endsection
