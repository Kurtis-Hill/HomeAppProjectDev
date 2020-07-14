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
    public function returnAllCardSensorData($type, $room)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $tempHumid = $cardRepository->getAllCardReadings($this->groupNameid, $this->userID, $type, $room);
       // $analog = $cardRepository->getAnalogCardReadings($this->groupNameid, $this->userID, $type, $room);

      //  $cardData = [];
        $cardData['sensorData'] = $tempHumid;
       // $cardData['analog'] = $analog;

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
           $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $name = $error->getOrigin()->getName();
                $errors[] = $error->getMessage();
            }
//            $message = join("\n", $errors);
            //dd($errors);
            return $errors;
        }
    }

}