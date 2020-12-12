<?php


namespace App\DTOs\Sensors;


use App\Entity\Core\Sensortype;
use App\Entity\Sensors\Temp;
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

    }




}