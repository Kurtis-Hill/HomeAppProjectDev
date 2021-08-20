<?php


namespace App\Services\CardServices;


use App\DTOs\CardDTOs\Factories\CardFactories\CardViewDTOFactory;
use App\Entity\Card\CardView;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CardDataRequestFilterService
{
    /**
     * @var CardViewDTOFactory
     */
    private CardViewDTOFactory $cardViewDTOFactory;

    public function __construct(CardViewDTOFactory $cardViewDTOFactory)
    {
        $this->cardViewDTOFactory = $cardViewDTOFactory;
    }

    /**
     * @param string|null $route
     * @param int|null $deviceId
     * @return array
     */
    public function prepareCardDTOs(?string $route = null, ?int $deviceId = null, array $cardFilters = []): array
    {
        try {
            if (isset($deviceId) && !is_numeric($deviceId)) {
                throw new BadRequestException('device id is not one that can be processed');
            }

            $sensorObjects = match ($route) {
                "room" => $this->getRoomCardDataObjects($deviceId),
                "device" => $this->getDevicePageCardDataObjects($deviceId),
                default => $this->getIndexPageCardDataObjects()
            };
        } catch (BadRequestException $e) {
            $this->userInputErrors[] = $e->getMessage();
        } catch (ORMException $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = 'Card Data Query Failure';
        }

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
}
