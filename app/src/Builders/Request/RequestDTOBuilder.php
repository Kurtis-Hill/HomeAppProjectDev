<?php
declare(strict_types=1);

namespace App\Builders\Request;

use App\DTOs\RequestDTO;

class RequestDTOBuilder
{
    public const REQUEST_TYPE_FULL = 'full';

    public const REQUEST_TYPE_SENSITIVE = 'password';

    public const REQUEST_TYPE_ONLY = 'only';

    public static function buildRequestDTO(
        mixed $responseType,
        mixed $page = null,
        mixed $limit = null
    ): RequestDTO {
        $responseTypeRequestDTO = new RequestDTO();
        $responseTypeRequestDTO->setResponseType($responseType);
        $responseTypeRequestDTO->setPage($page);
        $responseTypeRequestDTO->setLimit($limit);

        return $responseTypeRequestDTO;
    }
}
