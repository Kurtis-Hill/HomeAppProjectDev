<?php

namespace App\Builders\UserInterface\CardViewSensorTypeBuilders;

use App\DTOs\UserInterface\Response\CardForms\Boundary\BoolSensorTypeBoundaryViewFormDTO;
use App\DTOs\UserInterface\Response\CardForms\Boundary\StandardSensorTypeBoundaryViewFormDTO;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\CardViewReadingResponseDTOInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

readonly abstract class AbstractCardDTOBuilder
{
    public function __construct(
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory
    ) {
    }

    /**
     * @throws \App\Exceptions\Sensor\SensorTypeNotFoundException
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     */
    #[ArrayShape([CardViewReadingResponseDTOInterface::class])]
    public function formatSensorTypeObjectsByReadingType(SensorTypeInterface $cardDTOData, Sensor $sensor): array
    {
        if ($cardDTOData instanceof TemperatureReadingTypeInterface) {
            $temperatureRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Temperature::getReadingTypeName());
            $temperatures = $temperatureRepository->findBySensorID($sensor->getSensorID());
            foreach ($temperatures as $temperature) {
                if (!$temperature instanceof StandardReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setStandardSensorData($temperature, Temperature::getReadingTypeName(), Temperature::READING_SYMBOL);
            }
        }
        if ($cardDTOData instanceof HumidityReadingTypeInterface) {
            $humidityRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Humidity::getReadingTypeName());
            $humidities = $humidityRepository->findBySensorID($sensor->getSensorID());
            foreach ($humidities as $humidity) {
                if (!$humidity instanceof StandardReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setStandardSensorData($humidity, Humidity::getReadingTypeName(), Humidity::READING_SYMBOL);
            }
        }
        if ($cardDTOData instanceof LatitudeReadingTypeInterface) {
            $latitudeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Latitude::getReadingTypeName());
            $latitudes = $latitudeRepository->findBySensorID($sensor->getSensorID());
            foreach ($latitudes as $latitude) {
                if (!$latitude instanceof StandardReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setStandardSensorData($latitude, Latitude::getReadingTypeName());
            }
        }
        if ($cardDTOData instanceof AnalogReadingTypeInterface) {
            $analogRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Analog::getReadingTypeName());
            $analogs = $analogRepository->findBySensorID($sensor->getSensorID());
            foreach ($analogs as $analog) {
                if (!$analog instanceof StandardReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setStandardSensorData($analog, Analog::getReadingTypeName());
            }
        }
        if ($cardDTOData instanceof RelayReadingTypeInterface) {
            $relayRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Relay::getReadingTypeName());
            $relays = $relayRepository->findBySensorID($sensor->getSensorID());
            foreach ($relays as $relay) {
                if (!$relay instanceof BoolReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setBoolSensorData($relay, Relay::getReadingTypeName());
            }
        }
        if ($cardDTOData instanceof MotionSensorReadingTypeInterface) {
            $motionRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Motion::getReadingTypeName());
            $motions = $motionRepository->findBySensorID($sensor->getSensorID());
            foreach ($motions as $motion) {
                if (!$motion instanceof BoolReadingSensorInterface) {
                    throw new SensorTypeNotFoundException('Sensor Type Not Found');
                }
                $sensorData[] = $this->setBoolSensorData($motion, Motion::getReadingTypeName());
            }
        }

        if (empty($sensorData)) {
            throw new SensorTypeNotFoundException('Sensor Type Not Found');
        }

        return $sensorData;
    }

    private function setStandardSensorData(
        StandardReadingSensorInterface $sensorTypeObject,
        string $type,
        string $symbol = null
    ): StandardSensorTypeBoundaryViewFormDTO {
        return new StandardSensorTypeBoundaryViewFormDTO(
            $type,
            $sensorTypeObject->getHighReading(),
            $sensorTypeObject->getLowReading(),
            $sensorTypeObject->getConstRecord(),
            $symbol
        );
    }

    private function setBoolSensorData(
        BoolReadingSensorInterface $sensorTyeObject,
        string $type,
        string $symbol = null,
    ): BoolSensorTypeBoundaryViewFormDTO {
        return new BoolSensorTypeBoundaryViewFormDTO(
            $type,
            $sensorTyeObject->getConstRecord(),
            $sensorTyeObject->getExpectedReading(),
            $symbol
        );
    }
}
