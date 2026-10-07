@extends('layouts.app')

@section('title', 'Dashboard Superadmin - Maintenance X')
@section('page_title', 'Dashboard Superadmin')

@section('content')
<div class="space-y-6">
    @include('dashboard.widgets.header')
    @include('dashboard.widgets.kpi')
    @include('dashboard.widgets.attention')

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 min-w-0">@include('dashboard.widgets.recent-wo')</div>
        <div class="min-w-0">@include('dashboard.widgets.wo-chart')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="min-w-0">@include('dashboard.widgets.pm-due')</div>
        <div class="min-w-0">@include('dashboard.widgets.downtime')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 min-w-0">@include('dashboard.widgets.activity')</div>
        <div class="space-y-6 min-w-0">
            @include('dashboard.widgets.summary')
            @include('dashboard.widgets.low-stock')
        </div>
    </div>

    @include('dashboard.widgets.empty')
</div>
@endsection
