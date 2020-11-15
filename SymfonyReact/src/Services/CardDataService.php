<?php


namespace App\Services;

use App\Entity\Card\Cardview;
use App\HomeAppCore\HomeAppRoomAbstract;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CardDataService
 * @package App\Services
 */
class CardDataService extends HomeAppRoomAbstract
{
    /**
     * @param string $type
     * @return array
     */
    public function prepareAllTemperatureCards(string $type): array
    {
        $tempCardData = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $tempCardData = $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $tempCardData;
    }

    /**
     * @param string $type
     * @return array
     */
    public function prepareAllHumidCards(string $type): array
    {
        $humidCardData = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $humidCardData = $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $humidCardData;
    }

    /**
     * @param string $type
     * @return array
     */
    public function prepareAllAnalogCards(string $type): array
    {
        $analogCardData = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $analogCardData = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $analogCardData;
    }

    /**
     * @param string $type
     * @return array
     */
    public function prepareAllIndexCardData(string $type): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);
        }  catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $cardReadings;
    }


    /**
     * @param string $type
     * @param array $deviceDetails
     * @return array
     */
    public function prepareAllRoomPageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsForRoom($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        }  catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $cardReadings;
    }


    /**
     * @param string $type
     * @param array $deviceDetails
     * @return array
     */
    public function prepareAllDevicePageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsForDevice($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        }  catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $cardReadings;
    }


    public function prepareUsersCurrentCardData(string $cardViewData): array
    {
        $usersCurrentCardData = [];

        try {
            $usersCurrentCardData = $this->em->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewData, 'userID' =>  $this->getUserID()]);
        } catch(\PDOException $e){
            error_log($e->getMessage());
        } catch(\Exception $e){
            error_log($e->getMessage());
        }

        return $usersCurrentCardData;
    }

    /**
     * @param FormInterface $form
     * @param $formData
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function processForm(FormInterface $form, array $formData): FormInterface
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();
            try {
                $this->em->persist($validFormData);
            } catch(\PDOException $e){
                error_log($e->getMessage());
            } catch(\Exception $e){
                error_log($e->getMessage());
            }
        }

        return $form;
    }

}