<?php

namespace App\UserInterface\DTO\Response\Icons;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class IconResponseDTO
{
    private int $iconID;

    private string $iconName;

    private string $description;

    public function __construct(
        int $iconID,
        string $iconName,
        string $description
    ) {
        $this->iconID = $iconID;
        $this->iconName = $iconName;
        $this->description = $description;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getIconID(): int
    {
        return $this->iconID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getIconName(): string
    {
        return $this->iconName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getDescription(): string
    {
        return $this->description;
    }
}
