<?php
declare(strict_types=1);

namespace App\DTOs;

use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

class RequestDTO
{
    #[
        Assert\Type(
            type: ['string', "null"],
            message: RequestQueryParameterHandler::RESPONSE_TYPE . ' must be an {{ type }} you have provided {{ value }}'
        ),
        Assert\Choice(
            choices: [
                RequestTypeEnum::FULL->value,
                RequestTypeEnum::ONLY->value,
                RequestTypeEnum::SENSITIVE_FULL->value,
                RequestTypeEnum::SENSITIVE_ONLY->value,
            ],
            message: RequestQueryParameterHandler::RESPONSE_TYPE .' must be one of {{ choices }} you have provided {{ value }}'
        )
    ]
    private ?string $responseType = RequestTypeEnum::ONLY->value;

    #[
        Assert\Type(
            type: ['int', "null"],
            message: 'page must be an {{ type }} you have provided {{ value }}'
        ),
        Assert\Range(
            minMessage: 'page must be greater than {{ limit }}',
            invalidMessage: 'page must be an int|null you have provided {{ value }}',
            min: 0,
        ),
    ]
    private ?int $page = 1;

    #[
        Assert\Type(
            type: ['int', "null"],
            message: 'limit must be an {{ type }} you have provided {{ value }}'
        ),
        Assert\Range(
            notInRangeMessage: 'limit must be greater than {{ min }} but less than {{ max }}',
            invalidMessage: 'limit must be an int|null you have provided {{ value }}',
            min: 1,
            max: 100
        ),
    ]
    private ?int $limit = 10;

    public function getResponseType(): string
    {
        return $this->responseType ?? RequestTypeEnum::FULL->value;
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

    public function getLimit(): int
    {
        return $this->limit ?? 10;
    }

    public function setLimit(mixed $limit): void
    {
        $this->limit = $limit;
    }
}
