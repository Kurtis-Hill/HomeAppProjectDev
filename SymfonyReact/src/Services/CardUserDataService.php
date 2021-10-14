<?php


namespace App\Services;

use App\Core\APIInterface\APIErrorInterface;
use App\Core\UserInterface\APISensorUserInterface;
use App\Devices\Entity\Devices;
use App\DTOs\CardDTOs\Factories\CardFactories\CardViewDTOFactory;
use App\DTOs\CardDTOs\Sensors\DTOs\CardViewSensorFormDTO;
use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Card\Icons;
use App\Entity\Core\User;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\HomeAppSensorCore\Interfaces\Services\LoggedInUserRequiredInterface;
use App\Services\CardServices\CardDataFilterService;
use App\Services\CardServices\CardDataProviderInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;


/**
 * Class CardDataService.
 */
class CardUserDataService implements APIErrorInterface, LoggedInUserRequiredInterface, CardDataProviderInterface
{
    use FormProcessorTrait;

    /**
     * @var ?User
     */
    private ?User $user;

    /**
     * @var CardViewDTOFactory
     */
    private CardViewDTOFactory $cardViewDTOFactory;

    /**
     * @var CardDataFilterService
     */
    private $cardDataFilterService;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * @var array
     */
    protected array $userInputErrors = [];

    /**
     * @var array
     */
    private array $cardErrors = [];

    /**
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param CardViewDTOFactory $cardViewDTOFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        CardViewDTOFactory $cardViewDTOFactory,
        CardDataFilterService $cardDataFilterService,
    )
    {
        $this->em = $em;
        $this->user = $security->getUser();
        $this->cardViewDTOFactory = $cardViewDTOFactory;
        $this->cardDataFilterService = $cardDataFilterService;
    }

    /**
     * @param string|null $route
     * @param Devices|null $deviceId
     * @param array $cardFilters
     * @return array
     */
    public function prepareCardDTOs(?string $route = null, ?Devices $deviceId = null, array $cardFilters = []): array
    {
        $sensorTypes = SensorType::ALL_SENSOR_TYPE_DATA;
        if (!empty($cardFilters)) {
            $sensorTypes = $this->cardDataFilterService->filterSensorTypes($sensorTypes, $cardFilters);
        }

//        try {
            $sensorObjects = match ($route) {
                "room" => $this->getRoomCardDataObjects($sensorTypes),
                "device" => $this->getDevicePageCardDataObjects($deviceId),
                default => $this->getIndexPageCardDataObjects($sensorTypes)
            };
//        } catch (BadRequestException $e) {
//            $this->userInputErrors[] = $e->getMessage();
//        } catch (ORMException $e) {
//            error_log($e->getMessage());
//            $this->serverErrors[] = 'Card Data Query Failure';
//        }

        $cardViewDTOFactory = $this->cardViewDTOFactory->build(CardViewDTOFactory::SENSOR_TYPE_CURRENT_READING_SENSOR_CARD);
        if (!empty($sensorObjects)) {
                foreach ($sensorObjects as $cardDTO) {
                    try {
                        $cardViewObject = $this->em->getRepository(CardView::class)->findOneBy(
                            [
                                'userID' => $this->getUser(),
                                'sensorNameID' => $cardDTO->getSensorNameID()
                            ]
                        );
                        if (!$cardViewObject instanceof CardView) {
                            throw new BadRequestException('A Card Has Not Been Made For This Sensor ' . $cardDTO->getSensorObject()->getSensorName());
                        }
                        $cardDTO->setCardViewObject($cardViewObject);

                        $cardDTOs[] = $cardViewDTOFactory->makeDTO($cardDTO);
                    } catch (BadRequestException $e) {
                        $this->cardErrors[] = $e->getMessage();
                    }
                }
        }

        return $cardDTOs ?? [];
    }

    private function getIndexPageCardDataObjects($filteredSensorTypes)
    {
        $cardRepository = $this->em->getRepository(CardView::class);
        $standardSensorTypeCards = $this->getStandardSensorTypeData($cardRepository);

        return $standardSensorTypeCards;
    }

    private function getIndexUserDefaultView(): array
    {
        $standardSensorTypeCards = $this->getStandardSensorTypeData();

        return $standardSensorTypeCards;
    }

    /**
     * @param string $cardViewData
     * @return array
     */
    public function getSelectedCardDataById(string $cardViewData): array
    {
        try {
            $usersCurrentCardData = $this->em->getRepository(CardView::class)->getUsersCurrentlySelectedSensorsCardData(
                [
                    'id' => $cardViewData,
                    'userID' => $this->getUser()
                ],
                SensorType::SENSOR_READING_TYPE_DATA);
        } catch(ORMException $e){
            $this->serverErrors[] = 'Query error trying to find users card data';
            error_log($e->getMessage());
        }

        return $usersCurrentCardData ?? [];
    }


    /**
     * @return array
     */
    private function getStandardSensorTypeData(): array
    {
        $cardRepository = $this->em->getRepository(CardView::class);
        $cardData = $cardRepository->getAllSensorTypeObjectsForUser($this->getUser(), SensorType::ALL_SENSOR_TYPE_DATA, Cardstate::INDEX_ONLY);

        return $cardData ?? [];
    }


    /**
     * @param int|null $deviceId
     * @return array
     */
    private function getDevicePageCardDataObjects(?Devices $deviceId = null): array
    {
        if (!$deviceId instanceof Devices) {
            throw new BadRequestException(
                'No card data found query if you have sensors on the device please logout and back in again please'
            );
        }

        $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getUser(), SensorType::ALL_SENSOR_TYPE_DATA, $deviceId->getDeviceNameID());

        return $cardData ?? [];

    }

    #[ArrayShape(
        [
            'icons' => [Icons::class],
            'colours' => [CardColour::class],
            'states' => [Cardstate::class]
        ]
    )]
    private function getCardSelectionData(): array
    {
        $icons = $this->em->getRepository(Icons::class)->getAllIcons();
        $colours = $this->em->getRepository(CardColour::class)->getAllColours();
        $states = $this->em->getRepository(Cardstate::class)->getAllStates();

        if (empty($icons) || empty($colours) || empty($states)) {
            throw new RuntimeException('User selection data has failed to process');
        }

        return ['icons' => $icons, 'colours' => $colours, 'states' => $states];
    }


    /**
     * @param CardView $cardViewObject
     * @return CardViewSensorFormDTO|null
     */
    public function getCardViewFormDTO(CardView $cardViewObject): ?CardViewSensorFormDTO
    {
//        try {
            $cardData = $this->em->getRepository(Sensors::class)->getSensorReadingTypeCardFormDataBySensor($cardViewObject->getSensorNameID(), SensorType::ALL_SENSOR_TYPE_DATA);
            if ($cardData instanceof SensorInterface) {
                $userSelectionData = $this->getCardSelectionData();
                $cardData->setCardViewObject($cardViewObject);

                $cardFormDTOFactory = $this->cardViewDTOFactory->build(CardViewDTOFactory::SENSOR_TYPE_READING_FORM_CARD);
                $cardViewFormDTO = $cardFormDTOFactory->makeDTO($cardData, $userSelectionData);
            }
            else {
                $this->serverErrors[] = 'Sensor Not Recognised, You May Need To Update Your App';
            }
//        } catch (BadRequestException $e) {
//            $this->userInputErrors[] = $e->getMessage();
//        } catch (RuntimeException $e) {
//            $this->serverErrors[] = $e->getMessage();
//        } catch (ORMException $e) {
//            error_log($e->getMessage());
//            $this->serverErrors[] = 'Card Data Query Failure';
//        }

//        dd($cardViewFormDTO);
        return $cardViewFormDTO ?? null;
    }

    /**
     * @param Sensors $sensorObject
     * @param User $user
     * @return CardView|null
     */
    public function createNewSensorCard(Sensors $sensorObject, User $user): ?CardView
    {
//        try {
            $randomIcon = $this->returnRandomIcon();

            $randomColour = $this->returnRandomColour();

            $cardStateRepository = $this->em->getRepository(Cardstate::class);

            $onCardState = $cardStateRepository->findOneBy(['state' => Cardstate::ON]);

            if (!$onCardState instanceof Cardstate) {
                throw new RuntimeException('Something went wrong setting a defualt card state');
            }

            $newCard = new CardView();
            $newCard->setSensorNameID($sensorObject);
            $newCard->setUserID($user);
            $newCard->setCardIconID($randomIcon);
            $newCard->setCardColourID($randomColour);
            $newCard->setCardStateID($onCardState);

            $this->em->persist($newCard);

            return $newCard;
//        } catch (RuntimeException $exception) {
//            error_log($exception->getMessage());
//            $this->serverErrors[] = $exception->getMessage();
//        }
//        catch (ORMException $e) {
//            error_log($e->getMessage());
//            $this->serverErrors[] = 'Card Data Query Failure';
//        }

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
            throw new RuntimeException('Failed setting random icon');
        }

        return $randomIcon;
    }

    private function returnRandomColour(): CardColour
    {
        $colourRepository = $this->em->getRepository(CardColour::class);
        $maxColourNumber = $colourRepository->countAllColours();
        $firstColourId = $colourRepository->getFirstColourId()->getColourID();
        $randomColour = $colourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber + $firstColourId -1)]);

        if (!$randomColour instanceof CardColour) {
            throw new RuntimeException('Failed setting random colour');
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

    /**
     * @return APISensorUserInterface|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

}
