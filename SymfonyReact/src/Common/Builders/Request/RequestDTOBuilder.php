<?php

namespace App\Common\Builders\Request;

use App\Common\DTO\Request\ResponseTypeRequestDTO;

class RequestDTOBuilder
{
    public const REQUEST_TYPE_FULL = 'full';

    public const REQUEST_TYPE_SENSITIVE = 'password';

    public const REQUEST_TYPE_ONLY = 'only';

    public static function buildRequestTypeDTO(mixed $responseType): ResponseTypeRequestDTO
    {
        $responseTypeRequestDTO = new ResponseTypeRequestDTO();
        $responseTypeRequestDTO->setResponseType($responseType);

        return $responseTypeRequestDTO;
    }
}
