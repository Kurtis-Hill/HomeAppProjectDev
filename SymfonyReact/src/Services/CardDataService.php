<?php


namespace App\Services;

use App\Entity\Card\Cardview;
use App\Entity\Sensors\Humid;
use App\Entity\Sensors\Temp;
use App\HomeAppCore\HomeAppRoomAbstract;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

/**
 * Class CardDataService
 * @package App\Services
 */
class CardDataService extends HomeAppRoomAbstract
{
    public function getAllTemperatureCards(string $type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $tempCards = $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);

        return $tempCards;
    }

    public function getAllHumidCards(string $type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $humidCards = $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);

        return $humidCards;
    }

    public function getAllAnalogCards(string $type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $analogCards = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);

        return $analogCards;
    }

    //Add to this array if adding more sensors
    public function returnAllCardSensorData(string $type, string $room): array
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $cardReadings = $cardRepository->getAllCardReadings($this->groupNameIDs, $this->userID, $type, $room);

        $cardData['sensorData'] = $cardReadings;

        return $cardData;
    }

    public function processForm(FormInterface $form, $formData)
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();
            $this->em->persist($validFormData);

            return false;
        }
        else {
            return $form;
        }
    }

}