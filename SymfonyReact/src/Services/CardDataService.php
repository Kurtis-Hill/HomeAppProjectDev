<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services;

use App\DTOs\Sensors\CardDataDTO;
use App\Entity\Card\Cardview;
use App\HomeAppCore\HomeAppCoreAbstract;


/**
 * Class CardDataService.
 */
class CardDataService extends HomeAppCoreAbstract
{
    public function prepareAllIndexCardsDTO()
    {
        $tempCardData = $this->prepareAllTemperatureCards();
        $humidCardData = $this->prepareAllHumidCards();
        $analogCardData = $this->prepareAllAnalogCards();

        $allCardSensorData = [];

        array_push(
            $allCardSensorData,
            $tempCardData,
            $humidCardData,
            $analogCardData
        );

        $cardDTOs = [];

        foreach ($cardDTOs as $sensorData) {
            $cardDTOs[] = new CardDataDTO($sensorData);
        }

       // return $cardReadings;
    }

    public function prepareAllIndexCardData(string $type): array
    {
        $cardRepository = $this->em->getRepository(Cardview::class);

        return $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);
    }

    public function prepareAllRoomPageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsForRoom($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $cardReadings;
    }

    public function prepareAllDevicePageCardData(string $type, array $deviceDetails): array
    {
        $cardReadings = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $cardReadings = $cardRepository->getAllCardReadingsForDevice($this->groupNameIDs, $this->userID, $deviceDetails, $type);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        } catch (\Exception $e) {
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
            $cardRepository = $this->em->getRepository(Cardview::class);

            $tempCardData = $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        } catch (\Exception $e) {
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
            $cardRepository = $this->em->getRepository(Cardview::class);

            $humidCardData = $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $humidCardData;
    }

    /**
     * @param string $type
     */
    private function prepareAllAnalogCards(): array
    {
        $analogCardData = [];

        try {
            $cardRepository = $this->em->getRepository(Cardview::class);

            $analogCardData = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $analogCardData;
    }
}
