<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')
            ->latest('created_at');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Module
        |--------------------------------------------------------------------------
        */
        if ($request->filled('module')) {

            $query->where(
                'log_name',
                $request->module
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Event
        |--------------------------------------------------------------------------
        */
        if ($request->filled('event')) {

            $query->where(
                'event',
                $request->event
            );
        }

        $logs = $query
            ->paginate(15)
            ->withQueryString();

        $modules = Activity::query()
            ->whereNotNull('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        $events = Activity::query()
            ->whereNotNull('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        return view('activity_logs.index', compact(
            'logs',
            'modules',
            'events'
        ));
    }
}