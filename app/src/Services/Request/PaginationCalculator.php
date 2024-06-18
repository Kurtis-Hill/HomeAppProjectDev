<?php
declare(strict_types=1);

namespace App\Services\Request;

class PaginationCalculator
{
    public static function calculateOffset(int $limit, int $page): int
    {
        return ($page - 1) * $limit;
    }
}
