<?php

namespace App\UserInterface\DTO\Response\CurrentCardReadingDTO;

interface UserViewSensorTypeCardDataInterface
{
    public function getSensorData(): array;

    public function getSensorName(): string;

    public function getSensorType(): string;

    public function getSensorRoom(): string;

    public function getCardIcon(): string;

    public function getCardColour(): string;

    public function getCardViewID(): int;
}
