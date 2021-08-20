<?php

namespace App\DTOs\CardDTOs\Sensors\DTOs;

/**
 * Class CurrentReadingCardDataDTO
 * @package App\DTOs\Sensors\CardDTOs
 */
class CurrentReadingCardDataDTO implements AllCardViewDTOInterface
{
    /**
     * @var string
     */
    private string $sensorName;

    /**
     * @var string
     */
    private string $sensorType;

    /**
     * @var string
     */
    private string $sensorRoom;

    /**
     * @var string
     */
    private string $cardIcon;

    /**
     * @var string
     */
    private string $cardColour;

    /**
     * @var int
     */
    private int $cardViewID;

    /**
     * @var array
     */
    private array $sensorData;


    /**
     * CardDataDTO constructor
     * @param string $sensorName
     * @param string $sensorType
     * @param string $sensorRoom
     * @param string $cardIcon
     * @param string $cardColour
     * @param int $cardViewID
     * @param array $sensorData
     */
    public function __construct(
        string $sensorName,
        string $sensorType,
        string $sensorRoom,
        string $cardIcon,
        string $cardColour,
        int $cardViewID,
        array $sensorData,
    )
    {
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
        $this->sensorRoom = $sensorRoom;
        $this->cardIcon = $cardIcon;
        $this->cardColour = $cardColour;
        $this->cardViewID = $cardViewID;
        $this->sensorData = $sensorData;

    }

    /**
     * @return array
     */
    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    /**
     * @return string
     */
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @return string
     */
    public function getSensorRoom(): string
    {
        return $this->sensorRoom;
    }

    /**
     * @return string
     */
    public function getCardIcon(): string
    {
        return $this->cardIcon;
    }

    /**
     * @return string
     */
    public function getCardColour(): string
    {
        return $this->cardColour;
    }

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

}
