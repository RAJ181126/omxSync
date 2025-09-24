<x-app-layout>
    <div class="container">
        <nav class="" style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendance</li>
            </ol>
        </nav>

        <div class="py-12 mb-5">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    @php
                    $hasAttendance = !empty($attendance);
                    $isCheckedOut = $hasAttendance && !empty($attendance['check_out']);
                    // default note when attendance exists
                    $note = $hasAttendance ? 'Please ensure you have logged 8 hours of attendance today.' : 'Last check-in time is strictly 10 AM. Please make sure to arrive before this time to avoid any inconvenience.';

                    // button defaults
                    if ($hasAttendance) {
                    $btnLabel = $isCheckedOut ? 'Already Checked Out' : 'Check Out';
                    $btnHref = $isCheckedOut ? null : route('attendance.check_out');
                    $btnClass = $isCheckedOut ? 'btn btn-secondary' : 'btn btn-primary';
                    $btnAttrs = $isCheckedOut ? 'aria-disabled="true" disabled' : '';
                    } else {
                    $btnLabel = 'Check In';
                    $btnHref = route('attendance.check_in');
                    $btnClass = 'btn btn-primary';
                    $btnAttrs = '';
                    }
                    @endphp

                    <div class="d-flex align-items-center justify-content-between p-2 text-gray-900 dark:text-gray-100">
                        <div class="pe-3">
                            <p class="mb-0"><b>Note:</b> {{ $note }}</p>
                        </div>

                        <div class="ps-3">
                            @if($btnHref)
                            <a class="{{ $btnClass }}" href="{{ $btnHref }}" id="check_in" role="button" {!! $btnAttrs !!}>{{ $btnLabel }}</a>
                            @else
                            <button class="{{ $btnClass }}" id="check_in" role="button" {!! $btnAttrs !!}>{{ $btnLabel }}</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Check In Time</th>
                                    <th scope="col">Check Out Time</th>
                                    <th scope="col">On Time</th>
                                    <th scope="col">Mins Late</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user_attendance as $key => $attendance)
                                <tr>
                                    <th scope="row">{{$key + 1}}</th>
                                    <td>{{$attendance->date}}</td>
                                    <td>{{$attendance->check_in->format('h:i:s A')}}</td>
                                    <td>{{$attendance->check_out?->format('h:i:s A') ?? '--'}}</td>
                                    <td>{{$attendance->punctual == True ? 'Yes' : 'No'}}</td>
                                    <td>{{$attendance->punctual == True ? 'On Time' : $attendance->minutes_late . ' Mins'}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No Data found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // checkin
        });
    </script>
    @endpush
</x-app-layout>