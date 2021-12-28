<?php

namespace App\Services\CardServices;

use App\API\Traits\FormProcessorTrait;
use App\Core\APIInterface\APIErrorInterface;
use App\Core\UserInterface\APISensorUserInterface;
use App\Devices\Entity\Devices;
use App\DTOs\CardDTOs\Factories\CardFactories\CardViewDTOFactory;
use App\DTOs\CardDTOs\Sensors\DTOs\CardViewSensorFormDTO;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\Services\LoggedInUserRequiredInterface;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;

class CardDTOCreatorService implements APIErrorInterface, LoggedInUserRequiredInterface, CardDataProviderInterface
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
    private CardDataFilterService $cardDataFilterService;

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
        $cardData = $cardRepository->getAllSensorTypeObjectsForUser($this->getUser(), $filteredSensorTypes, Cardstate::INDEX_ONLY);

        return $cardData;
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

        $cardData =  $this->em->getRepository(CardView::class)->getAllCardReadingsForDevice($this->getUser(), SensorType::ALL_SENSOR_TYPE_DATA, $deviceId);

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
            $cardData = $this->em->getRepository(Sensor::class)->getSensorReadingTypeCardFormDataBySensor($cardViewObject->getSensorNameID(), SensorType::ALL_SENSOR_TYPE_DATA);
            if ($cardData instanceof SensorTypeInterface) {
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

        return $cardViewFormDTO ?? null;
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
