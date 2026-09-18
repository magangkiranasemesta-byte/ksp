<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\MaintenanceNotification;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user.
     */
    public static function user(
        ?User $user,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        if (!$user) {
            return;
        }

        $user->notify(
            new MaintenanceNotification(
                $title,
                $message,
                $type,
                $url
            )
        );
    }

    /**
     * Kirim notifikasi berdasarkan role.
     */
    public static function roles(
        array $roles,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        $normalizedRoles = array_map(
            'strtoupper',
            $roles
        );

        User::query()
            ->whereRaw(
                'UPPER(role) IN (' .
                implode(
                    ',',
                    array_fill(
                        0,
                        count($normalizedRoles),
                        '?'
                    )
                ) .
                ')',
                $normalizedRoles
            )
            ->get()
            ->each(function (User $user) use (
                $title,
                $message,
                $type,
                $url
            ) {
                self::user(
                    $user,
                    $title,
                    $message,
                    $type,
                    $url
                );
            });
    }
}