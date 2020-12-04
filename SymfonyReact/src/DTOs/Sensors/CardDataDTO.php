<?php


namespace App\DTOs\Sensors;


use App\Entity\Core\Sensortype;
use App\HomeAppCore\Interfaces\StandardSensorInterface;

class CardDataDTO
{
    private $sensorData = [];

    private $sensorName;

    private $sensorType;

    private $sensorRoom;

    private $cardIcon;

    private $cardColour;

    private $cardViewID;


    /**
     * CardDataDTO constructor.
     * @param array $sensorData
     */
    public function __construct(array $sensorData)
    {
        if ($sensorData['deviceType'] === Sensortype::DHT_SENSOR) {
            $this->prepareDHTSensorData($sensorData);
        }
    }

    private function prepareDHTSensorData($sensorData)
    {
        foreach ($sensorData as $data)
            $this->sensorData['temp'] =  [
                'highTempReading' => $data['t_highReading'],
                'lowTempReading' => $data['t_lowReading'],
                'currentReading' => $data['t_tempReading'],
            ];

            $this->sensorData['humid'] = [
                'highTempReading' => $data['h_highReading'],
                'lowTempReading' => $data['h_lowReading'],
                'currentReading' => $data['h_tempReading'],
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
     * @return mixed
     */
    public function getSensorName()
    {
        return $this->sensorName;
    }

    /**
     * @param mixed $sensorName
     */
    public function setSensorName($sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    /**
     * @return mixed
     */
    public function getSensorType()
    {
        return $this->sensorType;
    }

    /**
     * @param mixed $sensorType
     */
    public function setSensorType($sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    /**
     * @return mixed
     */
    public function getSensorRoom()
    {
        return $this->sensorRoom;
    }

    /**
     * @param mixed $sensorRoom
     */
    public function setSensorRoom($sensorRoom): void
    {
        $this->sensorRoom = $sensorRoom;
    }

    /**
     * @return mixed
     */
    public function getCardIcon()
    {
        return $this->cardIcon;
    }

    /**
     * @param mixed $cardIcon
     */
    public function setCardIcon($cardIcon): void
    {
        $this->cardIcon = $cardIcon;
    }

    /**
     * @return mixed
     */
    public function getCardColour()
    {
        return $this->cardColour;
    }

    /**
     * @param mixed $cardColour
     */
    public function setCardColour($cardColour): void
    {
        $this->cardColour = $cardColour;
    }

    /**
     * @return mixed
     */
    public function getCardViewID()
    {
        return $this->cardViewID;
    }

    /**
     * @param mixed $cardViewID
     */
    public function setCardViewID($cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

}