<?php


namespace App\Services;

use App\DTOs\Sensors\CardDataDTO;
use App\DTOs\Sensors\CardSensorFormDTO;
use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Core\Sensortype;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Class CardDataService
 * @package App\Services
 */
class CardDataService extends HomeAppRoomAbstract
{

    public function prepareAllIndexCardsDTO()
    {
        $tempCardData = $this->prepareAllTemperatureCards();
        $humidCardData = $this->prepareAllHumidCards();
        $analogCardData = $this->prepareAllAnalogCards();

        $allCardSensorData = [];

        array_push($allCardSensorData,
            $tempCardData,
            $humidCardData,
            $analogCardData
        );








        $cardDTOs = [];

        foreach ($cardReadings as $sensorData) {
            $cardDTOs[] = new CardDataDTO($sensorData);
        }

        return $cardReadings;
    }

    /**
     * @param string $type
     * @return array
     */
    private function prepareAllTemperatureCards(): array
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
    private function prepareAllHumidCards(): array
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
    private function prepareAllAnalogCards(): array
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
        $cardRepository = $this->em->getRepository(Cardview::class);

        $cardReadings = $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);

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

}