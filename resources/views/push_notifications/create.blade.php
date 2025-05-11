@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Notification Create
        </div>

        <div class="card-body">
            <form action="{{ route('push_notifications.store') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="mb-3">
                    <label class="custom-control-label">Message</label>
                    <input type="text" class="form-control" name="message" required>
                </div>

                <div class="mb-3">
                    <label class="custom-control-label">Select Deal</label>
                    <select name="deal_id" class="form-select">
                        @foreach(\App\Models\Deal::where('is_active', true)->get() as $deal)
                            <option value="{{ $deal->id }}">{{ $deal->description }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="custom-control-label">Sending Time</label>
                    <select name="sending_policy" class="form-select" id="sendingTime">
                        <option value="now">Send Now</option>
                        <option value="later">Send Later</option>
                    </select>
                </div>

                <div class="mb-3" id="scheduleTimeDiv" style="display: none;">
                    <label class="custom-control-label">Select Date</label>
                    <input type="datetime-local" class="form-control" name="scheduled_date" id="scheduledDate">
                </div>

                <button class="btn btn-primary">Submit</button>
            </form>


        </div>
    </div>

    <script>
        document.getElementById('sendingTime').addEventListener('change', function () {
            let scheduleDiv = document.getElementById('scheduleTimeDiv');
            if (this.value === 'later') {
                scheduleDiv.style.display = 'block';
            } else {
                scheduleDiv.style.display = 'none';
            }
        });
    </script>


@endsection
