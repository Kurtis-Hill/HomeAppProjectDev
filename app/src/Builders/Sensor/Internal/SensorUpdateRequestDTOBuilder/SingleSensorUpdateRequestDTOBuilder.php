<?php

namespace App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder;

use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\SensorTypeException;

readonly class SingleSensorUpdateRequestDTOBuilder implements SensorUpdateRequestDTOBuilderInterface
{
    public function __construct(
//        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory
    ) {}

    /**
     * @throws SensorTypeException
     */
    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface
    {
//        $sensorType = $sensor->getSensorTypeObject()->getSensorType();
//        $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensorType);
//        $sensorTypeObject = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);
//
//        $pinNumber = $sensor->getPinNumber();
//        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
//            $pinNumber = "A$pinNumber";
//        }

        return new SingleSensorUpdateRequestDTO(
            $sensor->getSensorName(),
            $sensor->getPinNumber(),
            $sensor->getReadingInterval()
        );
    }
}
