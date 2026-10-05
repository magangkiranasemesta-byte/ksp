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
    WorkOrderController,
    MaintenanceRequestController,
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

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

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

        /*
        |--------------------------------------------------------------------------
        | Ticket Resource
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'tickets',
            TicketController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Ticket Evidence
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/tickets/{ticket}/evidence',
            [MaintenanceEvidenceController::class, 'store']
        )->name('tickets.evidence.store');

        Route::delete(
            '/tickets/{ticket}/evidence/{evidence}',
            [MaintenanceEvidenceController::class, 'destroy']
        )->name('tickets.evidence.destroy');


        /*
        |--------------------------------------------------------------------------
        | Ticket Actions
        |--------------------------------------------------------------------------
        */

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
        | Downtime Equipment
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

        Route::get(
            '/downtime/{downtime}',
            [EquipmentDowntimeController::class, 'show']
        )->name('downtime.show');

        Route::get(
            '/downtime/{downtime}/edit',
            [EquipmentDowntimeController::class, 'edit']
        )->name('downtime.edit');

        Route::put(
            '/downtime/{downtime}',
            [EquipmentDowntimeController::class, 'update']
        )->name('downtime.update');

        Route::patch(
            '/downtime/{downtime}/complete',
            [EquipmentDowntimeController::class, 'complete']
        )->name('downtime.complete');

        Route::delete(
            '/downtime/{downtime}',
            [EquipmentDowntimeController::class, 'destroy']
        )->name('downtime.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Maintenance (Request -> Approval -> Work Order) + Preventive
    |--------------------------------------------------------------------------
    |
    | Akses modul dilindungi permission "maintenance". Hak aksi per-record
    | (siapa boleh approve / assign / start / dst.) diperiksa di Policy:
    | MaintenanceRequestPolicy dan WorkOrderPolicy.
    |
    */

    Route::middleware('permission:maintenance')->group(function () {

        /*
        |----------------------------------------------------------------------
        | Work Orders
        |----------------------------------------------------------------------
        | destroy tidak disediakan: Work Order dibatalkan (cancel), bukan dihapus.
        */

        Route::resource('work-orders', WorkOrderController::class)
            ->except(['destroy']);

        Route::prefix('work-orders/{workOrder}')
            ->name('work-orders.')
            ->where(['workOrder' => '[0-9]+'])
            ->group(function () {
                Route::post('/assign', [WorkOrderController::class, 'assign'])->name('assign');
                Route::post('/start', [WorkOrderController::class, 'start'])->name('start');
                Route::post('/hold', [WorkOrderController::class, 'hold'])->name('hold');
                Route::post('/resume', [WorkOrderController::class, 'resume'])->name('resume');
                Route::post('/complete', [WorkOrderController::class, 'complete'])->name('complete');
                Route::post('/cancel', [WorkOrderController::class, 'cancel'])->name('cancel');
            });


        /*
        |----------------------------------------------------------------------
        | Maintenance Request
        |----------------------------------------------------------------------
        */

        Route::prefix('maintenance')
            ->name('maintenance.')
            ->group(function () {

                Route::get('/', [MaintenanceRequestController::class, 'index'])->name('index');
                Route::post('/', [MaintenanceRequestController::class, 'store'])->name('store');

                /*
                | Preventive Maintenance
                | (didaftarkan SEBELUM route {maintenanceRequest} agar tidak tertabrak)
                */
                Route::prefix('preventive')
                    ->name('preventive.')
                    ->group(function () {
                        Route::get('/', [PreventiveMaintenanceController::class, 'index'])->name('index');
                        Route::post('/', [PreventiveMaintenanceController::class, 'store'])->name('store');
                        Route::patch('/{id}/complete', [PreventiveMaintenanceController::class, 'complete'])->name('complete');
                    });

                Route::where(['maintenanceRequest' => '[0-9]+'])->group(function () {
                    Route::get('/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('show');
                    Route::post('/{maintenanceRequest}/approve', [MaintenanceRequestController::class, 'approve'])->name('approve');
                    Route::post('/{maintenanceRequest}/reject', [MaintenanceRequestController::class, 'reject'])->name('reject');
                });
            });
    });


    /*
    |--------------------------------------------------------------------------
    | History & Export
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:history')
        ->prefix('history')
        ->name('history.')
        ->group(function () {

            Route::get(
                '/',
                [MaintenanceController::class, 'history']
            )->name('index');

            Route::get(
                '/export/pdf',
                [MaintenanceController::class, 'exportPdf']
            )->name('export.pdf');

            Route::get(
                '/export/excel',
                [MaintenanceController::class, 'exportExcel']
            )->name('export.excel');
        });


    /*
    |--------------------------------------------------------------------------
    | Spareparts
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

    Route::middleware('permission:spareparts')->group(function () {

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

    Route::middleware('permission:equipment')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Equipment List
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/equipment',
            [EquipmentController::class, 'index']
        )->name('equipment.index');


        /*
        |--------------------------------------------------------------------------
        | Equipment Detail
        |--------------------------------------------------------------------------
        |
        | Ini menjadi tujuan ketika QR Code equipment di-scan.
        |
        */

        Route::get(
            '/equipment/{equipment}',
            [EquipmentController::class, 'show']
        )->name('equipment.show');


        /*
        |--------------------------------------------------------------------------
        | Generate QR Code Equipment
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | /equipment/1/qr
        |
        */

        Route::get(
            '/equipment/{equipment}/qr',
            [EquipmentController::class, 'qr']
        )->name('equipment.qr');


        /*
        |--------------------------------------------------------------------------
        | Tambah Equipment
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/equipment',
            [EquipmentController::class, 'store']
        )->name('equipment.store');


        /*
        |--------------------------------------------------------------------------
        | Hapus Equipment
        |--------------------------------------------------------------------------
        */

        Route::delete(
            '/equipment/{equipment}',
            [EquipmentController::class, 'destroy']
        )->name('equipment.destroy');
    });


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
            'destroy',
        ]);

        Route::put(
            '/users/{user}/permissions',
            [UserController::class, 'updatePermissions']
        )->name('users.permissions');
    });
});