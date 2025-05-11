@extends('layouts.app')
@section('content')
    <!-- Include jQuery and jQuery UI -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
                    <th>Deal ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deal Category</th>
                    <th>Deal Type</th>
                    <th>Tag Name</th>
                    <th>University / College</th>
                    <th>Business Name</th>
                    <th>Deal Start</th>
                    <th>Deal Expired</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($deals as $deal)
                    <tr data-id="{{$deal->id}}">
                        <td>{{$deal->id}}</td>
                        <td>{{$deal->deal_title}}</td>
                        <td>{{$deal->description}}</td>
                        <td>{{$deal->deal_category}}</td>
                        <td>{{$deal->deal_type}}</td>
                        <td>{{$deal->tag_name}}</td>
                        <td>{{$deal->university_name}}</td>
                        <td>{{$deal->business_name}}</td>
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

    <script>

        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Simple-DataTables
            const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
                layout: {
                    bottomEnd: {
                        paging: {
                            firstLast: false
                        }
                    }
                },
                searching: false,
                paging: true,
                language: {
                    info: ""
                }
            });


            $("#datatablesSimple tbody").sortable({
                helper: function (e, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                start: function (event, ui) {
                    ui.item.data("startPos", ui.item.index()); // Store initial position
                },
                update: function (event, ui) {
                    let currentPage = dataTable._currentPage;
                    let pageSize = dataTable.options.perPage; // Get page size
                    let offset = (currentPage-1) * pageSize;
                    let positions = [];
                    $("#datatablesSimple tbody tr").each(function (index) {
                        //let dealId = $(this).data("id"); // Assuming each <tr> has data-id attribute
                        let dealId = $(this).map(function () {
                            return $(this).children("td:first").text().trim()
                        }).get();
                        positions.push({
                            id: dealId,
                            position: offset + index + 1 // Position should be 1-based
                        });
                    });

                    $.ajax({
                        url: "/api/update-deal-order",
                        method: "POST",
                        data: { positions: positions }, // Send full sorted list
                        headers: { "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content") },
                        success: function (response) {
                            console.log("Order updated successfully!", response);
                        },
                        error: function (error) {
                            console.error("Error updating order:", error);
                        }
                    });
                }
            }).disableSelection();

            // Make tbody rows draggable
            // $("#datatablesSimple tbody").sortable({
            //     helper: function (e, ui) {
            //         ui.children().each(function () {
            //             $(this).width($(this).width());
            //         });
            //         return ui;
            //     },
            //     start: function (event, ui) {
            //         ui.item.data("startPos", ui.item.index()); // Store initial position
            //     },
            //     update: function (event, ui) {
            //         let sortedIDs = $(this).children("tr").map(function () {
            //             return $(this).children("td:first").text().trim()
            //         }).get();
            //
            //         let draggedRowID = ui.item.data("id"); // Get dragged row ID
            //         let newPosition = ui.item.index(); // Get new position
            //         let oldPosition = ui.item.data("startPos"); // Get old position
            //         let dealId = sortedIDs[newPosition];
            //
            //         $.ajax({
            //             url: "/api/update-deal-order",
            //             method: "POST",
            //             data: { dealId: dealId, position: newPosition },
            //             headers: { "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content") },
            //             success: function (response) {
            //                 console.log("Order updated successfully!", response);
            //             },
            //             error: function (error) {
            //                 console.error("Error updating order:", error);
            //             }
            //         });
            //
            //     }
            // }).disableSelection();
        });
    </script>

@endsection
