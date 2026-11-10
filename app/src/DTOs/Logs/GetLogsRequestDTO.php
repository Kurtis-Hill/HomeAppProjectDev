<?php

declare(strict_types=1);

namespace App\DTOs\Logs;

use Symfony\Component\Validator\Constraints as Assert;

class GetLogsRequestDTO
{
    #[Assert\Type(type: ['string', 'null'], message: 'keyword must be a {{ type }}, you provided {{ value }}')]
    private ?string $keyword = null;

    #[Assert\Type(type: ['string', 'null'], message: 'level must be a {{ type }}, you provided {{ value }}')]
    #[Assert\Choice(
        choices: ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'],
        message: 'level must be one of: DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY',
    )]
    private ?string $level = null;

    #[Assert\Type(type: ['string', 'null'], message: 'startDate must be a {{ type }}, you provided {{ value }}')]
    private ?string $startDate = null;

    #[Assert\Type(type: ['string', 'null'], message: 'endDate must be a {{ type }}, you provided {{ value }}')]
    private ?string $endDate = null;

    #[Assert\Type(type: ['integer', 'null'], message: 'limit must be a {{ type }}, you provided {{ value }}')]
    #[Assert\When(
        expression: 'value !== null',
        constraints: [new Assert\Range(min: 1, max: 500, notInRangeMessage: 'limit must be between {{ min }} and {{ max }}')],
    )]
    private ?int $limit = 50;

    #[Assert\Type(type: ['integer', 'null'], message: 'offset must be a {{ type }}, you provided {{ value }}')]
    #[Assert\When(
        expression: 'value !== null',
        constraints: [new Assert\GreaterThanOrEqual(value: 0, message: 'offset must be 0 or greater')],
    )]
    private ?int $offset = 0;

    public function __construct(
        ?string $keyword = null,
        ?string $level = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = 50,
        ?int $offset = 0,
    ) {
        $this->keyword = $keyword;
        $this->level = $level;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->limit = $limit ?? 50;
        $this->offset = $offset ?? 0;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getLimit(): int
    {
        return max(1, min(500, $this->limit ?? 50));
    }

    public function getOffset(): int
    {
        return max(0, $this->offset ?? 0);
    }
}
