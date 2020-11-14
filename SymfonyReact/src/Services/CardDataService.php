<?php


namespace App\Services;

use App\Entity\Card\Cardview;
use App\Entity\Sensors\Humid;
use App\Entity\Sensors\Temp;
use App\HomeAppCore\HomeAppRoomAbstract;
use mysql_xdevapi\Exception;
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

        return $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);


    }

    public function getAllHumidCards(string $type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        return $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);
    }

    public function getAllAnalogCards(string $type)
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        $analogCards = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);

        return $analogCards;
    }

    //Add to this array if adding more sensors
    public function prepareAllIndexCardData(string $type): array
    {
        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);

        }  catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return (!empty($cardReadings)) ? $cardReadings : [];


    }

    public function prepareAllDevicePageCardData(string $type, array $deviceDetails): array
    {
        try {
            $cardRepository = $this->em->getRepository(Cardview::class);
        }  catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $cardRepository->getAllCardReadingsForDevice($this->groupNameIDs, $this->userID, $type, $deviceDetails);
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