<?php

declare(strict_types=1);

namespace App\Command\Sensor;

use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use App\Services\Sensor\SensorDeletion\SensorDeletionEventHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:delete-sensor-type',
    description: 'Deletes a sensor type and all associated sensors and data.',
    aliases: ['app:delete-sensor'],
    hidden: false
)]
class DeleteSensorCommand extends Command
{
    public function __construct(
        private SensorDeletionEventHandler $sensorDeletionEventHandler,
        private DeviceRepository $deviceRepository,
        private SensorRepository $sensorRepository,
        private SensorTypeRepository $sensorTypeRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('sensorTypeID', InputArgument::REQUIRED, 'The sensor type to delete')
            ->addArgument('deviceID', InputArgument::REQUIRED, 'The Device ID to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $device = $input->getArgument('deviceID');
        $sensorType = $input->getArgument('sensorTypeID');

        $deviceObject = $this->deviceRepository->find($device);
        $sensorTypeObject = $this->sensorTypeRepository->findOneBy(['sensorTypeID' => $sensorType]);

        $sensors = $this->sensorRepository->findSameSensorTypesOnSameDevice(
            deviceId: $deviceObject->getDeviceID(),
            sensorType: $sensorTypeObject->getSensorTypeID(),
        );
        foreach ($sensors as $sensor) {
            $this->sensorRepository->remove($sensor);
        }
        $this->sensorRepository->flush();

        $this->sensorDeletionEventHandler->handleSensorDeletionEvent($sensorType, $deviceObject->getDeviceID());

        return Command::SUCCESS;
    }
}
