@component('mail::message')
# Your ward has low attendance in {{ $subject_name }}

## Attendance Details
Total Hours : {{ $total_hours }} <br />
Attended Hours : {{ $present_count }} <br />
Absent Hours : {{ $total_hours - $present_count }} <br />

@endcomponent
