<?php

namespace App\UserInterface\DTO\Response\Icons;

use JetBrains\PhpStorm\Immutable;

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

    public function getIconID(): int
    {
        return $this->iconID;
    }

    public function getIconName(): string
    {
        return $this->iconName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
