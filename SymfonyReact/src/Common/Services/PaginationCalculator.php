<?php

namespace App\Common\Services;

class PaginationCalculator
{
    public static function calculateOffset(int $limit, int $page): int
    {
        return ($page - 1) * $limit;
    }
}
