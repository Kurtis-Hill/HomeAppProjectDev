<?php


namespace App\Services;

use App\DTOs\Sensors\CardDataDTO;
use App\Entity\Card\CardView;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use Doctrine\ORM\Query\Expr\Join;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;


/**
 * Class CardDataService.
 */
class CardDataService extends AbstractHomeAppSensorServiceCore
{
    /**
     * @var array
     */
    private array $userInputErrors = [];

    /**
     * @var array
     */
    private array $serverErrors = [];

    /**
     * @param Request $request
     * @return array
     */
    public function prepareAllCardDTOs(Request $request): array
    {
        $route = $request->request->get('view');

        $sensorObjects = match ($route) {
            "room" => $this->getRoomCardDataObjects($request),
            "device" => $this->getDeviceCardDataObjects($request),
            default => $this->getIndexCardDataObjects()
        };

        if (!empty($sensorObjects)) {
            foreach ($sensorObjects as $cardDTO) {
                $cardDTOs[] = new CardDataDTO($cardDTO);
            }
        }

        return $cardDTOs ?? [];
    }


    /**
     * @return array
     */
    private function getIndexCardDataObjects(): array
    {
        try {
            $cardRepository = $this->em->getRepository(CardView::class);

            $cardData = $cardRepository->getAllCardObjects($this->getUserID(), $this->getGroupNameIDs(), self::STANDARD_SENSOR_TYPE_DATA);
        }
        catch (\PDOException | \Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        }

        return $cardData ?? [];
    }


    /**
     * @param Request $request
     * @return array
     */
    private function getDeviceCardDataObjects(Request $request): array
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (empty($deviceName || $deviceGroup || $deviceRoom)) {
            $this->userInputErrors [] = 'No card data found query if you have devices please logout and back in again please';
        }
        else {
            $deviceDetails = [
                'deviceName' => $deviceName,
                'deviceGroup' => $deviceGroup,
                'deviceRoom' => $deviceRoom
            ];

            try {
                $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getGroupNameIDs(), $this->getUserID(), $deviceDetails, self::STANDARD_SENSOR_TYPE_DATA);
            } catch (\PDOException | \Exception $e) {
                error_log($e->getMessage());
                $this->serverErrors[] = 'Query Failure';
            }
        }

        return $cardData ?? [];
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getRoomCardDataObjects(Request $request): array
    {

    }


    public function getUserInputErrors(): array
    {
        return $this->userErrors;
    }

    /**
     * @return array
     */
    #[Pure] public function getServerErrors(): array
    {
        return array_merge($this->getUserErrors(), $this->serverErrors);
    }

















//
//    public function prepareAllIndexCardData(string $type): array
//    {
//        $cardRepository = $this->em->getRepository(CardView::class);
//
//        return $cardRepository->getAllCardReadingsIndex($this->groupNameIDs, $this->userID, $type);
//    }
//
//    public function prepareAllRoomPageCardData(string $type, array $deviceDetails): array
//    {
//        $cardReadings = [];
//
//        try {
//            $cardRepository = $this->em->getRepository(CardView::class);
//
//            $cardReadings = $cardRepository->getAllCardReadingsForRoom($this->groupNameIDs, $this->userID, $deviceDetails, $type);
//        } catch (\PDOException | \Exception $e) {
//            error_log($e->getMessage());
//        }
//
//        return $cardReadings;
//    }
//
//
//    /**
//     * @param string $type
//     */
//    private function prepareAllTemperatureCards(): array
//    {
//        $tempCardData = [];
//
//        try {
//            $cardRepository = $this->em->getRepository(CardView::class);
//
//            $tempCardData = $cardRepository->getTempCardReadings($this->groupNameIDs, $this->userID, $type);
//        } catch (\PDOException | \Exception $e) {
//            error_log($e->getMessage());
//        }
//
//        return $tempCardData;
//    }
//
//    /**
//     * @param string $type
//     */
//    private function prepareAllHumidCards(): array
//    {
//        $humidCardData = [];
//
//        try {
//            $cardRepository = $this->em->getRepository(CardView::class);
//
//            $humidCardData = $cardRepository->getHumidCardReadings($this->groupNameIDs, $this->userID, $type);
//        } catch (\PDOException | \Exception $e) {
//            error_log($e->getMessage());
//        }
//
//        return $humidCardData;
//    }
//
//    /**
//     * @return array
//     */
//    private function prepareAllAnalogCards(): array
//    {
//        $analogCardData = [];
//
//        try {
//            $cardRepository = $this->em->getRepository(CardView::class);
//
//            $analogCardData = $cardRepository->getAnalogCardReadings($this->groupNameIDs, $this->userID, $type);
//        } catch (\PDOException | \Exception $e) {
//            error_log($e->getMessage());
//        }
//
//        return $analogCardData;
//    }
}
