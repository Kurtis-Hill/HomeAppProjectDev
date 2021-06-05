<?php


namespace App\Services;

use App\DTOs\Sensors\CardViewSensorFormDTO;
use App\DTOs\Sensors\StandardSensorCardDataDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
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
        try {
            if (isset($deviceId) && !is_numeric($deviceId)) {
                throw new BadRequestException('none numeric device id passed');
            }

            $sensorObjects = match ($route) {
                "room" => $this->getRoomCardDataObjects($deviceId),
                "device" => $this->getDevicePageCardDataObjects($deviceId),
                default => $this->getIndexPageCardDataObjects()
            };
        } catch (BadRequestException $e) {
            $this->userInputErrors[] = $e->getMessage();
        } catch (\RuntimeException $e) {
            $this->serverErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->fatalErrors[] = 'Failed to prepare card data';
        }

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
                    } catch (BadRequestException $e) {
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

            if ($usersCurrentCardData === null) {
                throw new BadRequestException('CardID not recognised');
            }
        } catch(BadRequestException $e){
            $this->userInputErrors[] = $e->getMessage();
            error_log($e->getMessage());
        }
        catch(ORMException $e){
            $this->serverErrors[] = 'Query error trying to find users card data';
            error_log($e->getMessage());
        }

        return $usersCurrentCardData ?? [];
    }

    private function checkIfDeviceExists(int $deviceId)
    {
        $device = $this->em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $deviceId]);

//        dd($device);
        if ($device === null) {
//            dd('hahxx');
            throw new BadRequestException('Device given is not recognised');
        }
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

        $this->checkIfDeviceExists($deviceId);

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
    public function createNewSensorCard(Sensors $sensorObject, User $user): ?CardView
    {
        try {
            $randomIcon = $this->returnRandomIcon();

            $randomColour = $this->returnRandomColour();

            $cardStateRepository = $this->em->getRepository(Cardstate::class);

            $onCardState = $cardStateRepository->findOneBy(['state' => Cardstate::ON]);

            if (!$onCardState instanceof Cardstate) {
                throw new \RuntimeException('Something went wrong setting a defualt card state');
            }

            $newCard = new CardView();
            $newCard->setSensorNameID($sensorObject);
            $newCard->setUserID($user);
            $newCard->setCardIconID($randomIcon);
            $newCard->setCardColourID($randomColour);
            $newCard->setCardStateID($onCardState);

            $this->em->persist($newCard);

            return $newCard;
        } catch (\RuntimeException $exception) {
            error_log($exception->getMessage());
            $this->serverErrors[] = $exception->getMessage();
        }
        catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        }

        if (isset($newCard)) {
            $this->em->remove($newCard);
        }
        $this->em->remove($sensorObject);

        return null;
    }

    private function returnRandomIcon(): Icons
    {
        $iconRepository = $this->em->getRepository(Icons::class);
        $maxIconNumber = $iconRepository->countAllIcons();
        $firstIconId = $iconRepository->getFirstIconId()->getIconID();
        $randomIcon = $iconRepository->findOneBy(['iconID' => random_int($firstIconId, $firstIconId+$maxIconNumber-1)]);

        if (!$randomIcon instanceof Icons) {
            throw new \RuntimeException('Failed setting random icon');
        }

        return $randomIcon;
    }

    private function returnRandomColour(): CardColour
    {
        $colourRepository = $this->em->getRepository(CardColour::class);
        $maxColourNumber = $colourRepository->countAllColours();
        $firstColourId = $colourRepository->getFirstColourId()->getColourID();
        $randomColour = $colourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber+$firstColourId-1)]);

        if (!$randomColour instanceof CardColour) {
            throw new \RuntimeException('Failed setting random colour');
        }

        return $randomColour;
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
