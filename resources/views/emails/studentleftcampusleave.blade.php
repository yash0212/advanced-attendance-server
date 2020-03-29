@component('mail::message')
# Your ward has left the campus

Details submitted by your ward
Out Date : {{ $out_date }} <br />
In Date : {{ $in_date }} <br />
Visit To : {{ $visit_to }} <br />
Reason : {{ $reason }} <br />

@endcomponent
