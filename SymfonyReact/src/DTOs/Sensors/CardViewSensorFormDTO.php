<?php


namespace App\DTOs\Sensors;


use App\DTOs\CardDTOAbstract;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;


/**
 * Class CardViewSensorFormDTO
 * @package App\DTOs\Sensors
 */
class CardViewSensorFormDTO extends CardDTOAbstract
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
    private array $userIconSelections = [];

    /**
     * @var array
     */
    private array $userColourSelections = [];

    /**
     * @var array
     */
    private array $userCardViewSelections = [];


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
     * @param StandardSensorInterface $sensorTypeObject
     * @param string $type
     */
    protected function setSensorData(StandardSensorInterface $sensorTypeObject, string $type): void
    {
        $this->sensorData[] = [
            'sensorType' => $type,
            'highReading' => is_float($sensorTypeObject->getHighReading()) ? number_format($sensorTypeObject->getHighReading(), 2) : $sensorTypeObject->getHighReading(),
            'lowReading' => is_float($sensorTypeObject->getLowReading()) ? number_format($sensorTypeObject->getLowReading(), 2): $sensorTypeObject->getLowReading(),
            'constRecord' => $sensorTypeObject->getConstRecord(),
        ];
    }

    /**
     * @param StandardSensorTypeInterface $cardDTOData
     */
    protected function setCardViewData(StandardSensorTypeInterface $cardDTOData): void
    {
        $this->cardIcon[] = [
            'iconID' => $cardDTOData->getCardViewObject()->getCardIconObject()->getIconID(),
            'iconName' => $cardDTOData->getCardViewObject()->getCardIconObject()->getIconName()
        ];

        $this->cardColour[] = [
                'ID' => $cardDTOData->getCardViewObject()->getCardColourObject()->getColourID(),
                'colour' => $cardDTOData->getCardViewObject()->getCardColourObject()->getColour()
        ];

        $this->currentViewState[] = [
            'ID' => $cardDTOData->getCardViewObject()->getCardStateObject()->getCardstateID(),
            'state' => $cardDTOData->getCardViewObject()->getCardStateObject()->getState()
        ];

        $this->cardViewID = $cardDTOData->getCardViewObject()->getCardViewID();
    }

    /**
     * @param array $formOptions
     */
    private function setUserSelections(array $formOptions): void
    {
        //dd($formOptions['icons']);
        $this->userIconSelections[] = $formOptions['icons'];
        $this->userColourSelections[] = $formOptions['colours'];
        $this->userCardViewSelections[] = $formOptions['states'];
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
        return $this->userIconSelections;
    }

    /**
     * @return array
     */
    public function getUserColourSelections(): array
    {
        return $this->userColourSelections;
    }

    /**
     * @return array
     */
    public function getUserCardViewSelections(): array
    {
        return $this->userCardViewSelections;
    }

}
