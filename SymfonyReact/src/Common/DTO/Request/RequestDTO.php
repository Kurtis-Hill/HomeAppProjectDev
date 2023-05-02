<?php

namespace App\Common\DTO\Request;

use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\Services\RequestTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class RequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: 'responseType must be an {{ type }} you have provided {{ value }}'
        ),
        Assert\Choice(
            choices: [
                RequestTypeEnum::FULL->value,
                RequestTypeEnum::ONLY->value,
                RequestTypeEnum::SENSITIVE_FULL->value,
                RequestTypeEnum::SENSITIVE_ONLY->value,
            ],
            message: 'responseType must be one of {{ choices }} you have provided {{ value }}'
        )
    ]
    private mixed $responseType;

    #[
        Assert\Range(
            minMessage: 'page must be greater than {{ min }}',
            invalidMessage: 'page must be an int|null you have provided {{ value }}',
            min: 0,
        ),
    ]
    private mixed $page;

    #[
        Assert\Range(
            minMessage: 'offset must be greater than {{ min }}',
            invalidMessage: 'offset must be an int|null you have provided {{ value }}',
            min: 0,
        ),
    ]
    private mixed $offset;

    public function getResponseType(): string
    {
        return $this->responseType ?? RequestDTOBuilder::REQUEST_TYPE_ONLY;
//        return $this->responseType ?? RequestTypeEnum::ONLY;
    }

    public function setResponseType(mixed $responseType): void
    {
        $this->responseType = $responseType;
    }

    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    public function setPage(mixed $page): void
    {
        $this->page = $page;
    }

    public function getOffset(): int
    {
        return $this->offset ?? 0;
    }

    public function setOffset(mixed $offset): void
    {
        $this->offset = $offset;
    }
}
