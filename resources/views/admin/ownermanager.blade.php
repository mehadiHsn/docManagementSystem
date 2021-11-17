@extends('admin.layouts.app')

@section('content')
@if(Auth::guard('admin')->check())
<router-view :admin="{{Auth::guard('admin')->user()}}"></router-view>
@else
<router-view admin="0"></router-view>
@endif
@endsection