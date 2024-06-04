<?php
declare(strict_types=1);

namespace App\Devices\DTO\Internal;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class GetDeviceDTO
{
    public function __construct(
        private int $limit,
        private int $offset,
    ) {
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
