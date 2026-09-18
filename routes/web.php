<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AuthController,
    DashboardController,
    UserController,
    EquipmentController,
    MaintenanceController,
    SparepartController,
    SparepartUsageController,
    TicketController,
    PreventiveMaintenanceController,
    ActivityLogController,
    EquipmentDowntimeController,
    NotificationController,
    MaintenanceEvidenceController,
};

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::redirect('/', '/dashboard');

    Route::get('/select-role', [AuthController::class, 'selectRole'])
        ->name('select-role');

    /*
    |--------------------------------------------------------------------------
    | Activity Logs
    |--------------------------------------------------------------------------
    */

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard')
        ->name('dashboard');



    Route::get(
    '/notifications',
    [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::post(
        '/notifications/{id}/read',
        [NotificationController::class, 'read']
    )->name('notifications.read');

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'markAllRead']
    )->name('notifications.read-all');
    /*
    |--------------------------------------------------------------------------
    | Tickets
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:tickets')->group(function () {

        Route::resource('tickets', TicketController::class);
        Route::post(
    '/tickets/{ticket}/evidence',
    [MaintenanceEvidenceController::class, 'store']
)->name('tickets.evidence.store');

Route::delete(
    '/tickets/{ticket}/evidence/{evidence}',
    [MaintenanceEvidenceController::class, 'destroy']
)->name('tickets.evidence.destroy');
        Route::prefix('tickets/{ticket}')
            ->name('tickets.')
            ->group(function () {

                Route::patch(
                    '/assign',
                    [TicketController::class, 'assignTechnician']
                )->name('assign');

                Route::patch(
                    '/status',
                    [TicketController::class, 'updateStatus']
                )->name('status');

                Route::post(
                    '/logs',
                    [TicketController::class, 'addLog']
                )->name('addLog');

                /*
                |--------------------------------------------------------------------------
                | Spareparts inside Ticket
                |--------------------------------------------------------------------------
                */

                Route::post(
                    '/spareparts',
                    [TicketController::class, 'addSparepart']
                )->name('spareparts.store');

                Route::delete(
                    '/spareparts/{sparepart}',
                    [TicketController::class, 'removeSparepart']
                )->name('spareparts.destroy');
            });

        /*
        |--------------------------------------------------------------------------
        | Equipment Downtime
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/downtime',
            [EquipmentDowntimeController::class, 'index']
        )->name('downtime.index');

        Route::get(
            '/downtime/create',
            [EquipmentDowntimeController::class, 'create']
        )->name('downtime.create');

        Route::post(
            '/downtime',
            [EquipmentDowntimeController::class, 'store']
        )->name('downtime.store');

        Route::patch(
            '/downtime/{downtime}/complete',
            [EquipmentDowntimeController::class, 'complete']
        )->name('downtime.complete');
    });

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:maintenance')
        ->prefix('maintenance')
        ->name('maintenance.')
        ->group(function () {

            Route::get(
                '/',
                [MaintenanceController::class, 'index']
            )->name('index');

            Route::post(
                '/',
                [MaintenanceController::class, 'store']
            )->name('store');

            Route::post(
                '/{maintenance}/status',
                [MaintenanceController::class, 'updateStatus']
            )->name('status');

            /*
            |--------------------------------------------------------------------------
            | Preventive Maintenance
            |--------------------------------------------------------------------------
            */

            Route::prefix('preventive')
                ->name('preventive.')
                ->group(function () {

                    Route::get(
                        '/',
                        [PreventiveMaintenanceController::class, 'index']
                    )->name('index');

                    Route::post(
                        '/',
                        [PreventiveMaintenanceController::class, 'store']
                    )->name('store');

                    Route::patch(
                        '/{id}/complete',
                        [PreventiveMaintenanceController::class, 'complete']
                    )->name('complete');
                });
        });

    /*
    |--------------------------------------------------------------------------
    | History & Export
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:history')
        ->prefix('history')
        ->name('history')
        ->group(function () {

            Route::get(
                '/',
                [MaintenanceController::class, 'history']
            );

            Route::get(
                '/export/pdf',
                [MaintenanceController::class, 'exportPdf']
            )->name('.export.pdf');

            Route::get(
                '/export/excel',
                [MaintenanceController::class, 'exportExcel']
            )->name('.export.excel');
        });

    /*
    |--------------------------------------------------------------------------
    | Spareparts, Equipment, Users
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:spareparts')
        ->resource(
            'spareparts',
            SparepartController::class
        );

    /*
    |--------------------------------------------------------------------------
    | Riwayat Pemakaian Sparepart
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:spareparts')
        ->group(function () {

            Route::get(
                '/sparepart-usages',
                [SparepartUsageController::class, 'index']
            )->name('sparepart-usages.index');

            Route::get(
                '/sparepart-usages/create',
                [SparepartUsageController::class, 'create']
            )->name('sparepart-usages.create');

            Route::post(
                '/sparepart-usages',
                [SparepartUsageController::class, 'store']
            )->name('sparepart-usages.store');
        });

    /*
    |--------------------------------------------------------------------------
    | Equipment
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:equipment')
        ->resource(
            'equipment',
            EquipmentController::class
        )
        ->only([
            'index',
            'store',
            'destroy'
        ]);

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:users')->group(function () {

        Route::resource(
            'users',
            UserController::class
        )->only([
            'index',
            'store',
            'destroy'
        ]);

        Route::put(
            '/users/{user}/permissions',
            [UserController::class, 'updatePermissions']
        )->name('users.permissions');
    });
});