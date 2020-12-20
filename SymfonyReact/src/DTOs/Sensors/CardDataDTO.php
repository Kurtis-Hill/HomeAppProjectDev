<?php


namespace App\DTOs\Sensors;


use App\Entity\Core\Sensortype;
use App\Entity\Sensors\Temp;
use App\HomeAppCore\Interfaces\StandardSensorInterface;

class CardDataDTO
{
    /**
     * @var array
     */
    private $sensorData = [];

    /**
     * @var string
     */
    private $sensorName;

    /**
     * @var string
     */
    private $sensorType;

    /**
     * @var string
     */
    private $sensorRoom;

    /**
     * @var string
     */
    private $cardIcon;

    /**
     * @var string
     */
    private $cardColour;

    /**
     * @var int
     */
    private $cardViewID;


    /**
     * CardDataDTO constructor
     * @param array $sensorData
     */
    public function __construct(StandardSensorInterface $sensorData)
    {

    }


  private function prepareSensorReadingsData(StandardSensorInterface $sensorData, string $type): void
  {
      $this->sensorData[] = [
          'sensorType' => $type,
          'highReading' => $sensorData->getHighReading(),
          'lowReading' => $sensorData->getLowReading(),
          'currentReading' => $sensorData->getCurrentSensorReading(),

      ];
  }

    /**
     * @return array
     */
    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    /**
     * @param array $sensorData
     */
    public function setSensorData(array $sensorData): void
    {
        $this->sensorData = $sensorData;
    }

    /**
     * @return string
     */
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    /**
     * @param string $sensorName
     */
    public function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @param string $sensorType
     */
    public function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    /**
     * @return string
     */
    public function getSensorRoom(): string
    {
        return $this->sensorRoom;
    }

    /**
     * @param string $sensorRoom
     */
    public function setSensorRoom(string $sensorRoom): void
    {
        $this->sensorRoom = $sensorRoom;
    }

    /**
     * @return string
     */
    public function getCardIcon(): string
    {
        return $this->cardIcon;
    }

    /**
     * @param string $cardIcon
     */
    public function setCardIcon(string $cardIcon): void
    {
        $this->cardIcon = $cardIcon;
    }

    /**
     * @return string
     */
    public function getCardColour(): string
    {
        return $this->cardColour;
    }

    /**
     * @param string $cardColour
     */
    public function setCardColour(string $cardColour): void
    {
        $this->cardColour = $cardColour;
    }

    /**
     * @return int
     */
    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    /**
     * @param int $cardViewID
     */
    public function setCardViewID(int $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }


}