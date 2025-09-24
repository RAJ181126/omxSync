<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING    = 'pending';
    case IN_PROCESS = 'in_process';
    case COMPLETED  = 'completed';
    case DELAYS     = 'delays';

    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Pending',
            self::IN_PROCESS => 'In Process',
            self::COMPLETED  => 'Completed',
            self::DELAYS     => 'Delays',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING    => 'secondary',
            self::IN_PROCESS => 'primary',
            self::COMPLETED  => 'success',
            self::DELAYS     => 'danger',
        };
    }

    public static function all(): array
    {
        return array_map(
            fn($status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
            ],
            array_filter(
                self::cases(),
                fn($status) => $status !== self::DELAYS && $status !== self::PENDING
            )
        );
    }
}
