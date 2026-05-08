<?php

declare(strict_types=1);

namespace App\Command\Sensor;

use App\Builders\Sensor\Internal\SensorBuilder;
use App\DTOs\Sensor\Request\AddNewSensorRequestDTO;
use App\Repository\User\ORM\UserRepository;
use App\Services\Sensor\NewReadingType\ReadingTypeCreationHandler;
use App\Services\Sensor\NewSensor\SensorSavingHandler;
use App\Services\Sensor\SensorDeletion\SensorDeletionHandler;
use App\Services\UserInterface\Cards\CardCreation\CardCreationHandler;
use App\Traits\ValidatorProcessorTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'sensor:create',
    description: 'Create a sensor',
    aliases: ['app:sensor:create'],
    hidden: false,
)]
class CreateSensorCommand extends Command
{
    use ValidatorProcessorTrait;

    public function __construct(
        private readonly CardCreationHandler $cardCreationService,
        private readonly ReadingTypeCreationHandler $readingTypeCreationHandler,
        private readonly SensorDeletionHandler $deleteSensorService,
        private readonly SensorBuilder $sensorBuilder,
        private readonly SensorSavingHandler $newSensorSavingHandler,
        private readonly UserRepository $userRepository,
        private readonly ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('sensorName',InputArgument::REQUIRED, 'The sensor name to which the sensor belongs');
        $this->addArgument('deviceID', InputArgument::REQUIRED, 'The ID of the device to which the sensor belongs');
        $this->addArgument('sensorType',InputArgument::REQUIRED, 'The type of the sensor to create');
        $this->addArgument('pinNumber', InputArgument::REQUIRED, 'The pinNumber to add');
        $this->addArgument('readingInterval', InputArgument::REQUIRED, 'The pinNumber to add');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $newSensorRequestDTO = new AddNewSensorRequestDTO();
        $newSensorRequestDTO->setDeviceID((int)$input->getArgument('deviceID'));
        $newSensorRequestDTO->setSensorName($input->getArgument('sensorName'));
        $newSensorRequestDTO->setSensorTypeID((int) $input->getArgument('sensorType'));
        $newSensorRequestDTO->setPinNumber((int)$input->getArgument('pinNumber'));
        $newSensorRequestDTO->setReadingInterval((int)$input->getArgument('readingInterval'));

        $errors = $this->validator->validate($newSensorRequestDTO);
        if ($this->checkIfErrorsArePresent($errors)) {
            return Command::FAILURE;
        }

        $adminUser = $this->userRepository->findOneBy(['email' => 'admin']);
        if (!$adminUser instanceof UserInterface) {
            $output->writeln("<error>Could not find user</error>");
        }
        $newSensor = $this->sensorBuilder->buildNewSensor(
            sensorName: $newSensorRequestDTO->getSensorName(),
            sensorTypeID: $newSensorRequestDTO->getSensorTypeID(),
            deviceID: $newSensorRequestDTO->getDeviceID(),
            createdByID: $adminUser->getUserID(),
            pinNumber: $newSensorRequestDTO->getPinNumber(),
            readingInterval: $newSensorRequestDTO->getReadingInterval(),
        );

        $saveSensor = $this->newSensorSavingHandler->saveSensor($newSensor, false);
        if ($saveSensor !== true) {
            $output->writeln("<error>Could not save sensor</error>");
            return Command::FAILURE;
        }

        $sensorReadingTypesCreated = $this->readingTypeCreationHandler->handleSensorReadingTypeCreation($newSensor);
        foreach ($sensorReadingTypesCreated as $sensorReadingType) {
            $readingTypeValidationErrors = $this->validator->validate(value: $sensorReadingType, groups: [$newSensor->getSensorTypeObject()::getSensorTypeName()]);
            if ($this->checkIfErrorsArePresent($readingTypeValidationErrors)) {
                $this->deleteSensorService->deleteSensor(sensor: $newSensor, triggerESPUpdate: false);
                $output->writeln("<error>Sensor deleted</error>");

                return Command::FAILURE;
            }
        }

        $errors = $this->cardCreationService->createUserCardForSensor($newSensor, $adminUser);
        if (!empty($errors)) {
            $output->writeln("<error>Could not create cards for user</error>");
        }

        return Command::SUCCESS;
    }
}
