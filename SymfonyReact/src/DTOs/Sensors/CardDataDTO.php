<?php


namespace App\DTOs\Sensors;

use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\HomeAppCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppCore\Interfaces\StandardSensorInterface;

class CardDataDTO
{
    /**
     * @var array
     */
    private array $sensorData = [];

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
     * CardDataDTO constructor
     * @param StandardSensorTypeInterface $sensorData
     */
    public function __construct(StandardSensorTypeInterface $sensorData)
    {
        if ($sensorData instanceof Dht || $sensorData instanceof Bmp || $sensorData instanceof Dallas) {
            $this->setSensorData($sensorData->getTempObject(), 'Temperature');
        }
        if ($sensorData instanceof Dht || $sensorData instanceof Bmp) {
            $this->setSensorData($sensorData->getHumidObject(), 'Humidity');
        }
        if ($sensorData instanceof Bmp) {
            $this->setSensorData($sensorData->getLatitudeObject(), 'Soil');
        }
        if ($sensorData instanceof Soil) {
            $this->setSensorData($sensorData->getAnalogObject(), 'Analog');
        }

        $this->setCardViewID($sensorData->getCardViewObject()->getCardViewID());

        $this->setSensorName($sensorData->getCardViewObject()->getSensorObject()->getSensorName());

        $this->setCardIcon($sensorData->getCardViewObject()->getCardIconObject()->getIconName());

        $this->setSensorType($sensorData->getCardViewObject()->getSensorObject()->getSensorTypeID()->getSensorType());

        $this->setSensorRoom($sensorData->getCardViewObject()->getSensorObject()->getDeviceNameID()->getRoomID()->getRoom());

        $this->setCardColour($sensorData->getCardViewObject()->getCardColourObject()->getColour());
    }

    /**
     * @param StandardSensorInterface $sensorTypeObject
     * @param string $type
     */
    private function setSensorData(StandardSensorInterface $sensorTypeObject, string $type): void
    {
        $this->sensorData[] = [
            'sensorType' => $type,
            'highReading' => $sensorTypeObject->getHighReading(),
            'lowReading' => $sensorTypeObject->getLowReading(),
            'currentReading' => $sensorTypeObject->getCurrentSensorReading(),
            'getCurrentHighDifference' => $sensorTypeObject->getMeasurementDifferenceHighReading(),
            'getCurrentLowDifference' => $sensorTypeObject->getMeasurementDifferenceLowReading()
        ];
    }

    /**
     * @param string $sensorName
     */
    private function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    /**
     * @param string $cardIcon
     */
    private function setCardIcon(string $cardIcon): void
    {
        $this->cardIcon = $cardIcon;
    }

    /**
     * @param string $sensorType
     */
    private function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    /**
     * @param string $sensorRoom
     */
    private function setSensorRoom(string $sensorRoom): void
    {
        $this->sensorRoom = $sensorRoom;
    }

    /**
     * @param int $cardViewID
     */
    private function setCardViewID(int $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    /**
     * @param string $cardColour
     */
    private function setCardColour(string $cardColour): void
    {
        $this->cardColour = $cardColour;
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
