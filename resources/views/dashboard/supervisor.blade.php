@extends('layouts.app')

@section('title', 'Dashboard Supervisor - Maintenance X')
@section('page_title', 'Dashboard Supervisor')

@section('content')
<div class="space-y-6">
    @include('dashboard.widgets.header')
    @include('dashboard.widgets.kpi')

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="min-w-0">@include('dashboard.widgets.approval-queue')</div>
        <div class="min-w-0">@include('dashboard.widgets.workload')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 min-w-0">@include('dashboard.widgets.active-wo')</div>
        <div class="min-w-0">@include('dashboard.widgets.wo-chart')</div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="min-w-0">@include('dashboard.widgets.pm-due')</div>
        <div class="min-w-0">@include('dashboard.widgets.downtime')</div>
    </div>

    @include('dashboard.widgets.activity')
    @include('dashboard.widgets.empty')
</div>
@endsection
