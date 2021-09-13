<?php


namespace App\DTOs\CardDTOs\Sensors\DTOs;

/**
 * Class CardViewSensorFormDTO
 * @package App\DTOs\Sensors
 */
class CardViewSensorFormDTO implements AllCardViewDTOInterface, CardViewFormDTO
{
    /**
     * @var array
     */
    private array $sensorData;

//    /**
//     * @var array
//     */
//    private array $usersCurrentCardSelections;
//
//    /**
//     * @var array
//     */
//    private array $usersCardSelections;

    /**
     * @var array
     */
    private array $currentCardIcon;

    /**
     * @var array
     */
    private array $currentCardColour;

    /**
     * @var array
     */
    private array $currentState;

    /**
     * @var string
     */
    private string $cardViewID;

    /**
     * @var array
     */
    private array $iconSelection;

    /**
     * @var array
     */
    private array $colourSelection;

    /**
     * @var array
     */
    private array $cardStates;

//    /**
//     * CardDataDTO constructor.
//     * @param array $usersCurrentSelection
//     * @param array $usersCardSelections
//     * @param array $sensorData
//     */
//    public function __construct(
//        array $usersCurrentSelection,
//        array $usersCardSelections,
//        array $sensorData,
//    )
//    {
//        $this->usersCurrentCardSelections = $usersCurrentSelection;
//        $this->usersCardSelections = $usersCardSelections;
//        $this->sensorData = $sensorData;
//    }

    public function __construct(
        array $currentCardIcon,
        array $currentCardColour,
        array $currentState,
        string $cardViewID,
        array $iconSelection,
        array $colourSelection,
        array $cardStates,
        array $sensorData,
    ) {
       $this->currentCardIcon = $currentCardIcon;
       $this->currentCardColour = $currentCardColour;
       $this->currentState = $currentState;
       $this->cardViewID = $cardViewID;
       $this->iconSelection = $iconSelection;
       $this->colourSelection = $colourSelection;
       $this->cardStates = $cardStates;
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
        return $this->currentCardIcon;
    }

    /**
     * @return array
     */
    public function getCardColour(): array
    {
        return $this->currentCardColour;
    }

    /**
     * @return array
     */
    public function getCurrentViewState(): array
    {
        return $this->currentState;
    }

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    /**
     * @return array
     */
    public function getCurrentCardIcon(): array
    {
        return $this->currentCardIcon;
    }

    /**
     * @return array
     */
    public function getCurrentCardColour(): array
    {
        return $this->currentCardColour;
    }

    /**
     * @return array
     */
    public function getCurrentState(): array
    {
        return $this->currentState;
    }

    /**
     * @return array
     */
    public function getIconSelection(): array
    {
        return $this->iconSelection;
    }

    /**
     * @return array
     */
    public function getUserColourSelections(): array
    {
        return $this->colourSelection;
    }

    /**
     * @return array
     */
    public function getUserCardViewSelections(): array
    {
        return $this->cardStates;
    }
}
