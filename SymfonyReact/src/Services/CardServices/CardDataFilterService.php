<?php


namespace App\Services\CardServices;


use App\DTOs\CardDTOs\Factories\CardFactories\CardViewDTOFactory;
use App\Entity\Card\Cardstate;
use App\Entity\Card\CardView;
use App\Entity\Sensors\SensorType;
use App\Repository\Card\CardViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CardDataFilterService
{
    public function filterSensorTypes(array $sensorTypes, array $cardFilters): array
    {
        if (!empty($cardFilters['sensorTypes'])) {
            $sensorTypes = $this->filterSensorByType($sensorTypes, $cardFilters['types']);
        }
        if (!empty($cardFilters['readingTypes'])) {
            $sensorTypes = $this->filterSensorByReadingType($sensorTypes, $cardFilters);
        }

        return $sensorTypes;
    }

    private function filterSensorByType(array $sensorTypes, array $cardFilters = []): array
    {
        return array_filter($sensorTypes, static function ($sensorType) use ($cardFilters) {
            return (!in_array($sensorType, $cardFilters, true))
                ?  $sensorType
                : false;
        });
    }

    private function filterSensorByReadingType(array $sensorTypes, array $cardFilters): array
    {

    }

}
