<?php

use App\DTOs\DataStores\Elastic\StoreStatusResponseDTO;

class DataStoreStatusResponseDTOBuilder
{
    public static function buildDataStoreResponseDTO(
        string $health,
        string $status,
        string $index,
        int $entryCount,
        int $deletedEntryCount,
        int $storeSize,
    ): StoreStatusResponseDTO {
        return new StoreStatusResponseDTO(
            health: $health,
            status: $status,
            index: $index,
            entryCount: $entryCount,
            deletedEntryCount: $deletedEntryCount,
            storeSize: $storeSize,
        );
    }
}
