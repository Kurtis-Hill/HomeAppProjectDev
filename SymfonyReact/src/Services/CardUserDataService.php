<?php


namespace App\Services;

use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\DTOs\Sensors\StandardSensorCardDataDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Core\User;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\ORM\ORMException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;


/**
 * Class CardDataService.
 */
class CardUserDataService extends AbstractHomeAppUserSensorServiceCore
{
    private const SENSOR_DATA = [
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
     * @param string|null $route
     * @param string|null $deviceId
     * @return array
     */
    public function prepareAllCardDTOs(?string $route, ?string $deviceId): array
    {
       // try {
            if (isset($deviceId)) {
                if (!is_numeric($deviceId)) {
                    throw new BadRequestException('none numeric device id passed');
                }
            }

            $sensorObjects = match ($route) {
                "room" => $this->getRoomCardDataObjects($deviceId),
                "device" => $this->getDevicePageCardDataObjects($deviceId),
                default => $this->getIndexPageCardDataObjects()
            };
//        } catch (BadRequestException $e) {
//            $this->userInputErrors[] = $e->getMessage();
//        } catch (\RuntimeException $e) {
//            $this->serverErrors[] = $e->getMessage();
//        } catch (ORMException $e) {
//            error_log($e->getMessage());
//            $this->serverErrors[] = 'Card Data Query Failure';
//        } catch (Exception $e) {
//            error_log($e->getMessage());
//            $this->fatalErrors[] = 'Failed to prepare card data';
//        }

        if (!empty($sensorObjects)) {
                foreach ($sensorObjects as $cardDTO) {
                    try {
                        if ($cardDTO instanceof StandardSensorTypeInterface) {
                            $cardViewObject = $this->em->getRepository(CardView::class)->findOneBy(
                                [
                                    'userID' => $this->getUser(),
                                    'sensorNameID' => $cardDTO->getSensorNameID()
                                ]
                            );
                            if (empty($cardViewObject) || !$cardViewObject instanceof CardView) {
                                throw new BadRequestException('A Card Has Not Been Made For This Sensor');
                            }
                            $cardDTO->setCardViewObject($cardViewObject);
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
            $usersCurrentCardData = $this->em->getRepository(CardView::class)->getUsersCurrentlySelectedSensorsCardData(
                [
                    'id' => $cardViewData,
                    'userID' =>  $this->getUser()
                ],
                self::SENSOR_DATA);
        } catch(ORMException | Exception $e){
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

        if ($this->getUser() instanceof User) {
            $cardData = $cardRepository->getAllIndexSensorTypeObjectsForUser($this->getUser(), self::SENSOR_TYPE_DATA);

        }

        return $cardData ?? [];
    }


    /**
     * @param string $deviceId
     * @return array
     */
    private function getDevicePageCardDataObjects(string $deviceId): array
    {
        if (empty($deviceId)) {
            throw new BadRequestException(
                'No card data found query if you have sensors on the device please logout and back in again please'
            );
        }

        if ($this->getUser() instanceof User) {
            $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getUser(), self::SENSOR_TYPE_DATA, $deviceId);
        }

        return $cardData ?? [];
    }

    /**
     * @return array
     */
    #[ArrayShape(
        [
            'icons' => [Icons::class],
            'colours' => [CardColour::class],
            'states' => [Cardstate::class]
        ]
    )]
    private function getUserCardSelectionData(): array
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
            $cardData = $this->em->getRepository(CardView::class)->getSensorCardFormData(['id' => $cardViewID], self::SENSOR_TYPE_DATA);

            $userSelectionData = $this->getUserCardSelectionData();

            if ($userSelectionData === null) {
                return null;
            }

            if ($cardData instanceof StandardSensorTypeInterface) {
                $usersCardViewData = $this->em->getRepository(CardView::class)->findUsersCardFormDataByIdAndUser($cardViewID, $this->getUserID());
                if (!$usersCardViewData instanceof CardView) {
                    throw new BadRequestException('No card view data found for this sensor and user');
                }

                $cardData->setCardViewObject($usersCardViewData);
                $cardViewFormDTO = new CardViewSensorFormDTO($cardData, $userSelectionData);
            }
            else {
                $this->serverErrors[] = 'Sensor Not Recognised, You May Need To Update Your App';
            }
        } catch (\RuntimeException $e) {
            $this->serverErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Failed to prepare card data';
        }

        return $cardViewFormDTO ?? null;
    }

    /**
     * @param Sensors $sensorObject
     * @return CardView
     * @throws Exception
     */
    public function createNewSensorCard(Sensors $sensorObject): CardView
    {
        try {
            $iconRepository = $this->em->getRepository(Icons::class);
            $colourRepository = $this->em->getRepository(CardColour::class);
            $cardStateRepository = $this->em->getRepository(Cardstate::class);

            $maxIconNumber = $iconRepository->countAllIcons();
            $maxColourNumber = $colourRepository->countAllColours();


            $firstIconId = $iconRepository->getFirstIconId()->getIconID();
            $firstColourId = $colourRepository->getFirstColourId()->getColourID();

            $randomIcon = $iconRepository->findOneBy(['iconID' => random_int($firstIconId, $firstIconId+$maxIconNumber-1)]);
            $randomColour = $colourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber+$firstColourId-1)]);
            $onCardState = $cardStateRepository->findOneBy(['state' => Cardstate::ON]);
//            dd($randomIcon, $randomColour, $onCardState, $sensorObject, $firstIconId);
            if (!$randomIcon instanceof Icons && !$randomColour instanceof CardColour && !$onCardState instanceof Cardstate) {
                throw new \RuntimeException('Something went wrong setting default values for card');
            }

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
            $this->em->remove($newCard);
            $this->em->remove($sensorObject);
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        }
    }

    /**
     * @param string $request
     * @return array
     */
    private function getRoomCardDataObjects(string $request): array
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
