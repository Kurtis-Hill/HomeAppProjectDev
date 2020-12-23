<?php


namespace App\Services;

use App\DTOs\Sensors\CardDataDTO;
use App\Entity\Card\CardView;
use App\HomeAppCore\HomeAppSensorServiceCoreAbstract;


/**
 * Class CardDataService.
 */
class CardDataService extends HomeAppSensorServiceCoreAbstract
{
    public function prepareAllIndexCardDTOs()
    {
        $sensorObjects = $this->getIndexCardDataObjects();

        $cardDTOs = [];

        foreach ($sensorObjects as $sensorData) {
            $cardDTOs[] = new CardDataDTO($sensorData);
        }

        return $cardDTOs;
    }

    private function getIndexCardDataObjects()
    {
        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            return $cardRepository->getAllIndexCardObjects($this->getUserID(), $this->getGroupNameIDs());
        }
        catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }


return $cardRepository->getAllIndexCardObjects($this->getUserID(), $this->getGroupNameIDs())
    }

    public function prepareAllIndexCardData(string $type): array
    {
        $cardRepository = $this->em->getRepository(CardView::class);
//        dd('ety');
        return $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);
    }

    public function prepareAllRoomPageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $cardReadings = $cardRepository->getAllCardReadingsForRoom($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }

        return $cardReadings;
    }

    public function prepareAllDevicePageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $cardReadings = $cardRepository->getAllCardReadingsForDevice($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }

        return $cardReadings;
    }

    /**
     * @param string $type
     */
    private function prepareAllTemperatureCards(): array
    {
        $tempCardData = [];

        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $tempCardData = $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }

        return $tempCardData;
    }

    /**
     * @param string $type
     */
    private function prepareAllHumidCards(): array
    {
        $humidCardData = [];

        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $humidCardData = $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }

        return $humidCardData;
    }

    /**
     * @return array
     */
    private function prepareAllAnalogCards(): array
    {
        $analogCardData = [];

        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $analogCardData = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
        }

        return $analogCardData;
    }
}
