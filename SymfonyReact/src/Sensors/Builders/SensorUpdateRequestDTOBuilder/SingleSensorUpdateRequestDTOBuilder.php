<?php

namespace App\Sensors\Builders\SensorUpdateRequestDTOBuilder;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;

readonly class SingleSensorUpdateRequestDTOBuilder implements SensorUpdateRequestDTOBuilderInterface
{
    public function __construct(
        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory
    ) {}

    /**
     * @throws SensorTypeException
     */
    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface
    {
        $sensorType = $sensor->getSensorTypeObject()->getSensorType();
        $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);

        $pinNumber = $sensor->getPinNumber();
        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
            $pinNumber = "A$pinNumber";
        }

        return new SingleSensorUpdateRequestDTO(
            $sensor->getSensorName(),
            $pinNumber,
            $sensor->getReadingInterval()
        );
    }
}
