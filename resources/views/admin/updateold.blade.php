@extends('admin.layouts.app')

@section('content')
@if(Auth::guard('admin')->check())
<router-view :admin="{{Auth::guard('admin')->user()}}"></router-view>
@else
<router-view></router-view>
@endif
@endsection