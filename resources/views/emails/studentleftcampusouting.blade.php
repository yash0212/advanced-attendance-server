@component('mail::message')
# Your ward has left the campus

Details submitted by your ward
Out Time : {{ $out_time }} <br />
In Time : {{ $in_time }} <br />
Visit To : {{ $visit_to }} <br />
Reason : {{ $reason }} <br />

@endcomponent
