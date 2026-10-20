<?php

namespace Tests\Feature;

use App\Exports\MaintenanceHistoryExport;
use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Pengujian fitur Maintenance History: checklist export, pencarian, per halaman.
 */
class MaintenanceHistoryExportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Equipment $equipment;
    private int $seq = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->makeUser(['history']);
        $this->equipment = $this->makeEquipment('Boiler Utama', 'EQ-BOILER');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /* ------------------------------------------------------------------ */
    /* Helper                                                              */
    /* ------------------------------------------------------------------ */

    private function makeUser(array $permissions, string $role = 'ADMIN'): User
    {
        $this->seq++;

        return User::create([
            'username'    => 'user' . $this->seq,
            'email'       => 'user' . $this->seq . '@example.test',
            'password'    => bcrypt('secret'),
            'role'        => $role,
            'permissions' => $permissions,
        ]);
    }

    private function makeEquipment(string $name, string $code): Equipment
    {
        return Equipment::create([
            'equipment_code' => $code,
            'name'           => $name,
            'location'       => 'Plant A',
        ]);
    }

    private function makeRequest(array $attributes = [], ?string $createdAt = null): MaintenanceRequest
    {
        $request = new MaintenanceRequest(array_merge([
            'equipment_id' => $this->equipment->id,
            'engineer_id'  => $this->user->id,
            'description'  => 'Perawatan rutin ' . (++$this->seq),
            'priority'     => 'MEDIUM',
            'status'       => 'COMPLETED',
        ], $attributes));

        if ($createdAt) {
            $request->created_at = $createdAt;
        }

        $request->save();

        return $request;
    }

    /** Mock DomPDF; mengembalikan daftar ID yang diterima view lewat $captured. */
    private function fakePdf(?array &$captured): void
    {
        $pdf = Mockery::mock();
        $pdf->shouldReceive('setPaper')->once()->with('a4', 'landscape')->andReturnSelf();
        $pdf->shouldReceive('download')->once()->andReturn(
            response('PDF', 200, ['Content-Type' => 'application/pdf'])
        );

        Pdf::shouldReceive('loadView')
            ->once()
            ->withArgs(function ($view, $data) use (&$captured) {
                $captured = $data['history']->pluck('id')->sort()->values()->all();

                return $view === 'reports.maintenance-history';
            })
            ->andReturn($pdf);
    }

    /* ------------------------------------------------------------------ */
    /* Akses                                                               */
    /* ------------------------------------------------------------------ */

    public function test_guest_cannot_export(): void
    {
        $this->post(route('history.export.pdf'), ['selected_ids' => [1]])
            ->assertRedirect(route('login'));
    }

    public function test_user_without_history_permission_is_forbidden(): void
    {
        $other = $this->makeUser(['dashboard'], 'ENGINEER');
        $row = $this->makeRequest();

        $this->actingAs($other)
            ->post(route('history.export.excel'), ['selected_ids' => [$row->id]])
            ->assertForbidden();
    }

    /* ------------------------------------------------------------------ */
    /* Validasi                                                            */
    /* ------------------------------------------------------------------ */

    public function test_export_without_selection_is_rejected_with_message(): void
    {
        foreach (['history.export.pdf', 'history.export.excel'] as $routeName) {
            $this->actingAs($this->user)
                ->post(route($routeName))
                ->assertRedirect(route('history.index'))
                ->assertSessionHas('error');

            $this->assertStringContainsString('Pilih minimal satu data', session('error'));
        }
    }

    public function test_non_integer_id_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['selected_ids' => ['abc']])
            ->assertRedirect(route('history.index'))
            ->assertSessionHas('error');

        $this->assertStringContainsString('tidak valid', session('error'));
    }

    public function test_too_many_ids_are_rejected(): void
    {
        $this->actingAs($this->user)
            ->post(route('history.export.excel'), ['selected_ids' => range(1, 501)])
            ->assertRedirect(route('history.index'))
            ->assertSessionHas('error');

        $this->assertStringContainsString('500', session('error'));
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $row = $this->makeRequest();

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), [
                'selected_ids' => [$row->id],
                'start_date'   => '2026-10-10',
                'end_date'     => '2026-10-01',
            ])
            ->assertSessionHas('error');

        $this->assertStringContainsString('Tanggal akhir', session('error'));
    }

    public function test_unknown_ids_are_rejected_when_nothing_matches(): void
    {
        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['selected_ids' => [999999]])
            ->assertRedirect(route('history.index'))
            ->assertSessionHas('error');

        $this->assertStringContainsString('tidak ditemukan', session('error'));
    }

    public function test_filters_are_kept_when_redirecting_back_with_error(): void
    {
        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['status' => 'COMPLETED', 'search' => 'boiler'])
            ->assertRedirect(route('history.index', ['status' => 'COMPLETED', 'search' => 'boiler']));
    }

    /* ------------------------------------------------------------------ */
    /* Export PDF                                                          */
    /* ------------------------------------------------------------------ */

    public function test_pdf_contains_only_selected_rows(): void
    {
        $a = $this->makeRequest();
        $b = $this->makeRequest();
        $this->makeRequest(); // tidak dipilih

        $this->fakePdf($captured);

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['selected_ids' => [$a->id, $b->id]])
            ->assertOk();

        $this->assertSame([$a->id, $b->id], $captured);
    }

    public function test_duplicate_ids_are_processed_once(): void
    {
        $a = $this->makeRequest();

        $this->fakePdf($captured);

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['selected_ids' => [$a->id, $a->id, (string) $a->id]])
            ->assertOk();

        $this->assertSame([$a->id], $captured);
    }

    public function test_rows_outside_history_statuses_are_never_exported(): void
    {
        $ok = $this->makeRequest(['status' => 'COMPLETED']);
        $pending = $this->makeRequest(['status' => 'PENDING_SUPERVISOR']);

        $this->fakePdf($captured);

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), ['selected_ids' => [$ok->id, $pending->id]])
            ->assertOk();

        $this->assertSame([$ok->id], $captured);
    }

    public function test_pdf_respects_active_status_and_date_filters(): void
    {
        $match = $this->makeRequest(['status' => 'COMPLETED'], '2026-10-05 09:00:00');
        $wrongStatus = $this->makeRequest(['status' => 'REJECTED'], '2026-10-05 09:00:00');
        $tooOld = $this->makeRequest(['status' => 'COMPLETED'], '2026-09-01 09:00:00');

        $this->fakePdf($captured);

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), [
                'selected_ids' => [$match->id, $wrongStatus->id, $tooOld->id],
                'status'       => 'completed',
                'start_date'   => '2026-10-01',
                'end_date'     => '2026-10-31',
            ])
            ->assertOk();

        $this->assertSame([$match->id], $captured);
    }

    /* ------------------------------------------------------------------ */
    /* Export Excel                                                        */
    /* ------------------------------------------------------------------ */

    public function test_excel_download_is_a_valid_workbook_with_only_selected_rows(): void
    {
        Carbon::setTestNow('2026-10-17 10:00:00');

        $a = $this->makeRequest(['description' => 'Ganti oli']);
        $this->makeRequest(['description' => 'Tidak dipilih']);

        $response = $this->actingAs($this->user)
            ->post(route('history.export.excel'), ['selected_ids' => [$a->id]]);

        $response->assertOk();
        $this->assertStringContainsString(
            'maintenance-history-selected-2026-10-17-100000.xlsx',
            (string) $response->headers->get('content-disposition')
        );

        $path = $response->baseResponse->getFile()->getPathname();
        $rows = IOFactory::load($path)->getActiveSheet()->toArray();

        $this->assertSame(
            ['ID', 'Equipment', 'Engineer', 'Priority', 'Status', 'Description', 'Created At'],
            $rows[0]
        );
        $this->assertCount(2, $rows); // heading + 1 baris
        $this->assertEquals($a->id, $rows[1][0]);
        $this->assertSame('Boiler Utama', $rows[1][1]);
        $this->assertSame($this->user->username, $rows[1][2]);
    }

    public function test_export_class_respects_selected_ids_and_filters(): void
    {
        $a = $this->makeRequest(['status' => 'COMPLETED']);
        $b = $this->makeRequest(['status' => 'APPROVED']);
        $c = $this->makeRequest(['status' => 'COMPLETED']);

        $ids = fn (MaintenanceHistoryExport $e) => $e->collection()->pluck('id')->sort()->values()->all();

        // Tanpa pembatasan ID (null) -> semua baris history
        $this->assertSame([$a->id, $b->id, $c->id], $ids(new MaintenanceHistoryExport()));

        // Array kosong -> tidak ada data (bukan "semua")
        $this->assertSame([], $ids(new MaintenanceHistoryExport(null, null, null, [])));

        // ID + status
        $this->assertSame(
            [$a->id],
            $ids(new MaintenanceHistoryExport(null, null, 'COMPLETED', [$a->id, $b->id]))
        );
    }

    /* ------------------------------------------------------------------ */
    /* Pencarian                                                           */
    /* ------------------------------------------------------------------ */

    public function test_search_matches_description_equipment_engineer_and_id(): void
    {
        $other = $this->makeEquipment('Kompresor', 'EQ-KOMP');
        $byDesc = $this->makeRequest(['description' => 'Bocor pada pipa']);
        $byEquip = $this->makeRequest(['equipment_id' => $other->id, 'description' => 'x']);

        $search = fn (string $term) => MaintenanceRequest::forHistory()
            ->filterHistory(null, null, null, $term)
            ->pluck('id')->sort()->values()->all();

        $this->assertSame([$byDesc->id], $search('bocor'));
        $this->assertSame([$byEquip->id], $search('kompresor'));
        $this->assertSame([$byEquip->id], $search('EQ-KOMP'));
        $this->assertContains($byDesc->id, $search($this->user->username));
        $this->assertContains($byDesc->id, $search('#' . $byDesc->id));
    }

    public function test_search_treats_like_wildcards_as_plain_text(): void
    {
        $percent = $this->makeRequest(['description' => 'Diskon 50% dicek']);
        $this->makeRequest(['description' => 'Tanpa simbol']);
        $under = $this->makeRequest(['description' => 'kode_A1']);

        $search = fn (string $term) => MaintenanceRequest::forHistory()
            ->filterHistory(null, null, null, $term)
            ->pluck('id')->sort()->values()->all();

        $this->assertSame([$percent->id], $search('%'));
        $this->assertSame([$under->id], $search('_'));
    }

    public function test_pdf_applies_search_filter(): void
    {
        $match = $this->makeRequest(['description' => 'Ganti bearing']);
        $other = $this->makeRequest(['description' => 'Cek kabel']);

        $this->fakePdf($captured);

        $this->actingAs($this->user)
            ->post(route('history.export.pdf'), [
                'selected_ids' => [$match->id, $other->id],
                'search'       => 'bearing',
            ])
            ->assertOk();

        $this->assertSame([$match->id], $captured);
    }

    /* ------------------------------------------------------------------ */
    /* Halaman list                                                        */
    /* ------------------------------------------------------------------ */

    public function test_history_page_renders_checkboxes_and_limits_per_page(): void
    {
        for ($i = 0; $i < 12; $i++) {
            $this->makeRequest();
        }

        $response = $this->actingAs($this->user)->get(route('history.index'));
        $response->assertOk()
            ->assertSee('data-row-checkbox', false)
            ->assertSee('data-select-all', false)
            ->assertSee('history-select.js', false);
        $this->assertSame(10, $response->viewData('perPage'));
        $this->assertCount(10, $response->viewData('history')->items());

        $response = $this->actingAs($this->user)->get(route('history.index', ['per_page' => 25]));
        $this->assertSame(25, $response->viewData('perPage'));
        $this->assertCount(12, $response->viewData('history')->items());

        foreach (['9999', 'abc', '0'] as $invalid) {
            $response = $this->actingAs($this->user)->get(route('history.index', ['per_page' => $invalid]));
            $this->assertSame(10, $response->viewData('perPage'));
        }
    }

    public function test_history_page_ignores_array_query_parameters(): void
    {
        $this->actingAs($this->user)
            ->get('/history?search[]=x&status[]=y&start_date[]=z')
            ->assertOk();
    }
}
