<?php


namespace App\DTOs\Sensors;

use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;

class CardDataDTO
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
    private array $sensorData = [];


    /**
     * CardDataDTO constructor
     * @param StandardSensorTypeInterface $cardDTO
     */
    public function __construct(StandardSensorTypeInterface $cardDTO)
    {
        if ($cardDTO instanceof Dht || $cardDTO instanceof Bmp || $cardDTO instanceof Dallas) {
            $this->setSensorData($cardDTO->getTempObject(), 'Temperature');
        }
        if ($cardDTO instanceof Dht || $cardDTO instanceof Bmp) {
            $this->setSensorData($cardDTO->getHumidObject(), 'Humidity');
        }
        if ($cardDTO instanceof Bmp) {
            $this->setSensorData($cardDTO->getLatitudeObject(), 'Soil');
        }
        if ($cardDTO instanceof Soil) {
            $this->setSensorData($cardDTO->getAnalogObject(), 'Analog');
        }

        $this->setCardViewID($cardDTO->getCardViewObject()->getCardViewID());

        $this->setSensorName($cardDTO->getCardViewObject()->getSensorObject()->getSensorName());

        $this->setCardIcon($cardDTO->getCardViewObject()->getCardIconObject()->getIconName());

        $this->setSensorType($cardDTO->getCardViewObject()->getSensorObject()->getSensorTypeID()->getSensorType());

        $this->setSensorRoom($cardDTO->getCardViewObject()->getSensorObject()->getDeviceNameID()->getRoomID()->getRoom());

        $this->setCardColour($cardDTO->getCardViewObject()->getCardColourObject()->getColour());
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
