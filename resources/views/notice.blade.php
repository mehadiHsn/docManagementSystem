@extends('layouts.app')
@section('content')
@if(Auth::check())
<router-view :user-info="{{Auth::user()}}"></router-view>
@else
<router-view :user-info=0></router-view>
@endif
@endsection