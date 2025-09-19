@component('mail::message')
# {{ $payload['event_title'] ?? '' }}

{!! $payload['message'] !!}

Thanks,<br>
{{ $payload['from_name'] ?? config('app.name') }}
@endcomponent
