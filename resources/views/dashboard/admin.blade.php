@extends('layouts.app')

@section('title', 'Dashboard Admin - Maintenance X')
@section('page_title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    @include('dashboard.widgets.header')
    @include('dashboard.widgets.kpi')

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="min-w-0">@include('dashboard.widgets.ready-for-wo')</div>
        <div class="min-w-0">@include('dashboard.widgets.unassigned-wo')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="min-w-0">@include('dashboard.widgets.pm-due')</div>
        <div class="min-w-0">@include('dashboard.widgets.downtime')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 min-w-0">@include('dashboard.widgets.recent-wo')</div>
        <div class="space-y-6 min-w-0">
            @include('dashboard.widgets.low-stock')
            @include('dashboard.widgets.wo-chart')
        </div>
    </div>

    @include('dashboard.widgets.activity')
    @include('dashboard.widgets.empty')
</div>
@endsection
