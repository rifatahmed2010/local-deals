@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">

            <h3><i class="fas fa-table me-1"></i>Deal Create</h3>
        </div>

        <div class="card-body">
            <form action="{{route('deals.store')}}" enctype="multipart/form-data" method="POST">

                {{csrf_field()}}
                {{ method_field('POST') }}

                <div class="mb-3">
                    <label class="custom-control-label">Deal Title</label>
                    <input class="form-control" type="text" name="deal_title">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="custom-control-label">Deal Description</label>
                    <input class="form-control" type="text" name="description"/>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="custom-control-label">Business Name</label>
                    <input type="text" class="form-control" name="business_name">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="custom-control-label">Location</label>
                    <input type="text" class="form-control" name="location">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="custom-control-label">Total $ Savings</label>
                    <input type="number" class="form-control" name="total_saving">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">Deal Category</label>
                        <select class="form-select" name="deal_category">
                            <option value="" selected disabled> Enter deal category</option>
                            <option value="Food">Food</option>
                            <option value="Night Out">Night Out</option>
                            <option value="Things To Do">Things To Do</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">Deal Type</label>
                        <select class="form-select" name="deal_type">
                            <option value="" selected disabled > Enter deal type</option>
                            <option value="Everyday Exclusives">Everyday Exclusives</option>
                            <option value="Monthly Specials">Monthly Specials</option>
                            <option value="Weekly Wins">Weekly Wins</option>
                            <option value="Limited Time">Limited Time</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">Tag Name</label>
                        <select class="form-select select2" multiple="multiple" name="tag_name[]" id="tag_name">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">University / College</label>
                        <select class="form-select select2" name="university_name[]" id="university_name" multiple="multiple">
                            @foreach($universities as $university)
                                <option value="{{ $university->name }}">{{ $university->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="custom-control-label">Expired Date</label>
                        <input type="date" class="form-control" name="expired_date">
                    </div>
                </div>
                <img id="newImg" src="{{asset('assets/img/default.jpg')}}" style="width: 70px;height: 50px;"/>
                <div class="mb-3">
                    <label class="custom-control-label">Deal Image</label>
                    <input oninput = "newImg.src = window.URL.createObjectURL(this.files[0])"  type="file" class="form-control" name="deal_image_path">
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
                placeholder: "Enter university or college",
                allowClear: true
            });


        });
    </script>
@endsection
