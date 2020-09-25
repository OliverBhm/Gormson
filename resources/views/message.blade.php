*{{$header}}*
@foreach($dates as $date)
{{$date['employee']['first_name']}} {{$date['employee']['last_name']}}@if($date['absence_type'] == 'Half a day') for {{$date['absence_type']}}@endif from: *{{$date['absence_begin']}}* until: *{{$date['absence_end']}}*
@if(count($date['employee']['substitutes']) >= 1)
{{'Please refer to: '}}@foreach($date['employee']['substitutes'] as $substitute) {{$substitute['first_name']}} {{$substitute['last_name']}}@if(isset(next($date['employee']['substitutes'])['first_name'])),@endif
@endforeach

@endif
@endforeach

