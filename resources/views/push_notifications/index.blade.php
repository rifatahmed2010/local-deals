@extends('layouts.app')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Push Notifications
            <a href="{{route("push_notifications.create")}}" style="float: right">
                <button class="btn btn-sm btn-primary">Add Notification</button>
            </a>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Message</th>
                    <th>Deal</th>
                    <th>Scheduled</th>
                    <th>Click Count</th>
                    <th>Sent Status</th>
                </tr>
                </thead>
                <tbody>
                        @foreach($notifications as $notification)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$notification->message}}</td>
                                <td>{{\App\Models\Deal::where('id',$notification->deal_id)->first()?->description}}</td>
                                <td>{{$notification->scheduled_date}}</td>
                                <td>{{$notification->counts}}</td>
                                <td>
                                    @if($notification->is_sent)
                                        <?php echo "Sent"; ?>
                                    @else
                                        <?php echo "Not Sent"; ?>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
