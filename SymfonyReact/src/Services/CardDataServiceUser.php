<?php


namespace App\Services;

use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\DTOs\Sensors\StandardSensorCardDataDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Services\AbstractHomeAppUserSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use App\Services\SensorData\AbstractSensorService;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;


/**
 * Class CardDataService.
 */
class CardDataServiceUser extends AbstractHomeAppUserSensorServiceCore
{
    protected const SENSOR_DATA = [
        Sensors::TEMPERATURE => [
            'alias' => 'temp',
            'object' => Temperature::class
        ],
        Sensors::HUMIDITY => [
            'alias' => 'humid',
            'object' => Humidity::class
        ],
        Sensors::ANALOG => [
            'alias' => 'analog',
            'object' => Analog::class
        ],
        Sensors::LATITUDE => [
            'alias' => 'lat',
            'object' => Latitude::class
        ],

    ];

    /**
     * @var array
     */
    private array $cardErrors = [];

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
                "device" => $this->getDevicePageCardDataObjects($request),
                default => $this->getIndexPageCardDataObjects()
            };
//            dd($sensorObjects);
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
                foreach ($sensorObjects as $cardDTO) {
                    try {
                        if ($cardDTO instanceof StandardSensorTypeInterface) {
                            $cardDTOs[] = new StandardSensorCardDataDTO($cardDTO);
                        }
                    } catch (\RuntimeException $e) {
                        $this->cardErrors[] = $e->getMessage();
                    }
                }
        }

        return $cardDTOs ?? [];
    }

    /**
     * @param string $cardViewData
     * @return array
     */
    public function editSelectedCardData(string $cardViewData): array
    {
        try {
            $usersCurrentCardData = $this->em->getRepository(CardView::class)->getUsersCurrentlySelectedSensorsCardData(['id' => $cardViewData, 'userID' =>  $this->getUserID()], self::SENSOR_DATA);
        } catch(ORMException | \Exception $e){
            $this->serverErrors[] = 'Query error trying to find users card data';
            error_log($e->getMessage());
        }

        return $usersCurrentCardData ?? [];
    }


    /**
     * @return array
     */
    private function getIndexPageCardDataObjects(): array
    {
        $cardRepository = $this->em->getRepository(CardView::class);

        $cardData = $cardRepository->getAllCardObjectsForUser($this->getUserID(), $this->getGroupNameIDs(), AbstractSensorService::SENSOR_TYPE_DATA);

        return $cardData ?? [];
    }


    /**
     * @param Request $request
     * @return array
     */
    private function getDevicePageCardDataObjects(Request $request): array
    {
        $deviceId = $request->get('device-name');

        if (empty($deviceId)) {
            throw new BadRequestException(
                'No card data found query if you have sensors on the device please logout and back in again please'
            );
        }

        $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getUserID(), $this->getGroupNameIDs(), AbstractSensorService::SENSOR_TYPE_DATA, $deviceId);
//        dd($cardData, 'card data');
        return $cardData ?? [];
    }

    /**
     * @return array|null
     */
    #[ArrayShape(
        [
            'icons' => Icons::class,
            'colours' => CardColour::class,
            'states' => Cardstate::class
        ]
    )]
    private function getUserSensorCardSelectionData(): ?array
    {
        $icons = $this->em->getRepository(Icons::class)->getAllIcons();
        $colours = $this->em->getRepository(CardColour::class)->getAllColours();
        $states = $this->em->getRepository(Cardstate::class)->getAllStates();

        if (empty($icons) || empty($colours) || empty($states)) {
            throw new \RuntimeException('user selection data has failed to process');
        }

        return ['icons' => $icons, 'colours' => $colours, 'states' => $states];
    }



    /**
     * @param string $cardViewID
     * @return CardViewSensorFormDTO|null
     */
    public function getCardViewFormDTO(string $cardViewID): ?CardViewSensorFormDTO
    {
        try {
            $cardData = $this->em->getRepository(CardView::class)->getSensorCardFormData(['id' => $cardViewID], AbstractSensorService::SENSOR_TYPE_DATA);

            $userSelectionData = $this->getUserSensorCardSelectionData();

            if ($userSelectionData === null) {
                return null;
            }

            if ($cardData instanceof StandardSensorTypeInterface) {
                $cardViewFormDTO = new CardViewSensorFormDTO($cardData, $userSelectionData);
            }
            else {
                $this->serverErrors[] = 'Query error for card view form';
            }
        } catch (\RuntimeException $e) {
            $this->serverErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Failed to prepare card data';
        }

        return $cardViewFormDTO ?? null;
    }

    /**
     * @param Sensors $sensorObject
     * @return CardView
     * @throws ORMException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createNewSensorCard(Sensors $sensorObject): CardView
    {
        try {
            $maxIconNumer = $this->em->createQueryBuilder()
                ->select('count(icons.iconID)')
                ->from(Icons::class, 'icons')
                ->getQuery()->getSingleScalarResult();

            $randomIcon = $this->em->getRepository(Icons::class)->findOneBy(['iconID' => random_int(1, $maxIconNumer)]);
            $randomColour = $this->em->getRepository(CardColour::class)->findOneBy(['colourID' => random_int(1, 4)]);
            $onCardState = $this->em->getRepository(Cardstate::class)->findOneBy(['cardStateID' => Cardstate::ON]);

            $newCard = new CardView();
            $newCard->setSensorNameID($sensorObject);
            $newCard->setUserID($this->getUser());
            $newCard->setCardIconID($randomIcon);
            $newCard->setCardColourID($randomColour);
            $newCard->setCardStateID($onCardState);

            $this->em->persist($newCard);
            $this->em->flush();

            return $newCard;
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getRoomCardDataObjects(Request $request): array
    {

    }

    /**
     * @return array
     */
    public function getCardErrors(): array
    {
        return $this->cardErrors;
    }
}
