<?php

namespace App\Common\DTO\Request;

use App\Common\Builders\Request\RequestDTOBuilder;
use Symfony\Component\Validator\Constraints as Assert;

class ResponseTypeRequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: 'responseType must be an {{ type }} you have provided {{ value }}'
        ),
        Assert\Choice(
            choices: [
                RequestDTOBuilder::REQUEST_TYPE_FULL,
                RequestDTOBuilder::REQUEST_TYPE_ONLY
            ],
            message: 'responseType must be one of {{ choices }} you have provided {{ value }}'
        )
    ]
    private mixed $responseType;

    public function getResponseType(): string
    {
        return $this->responseType ?? RequestDTOBuilder::REQUEST_TYPE_ONLY;
    }

    public function setResponseType(mixed $responseType): void
    {
        $this->responseType = $responseType;
    }
}
