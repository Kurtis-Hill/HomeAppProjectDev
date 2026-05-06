<?php

namespace App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoolReadingType;

use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Services\Request\RequestTypeEnum;
use Symfony\Component\Serializer\Annotation\Groups;

class BoolBoundaryReadingsTypeResponseDTO implements BoundaryReadingTypeResponseInterface
{
    public function __construct(
        private int $sensorReadingTypeID,
        private string $readingType,
        private bool $constRecord,
        private ?bool $expectedReading = null,
    ){
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorReadingTypeID() : int
    {
        return $this->sensorReadingTypeID;

    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getReadingType() : string
    {
        return $this->readingType;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getExpectedReading() : ?bool
    {
        return $this->expectedReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getConstRecord() : bool
    {
        return $this->constRecord;
    }
}
