<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceEvidence;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaintenanceEvidenceController extends Controller
{
    /**
     * Menambahkan evidence ke ticket.
     */
    public function store(
        Request $request,
        $ticketId
    ) {
        $request->validate([
            'stage' => [
                'required',
                'in:before,process,after',
            ],

            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'stage.required' =>
                'Tahap evidence wajib dipilih.',

            'stage.in' =>
                'Tahap evidence tidak valid.',

            'image.required' =>
                'Foto evidence wajib dipilih.',

            'image.image' =>
                'File harus berupa gambar.',

            'image.mimes' =>
                'Format foto harus JPG, JPEG, PNG, atau WEBP.',

            'image.max' =>
                'Ukuran foto maksimal 5 MB.',
        ]);

        $ticket = MaintenanceTicket::findOrFail(
            $ticketId
        );

        /*
         * Ticket closed/cancelled tidak dapat
         * menerima evidence baru.
         */
        if (in_array($ticket->status, [
            'closed',
            'cancelled',
        ])) {
            return back()->with(
                'error',
                'Evidence tidak dapat ditambahkan karena ticket sudah ditutup.'
            );
        }

        /*
         * Simpan file:
         *
         * storage/app/public/
         * maintenance-evidence/
         * {ticket_id}/
         */
        $path = $request
            ->file('image')
            ->store(
                'maintenance-evidence/' . $ticket->id,
                'public'
            );

        $evidence = MaintenanceEvidence::create([
            'ticket_id' => $ticket->id,
            'stage' => $request->stage,
            'image_path' => $path,
            'description' => $request->description,
            'uploaded_by' => Auth::id(),
        ]);

        /*
         * Activity Log.
         */
        if (function_exists('activity')) {
            activity('maintenance_ticket')
                ->performedOn($ticket)
                ->causedBy(Auth::user())
                ->withProperties([
                    'evidence_id' =>
                        $evidence->id,

                    'stage' =>
                        $evidence->stage,

                    'description' =>
                        $evidence->description,

                    'image_path' =>
                        $evidence->image_path,
                ])
                ->log(
                    'Evidence maintenance berhasil ditambahkan'
                );
        }

        return back()->with(
            'success',
            'Foto evidence berhasil ditambahkan.'
        );
    }

    /**
     * Menghapus evidence.
     */
    public function destroy(
        $ticketId,
        $evidenceId
    ) {
        $ticket = MaintenanceTicket::findOrFail(
            $ticketId
        );

        $evidence = MaintenanceEvidence::where(
            'ticket_id',
            $ticket->id
        )->findOrFail($evidenceId);

        $user = Auth::user();

        /*
         * Role yang memiliki hak hapus.
         */
        $allowedRoles = [
            'SUPERADMIN',
            'ADMIN',
            'SUPERVISOR',
            'MANAGER',
        ];

        $isUploader =
            $evidence->uploaded_by === $user->id;

        $isPrivileged =
            in_array(
                strtoupper((string) $user->role),
                $allowedRoles
            );

        if (!$isUploader && !$isPrivileged) {
            abort(403);
        }

        /*
         * Hapus file dari storage.
         */
        if (
            $evidence->image_path &&
            Storage::disk('public')->exists(
                $evidence->image_path
            )
        ) {
            Storage::disk('public')->delete(
                $evidence->image_path
            );
        }

        /*
         * Activity Log.
         */
        if (function_exists('activity')) {
            activity('maintenance_ticket')
                ->performedOn($ticket)
                ->causedBy($user)
                ->withProperties([
                    'evidence_id' =>
                        $evidence->id,

                    'stage' =>
                        $evidence->stage,
                ])
                ->log(
                    'Evidence maintenance dihapus'
                );
        }

        $evidence->delete();

        return back()->with(
            'success',
            'Evidence berhasil dihapus.'
        );
    }
}