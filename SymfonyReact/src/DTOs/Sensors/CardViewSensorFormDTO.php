<?php


namespace App\DTOs\Sensors;

use App\HomeAppSensorCore\Interfaces\DTO\AllCardViewDTOInterface;

/**
 * Class CardViewSensorFormDTO
 * @package App\DTOs\Sensors
 */
class CardViewSensorFormDTO implements AllCardViewDTOInterface
{
    /**
     * @var array
     */
    private array $sensorData;

    /**
     * @var array
     */
    private array $usersCurrentCardSelections;

    /**
     * @var array
     */
    private array $usersCardSelections;

    /**
     * CardDataDTO constructor.
     * @param array $usersCurrentSelection
     * @param array $usersCardSelections
     * @param array $sensorData
     */
    public function __construct(
        array $usersCurrentSelection,
        array $usersCardSelections,
        array $sensorData,
    )
    {
        $this->usersCurrentCardSelections = $usersCurrentSelection;
        $this->usersCardSelections = $usersCardSelections;
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
     * @return array
     */
    public function getCardIcon(): array
    {
        return $this->usersCurrentCardSelections['cardIcon'];
    }

    /**
     * @return array
     */
    public function getCardColour(): array
    {
        return $this->usersCurrentCardSelections['colours'];
    }

    /**
     * @return array
     */
    public function getCurrentViewState(): array
    {
        return $this->usersCurrentCardSelections['states'];
    }

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->usersCurrentCardSelections['cardViewID'];
    }

    /**
     * @return array
     */
    public function getUserIconSelections(): array
    {
        return $this->usersCardSelections['icons'];
    }

    /**
     * @return array
     */
    public function getUserColourSelections(): array
    {
        return $this->usersCardSelections['colours'];
    }

    /**
     * @return array
     */
    public function getUserCardViewSelections(): array
    {
        return $this->usersCardSelections['states'];
    }

}
