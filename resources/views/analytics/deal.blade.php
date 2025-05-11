@extends('layouts.app')
@section('content')
    <style>
        .dataTables_filter {
            text-align: right !important; /* Right-align the search field */
        }
    </style>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Deal Analytics
            <form method="get" action="">
                <div class="row">
                    <div class="col-md-4">
                        <input type="date" name="start_date" class="form-control sm"
                               value="{{ request()->has('start_date') ? request('start_date') : '' }}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="end_date" class="form-control sm"
                               value="{{ request()->has('end_date') ? request('end_date') : '' }}">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary" onclick="fetchChartData()">Search</button>
                    </div>

                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatablesSimples">
                <thead>
                <tr>
                    <th>SL</th>
                    <th>Deal Description</th>
                    <th>
                        University <br>
                        <input type="text" id="searchUniversity" class="form-control form-control-sm" placeholder="Search">
                    </th>
                    <th>
                        Business <br>
                        <input type="text" id="searchBusiness" class="form-control form-control-sm" placeholder="Search">
                    </th>
                    <th>Total Redemption</th>
                    <th>Engagement Trends</th>
                    <th>Total Click</th>
                    <th>Start Date</th>
                    <th>Expired Date</th>
                    <th>Deal Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($deal_analytics as $deal_analytic)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$deal_analytic->description}}</td>
                        <td>{{$deal_analytic->university_name}}</td>
                        <td>{{$deal_analytic->business_name}}</td>
                        <td>{{$deal_analytic->deal_used}}</td>
                        <td>
                            @if($deal_analytic->trends>0)
                                <span class="text-success">{{$deal_analytic->trends}} % Up</span>
                            @elseif($deal_analytic->trends<0)
                                <span class="text-danger">{{$deal_analytic->trends}} % Down</span>
                            @else
                                <span class="text-secondary">No Change</span>
                            @endif
                        </td>
                        <td>{{$deal_analytic?->total_click ?? 0}}</td>
                        <td>{{$deal_analytic->start_date}}</td>
                        <td>{{$deal_analytic->expired_date}}</td>
                        <td>{{$deal_analytic->is_active==0 ? "Deleted" : "Active"}}</td>
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
        $(document).ready(function () {
            var table = $('#datatablesSimples').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": false,
                "lengthChange": true,
                "pageLength": 10,
                "dom": '<"row mb-2"<"col-md-6"l><"col-md-6 text-end"B>>frtip', // Fix layout
                "buttons": [
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: { columns: ':visible' }
                    }
                ]
            });

            // Custom search for University
            $('#searchUniversity').on('keyup', function () {
                table.column(2).search(this.value).draw();
            });

            // Custom search for Business
            $('#searchBusiness').on('keyup', function () {
                table.column(3).search(this.value).draw();
            });
        });


    </script>
@endsection
