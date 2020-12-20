<?php


namespace App\DTOs\Sensors;


use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Core\Icons;
use App\Entity\Core\Room;
use App\Entity\Core\Sensors;
use App\Entity\Sensors\Temp;

class CardSensorFormDTO
{
    private $highReadings = [];

    private $lowReadings = [];

    private $sensorRoomID;

    private $sensorRoom;

    private $cardIconName;

    private $cardIconID;

    private $cardColour;

    private $carcColurID;

    private $cardViewID;


    /**
     * CardDataDTO constructor.
     * @param array $sensorData
     */
    public function __construct(array $sensorData)
    {
        $this->setHighReadings($sensorData);
        $this->setLowReadings($sensorData);
        $this->setCardIconData($sensorData['icons']);
        $this->setCardColour($sensorData['cardColour']);
        $this->setCardViewID($sensorData['cardState']);
    }

    private function setHighReadings(array $sensorData): void
    {
        if (!empty($sensorData['temp'])) {
            $this->highReadings['temp'] = ['highReading' => $sensorData['temp']->getHighReading()];
        }
        if (!empty($sensorData['humid'])) {
            $this->highReadings['humid'] = ['highReading' => $sensorData['humid']->getHighReading()];
        }
        if (!empty($sensorData['analog'])) {
            $this->highReadings['analog'] = ['highReading' => $sensorData['analog']->getHighReading()];
        }
    }


    private function setLowReadings(array $sensorData): void
    {
        if (!empty($sensorData['temp'])) {
            $this->lowReadings['temp'] = ['lowReading' => $sensorData['temp']->getLowReading()];
        }
        if (!empty($sensorData['humid'])) {
            $this->lowReadings['humid'] = ['lowReading' => $sensorData['humid']->getLowReading()];
        }
        if (!empty($sensorData['analog'])) {
            $this->lowReadings['analog'] = ['lowReading' => $sensorData['analog']->getLowReading()];
        }
    }

    /**
     * @param Room $room
     */
    private function setSensorRoom(Room $room): void
    {
        $this->sensorRoom = $room->getRoom();
        $this->sensorRoomID = $room->getRoomID();
    }

    private function setCardIconData(Icons $icons): void
    {
        $this->cardIconID = $icons->getIconid();
        $this->cardIconName = $icons->getIconname();
    }

    private function setCardColour(CardColour $cardcolour): void
    {
        $this->cardColour = $cardcolour->getColour();
        $this->carcColurID = $cardcolour->getColourID();
    }

    private function setCardViewID(Cardstate $cardview): void
    {
        $this->cardViewID = $cardview->getCardstateID();
    }
}
