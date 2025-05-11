@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            User Anylytic
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="datatablesSimples">
                <thead>
                <th>SL</th>
                <th>User Name</th>
                <th>Total Used</th>
                <th>Total Clicks</th>
                <th>Total Savings</th>
                <th>Last Login</th>
{{--                <th>Last Session</th>--}}
                </thead>
                <tbody>
                @foreach($deal_analytics as $deal_analytic)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$deal_analytic->full_name}}</td>
                        <td>{{$deal_analytic->deal_used}}</td>
                        <td>{{$deal_analytic->total_clicks}}</td>
                        <td>{{$deal_analytic->total_saving}}</td>
                        <td>{{$deal_analytic->updated_at}}</td>
{{--                        <td>{{$deal_analytic->last_login}}</td>--}}
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
                        exportOptions: { columns: ':visible' },
                        title: 'User Analytics Report',
                        filename: 'User_Analytics_Report' // Custom file name
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: { columns: ':visible' },
                        title: 'User Analytics Report',
                        filename: 'User_Analytics_Report' // Custom file name
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: { columns: ':visible' },
                        title: 'User Analytics Report',
                        filename: 'User_Analytics_Report' // Custom file name
                    }
                ]
            });
        });


    </script>


@endsection
