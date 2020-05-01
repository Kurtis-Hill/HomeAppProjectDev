<?php


namespace App\Services;

use App\Entity\Card\Cardview;
use App\Entity\Sensors\Humid;
use App\Entity\Sensors\Temp;
use App\HomeAppCore\HomeAppRoomAbstract;

/**
 * Class CardDataService
 * @package App\Services
 */
class CardDataService extends HomeAppRoomAbstract
{
    public function getAllTemperatureCards($type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $tempCards = $cardRepository->getTempCardReadings($this->groupNameid, $this->userID, $type);

        return $tempCards;
    }

    public function getAllHumidCards($type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $humidCards = $cardRepository->getHumidCardReadings($this->groupNameid, $this->userID, $type);

        return $humidCards;
    }

    public function getAllAnalogCards($type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $analogCards = $cardRepository->getAnalogCardReadings($this->groupNameid, $this->userID, $type);

        return $analogCards;
    }

    //Add to this array if adding more sensors
    public function returnAllCardSensorData($type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $tempHumid = $cardRepository->getTempHumidCardReadings($this->groupNameid, $this->userID, $type);
        $analog = $cardRepository->getAnalogCardReadings($this->groupNameid, $this->userID, $type);

        $cardData = [];
        $cardData['tempHumid'] = $tempHumid;
        $cardData['analog'] = $analog;

        return $cardData;
    }



    private function queryForTempCardData()
    {

    }

    private function queryForHumidCardData()
    {

    }

    private function queryForAnalogCardData()
    {

    }

}