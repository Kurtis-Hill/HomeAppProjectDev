<?php


namespace App\Services\CardServices;


class CardDataFilterService
{
    public function filterSensorTypes(array $sensorTypes, array $cardFilters): array
    {
        if (!empty($cardFilters['sensorTypes'])) {
            $sensorTypes = $this->filterSensorByType($sensorTypes, $cardFilters['types']);
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
}
