<?php


namespace App\DTOs\Sensors;


use App\DTOs\Sensors\AbstractCardSensorDTO;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;


/**
 * Class CardViewSensorFormDTO
 * @package App\DTOs\Sensors
 */
class CardViewSensorFormDTO extends AbstractCardSensorDTO
{
    /**
     * @var array
     */
    private array $sensorData = [];

    /**
     * @var array
     */
    private array $cardIcon;

    /**
     * @var array
     */
    private array $cardColour;

    /**
     * @var array
     */
    private array $currentViewState;

    /**
     * @var int
     */
    private int $cardViewID;

    /**
     * @var array
     */
    private array $userSelections = [];


    /**
     * CardDataDTO constructor.
     * @param StandardSensorTypeInterface $sensorData
     * @param array $formOptions
     */
    public function __construct(StandardSensorTypeInterface $sensorData, array $formOptions)
    {
        $this->filterSensorTypes($sensorData);
        $this->setCardViewData($sensorData);
        $this->setUserSelections($formOptions);
    }

    /**
     * @param StandardReadingSensorInterface $sensorTypeObject
     * @param string $type
     * @param string|null $symbol
     */
    protected function setSensorData(StandardReadingSensorInterface $sensorTypeObject, string $type, string $symbol = null): void
    {
        $this->sensorData[] = [
            'sensorType' => $type,
            'highReading' => is_float($sensorTypeObject->getHighReading()) ? number_format($sensorTypeObject->getHighReading(), 2) : $sensorTypeObject->getHighReading(),
            'lowReading' => is_float($sensorTypeObject->getLowReading()) ? number_format($sensorTypeObject->getLowReading(), 2): $sensorTypeObject->getLowReading(),
            'readingSymbol' => $symbol,
            'constRecord' => $sensorTypeObject->getConstRecord(),
        ];
    }

    /**
     * @param StandardSensorTypeInterface $cardDTOData
     */
    protected function setCardViewData(StandardSensorTypeInterface $cardDTOData): void
    {
        $this->cardIcon['iconID'] = $cardDTOData->getSensorObject()->getCardIconID()->getIconID();
        $this->cardIcon['iconName'] = $cardDTOData->getSensorObject()->getCardIconID()->getIconName();

        $this->cardColour['colourID'] = $cardDTOData->getSensorObject()->getCardColourID()->getColourID();
        $this->cardColour['colour'] = $cardDTOData->getSensorObject()->getCardColourID()->getColour();

        $this->currentViewState['stateID'] = $cardDTOData->getSensorObject()->getCardStateID()->getCardstateID();
        $this->currentViewState['state'] = $cardDTOData->getSensorObject()->getCardStateID()->getState();

        $this->cardViewID = $cardDTOData->getSensorObject()->getCardViewID();
    }

    /**
     * @param array $formOptions
     */
    private function setUserSelections(array $formOptions): void
    {
        $this->userSelections['icons'] = $formOptions['icons'];
        $this->userSelections['colours'] = $formOptions['colours'];
        $this->userSelections['states'] = $formOptions['states'];
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
        return $this->cardIcon;
    }

    /**
     * @return array
     */
    public function getCardColour(): array
    {
        return $this->cardColour;
    }

    /**
     * @return array
     */
    public function getCurrentViewState(): array
    {
        return $this->currentViewState;
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
    public function getUserIconSelections(): array
    {
        return $this->userSelections['icons'];
    }

    /**
     * @return array
     */
    public function getUserColourSelections(): array
    {
        return $this->userSelections['colours'];
    }

    /**
     * @return array
     */
    public function getUserCardViewSelections(): array
    {
        return $this->userSelections['states'];
    }

}
