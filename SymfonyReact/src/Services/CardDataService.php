<?php


namespace App\Services;

use App\DTOs\Sensors\StandardSensorCardDataDTO;
use App\Entity\Card\CardView;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\ORMException;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
    private array $cardErrors = [];

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
        $route = $request->get('view');

        try {
            $sensorObjects = match ($route) {
                "room" => $this->getRoomCardDataObjects($request),
                "device" => $this->getDeviceCardDataObjects($request),
                default => $this->getIndexCardDataObjects()
            };
        } catch (\RuntimeException $e) {
            $this->serverErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Failed to prepare card data';
        }

        if (!empty($sensorObjects)) {
            if ($sensorObjects instanceof StandardSensorTypeInterface) {
                foreach ($sensorObjects as $cardDTO) {
                    try {
                        $cardDTOs[] = new StandardSensorCardDataDTO($cardDTO);

                    } catch (\RuntimeException $e) {
                        $this->cardErrors[] = $e->getMessage();
                    }
                }
            }
        }

        return $cardDTOs ?? [];
    }


    /**
     * @return array
     */
    private function getIndexCardDataObjects(): array
    {

        $cardRepository = $this->em->getRepository(CardView::class);

        $cardData = $cardRepository->getAllCardObjects($this->getUserID(), $this->getGroupNameIDs(), self::SENSOR_TYPE_DATA);

        if (empty($cardData)) {
            throw new \RuntimeException(
                'No sensors found'
            );
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
            throw new BadRequestException(
                'No card data found query if you have sensors on the device please logout and back in again please'
            );
        }
        else {
            $deviceDetails = [
                'deviceName' => $deviceName,
                'deviceGroup' => $deviceGroup,
                'deviceRoom' => $deviceRoom
            ];

            $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getGroupNameIDs(), $this->getUserID(), $deviceDetails, self::SENSOR_TYPE_DATA);
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


    public function getCardErrors(): array
    {
        return $this->cardErrors;
    }

    /**
     * @return array
     */
    #[Pure] public function getServerErrors(): array
    {
        return array_merge($this->getFatalErrors(), $this->serverErrors);
    }

}
