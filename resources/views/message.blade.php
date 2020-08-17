{{$first_name }} {{$last_name}} @if($isFromDisplayed)from: *{{$from}}* @endif until: *{{$until}}*
@isset($substitute_01_first_name)
Please refer to: {{$substitute_01_first_name}} {{$substitute_01_last_name}}
@endisset
@isset($substitute_02_first_name)
, {{$substitute_02_first_name}} {{$substitute_02_last_name}}
@endisset
@isset($substitute_03_first_name)
, {{$substitute_03_first_name}} {{$substitute_03_last_name}}
@endisset

