@extends('layouts.app')

@section('title')
    Users
@endsection

@section('bread_controller')
    <a href="index.html">Users</a>
@endsection

@section('bread_action')
    index
@endsection

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Users
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="datatablesSimple">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Profile Picture</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $key => $user) {?>

                    <tr>
                        <td><?php echo $user->id; ?></td>
                        <td><?php echo $user->full_name; ?></td>
                        <td><?php echo $user->email; ?></td>
                        <td><?php echo $user->phone_number; ?></td>
                        <td>
                            <img src="{{$user->profile_photo_path}}" style="height: 65px;width: 100px">
                        </td>
                        <td>
{{--                            <a class="btn btn-sm btn-info" href="{{ route('user.show',$user->id) }}"><i class="fa fa-eye"></i></a>--}}
{{--                            <a class="btn btn-sm btn-primary" href="{{route('user.edit',['id'=>$user->id])}}"><i class="fa fa-pencil"></i></a>--}}
                            <a href="{{route('user.destroy',['id'=>$user->id])}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure to delete?');">Delete</a>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
@endsection
