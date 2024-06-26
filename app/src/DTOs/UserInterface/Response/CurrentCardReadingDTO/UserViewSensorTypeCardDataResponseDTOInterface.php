<?php

namespace App\DTOs\UserInterface\Response\CurrentCardReadingDTO;

interface UserViewSensorTypeCardDataResponseDTOInterface
{
    public function getSensorData(): array;

    public function getSensorName(): string;

    public function getSensorType(): string;

    public function getSensorRoom(): string;

    public function getCardIcon(): string;

    public function getCardColour(): string;

    public function getCardViewID(): int;

    public function getCardType(): string;
}
