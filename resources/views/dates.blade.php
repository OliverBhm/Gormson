*{{$header}}*
@foreach($dates as $date)
{{$date['employee']}} until: *{{$date['absence_end']}}* ({{$date['days']}} {{ $date['days'] == '1,0' ? 'day' : 'days' }})
@if(isset($date['substitutes']))
{{'Please refer to: '}}{{$date['substitutes']}}
@endif
@endforeach
