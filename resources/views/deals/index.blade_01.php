@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Deals
                <a href="{{route("deals.create")}}" style="float: right">
                    <button class="btn btn-sm btn-primary">Add Deal</button>
                </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deal Category</th>
                    <th>Deal Type</th>
                    <th>Tag Name</th>
                    <th>University / College</th>
                    <th>Deal Start</th>
                    <th>Deal Expired</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($deals as $deal)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$deal->deal_title}}</td>
                            <td>{{$deal->description}}</td>
                            <td>{{$deal->deal_category}}</td>
                            <td>{{$deal->deal_type}}</td>
                            <td>{{$deal->tag_name}}</td>
                            <td>{{$deal->university_name}}</td>
                            <td>{{$deal->start_date}}</td>
                            <td>{{$deal->expired_date}}</td>
                            <td><img src="{{$deal->getFirstMediaUrl('deal_image_path', 'thumb')}}" width="120px"></td>

                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a class="btn btn-sm btn-info" href="{{ route('deals.show',$deal->id) }}" title="Detail"><i class="fa fa-eye"></i></a>
                                    <a class="btn btn-sm btn-primary" href="{{route('deals.edit',['deal'=>$deal])}}" title="Edit"><i class="fa fa-pencil"></i></a>
                                    <a href="{{route('deals.destroy',['id'=>$deal->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');" title="Delete"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

    <script>
        new DataTable('#datatablesSimple', {
            layout: {
                topStart: {
                    buttons: ['copy', 'csv', 'excel',
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            orientation: 'landscape', // Change to portrait if needed
                            pageSize: 'A4',
                            exportOptions: {
                                modifier: {
                                    page: 'all' // Export all pages
                                },
                                columns: ':visible' // Export only visible columns
                            }
                        },

                        'print']
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
