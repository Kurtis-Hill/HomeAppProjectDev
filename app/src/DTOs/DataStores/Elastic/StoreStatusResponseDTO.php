<?php

namespace App\DTOs\DataStores\Elastic;

readonly class StoreStatusResponseDTO
{
    public function __construct(
        private string $health,
        private string $status,
        private string $index,
        private int $entryCount,
        private int $deletedEntryCount,
        private int $storeSize,
    ) {
    }

    public function getHealth(): string
    {
        return $this->health;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getEntryCount(): int
    {
        return $this->entryCount;
    }

    public function getDeletedEntryCount(): int
    {
        return $this->deletedEntryCount;
    }

    public function getStoreSize(): int
    {
        return $this->storeSize;
    }
}
