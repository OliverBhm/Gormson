*{{$header}}*
@foreach($dates as $date)
{{$date['employee']['first_name']}} {{$date['employee']['last_name']}}@if($date['absence_type'] == 'Half a day') for {{$date['absence_type']}}@endif until: *{{$date['absence_end']}}*
@if(isset($date['substitute01']['first_name'])){{'Please refer to: '.$date['substitute01']['first_name'] ?? ''}} {{$date['substitute01']['last_name'] ?? ''}}{{$date['substitute02']['last_name'] ?? ''}}@endif

@endforeach

