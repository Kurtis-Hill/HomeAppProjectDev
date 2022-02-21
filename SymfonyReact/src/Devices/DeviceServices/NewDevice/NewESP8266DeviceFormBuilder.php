<?php


namespace App\Devices\DeviceServices\NewDevice;

use App\API\Traits\FormProcessorTrait;
use App\Devices\DTO\NewDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Forms\AddNewDeviceForm;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

#[Deprecated]
class NewESP8266DeviceFormBuilder implements NewDeviceBuilderInterface
{
    use FormProcessorTrait;

    private FormFactoryInterface $formFactory;

    private DeviceRepositoryInterface $deviceRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->deviceRepository = $deviceRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices
    {
        $newDevice = new Devices();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceDTO->getCreatedByUserObject());
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($deviceDTO->getRoomObject());

        return $newDevice;
    }

    public function validateNewDevice(Devices $newDevice): array
    {
        $this->processNewDeviceForm($newDevice);

        if (!empty($this->getAllFormInputErrors())) {
            return $this->getAllFormInputErrors();
        }

        $errors = [];
        try {
            $this->duplicateDeviceCheck($newDevice);
        }
        catch (DuplicateDeviceException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    private function duplicateDeviceCheck(Devices $deviceData): void
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceData->getDeviceName(),
            $deviceData->getRoomObject()->getRoomId(),
        );

        if ($currentUserDeviceCheck instanceof Devices) {
            throw new DuplicateDeviceException(
                sprintf(
                    DuplicateDeviceException::MESSAGE,
                    $currentUserDeviceCheck->getDeviceName(),
                    $currentUserDeviceCheck->getRoomObject()->getRoom()
                )
            );
        }
    }

    private function processNewDeviceForm(Devices $device): array
    {
        $addNewDeviceForm = $this->formFactory->create(AddNewDeviceForm::class);

        $addNewDeviceForm->submit([
            'deviceName' => $device->getDeviceName(),
            'groupNameObject' => $device->getGroupNameObject(),
            'roomObject' => $device->getRoomObject(),
        ]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $devicePasswordHash = $this->createDevicePasswordHash($device);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($devicePasswordHash);
            $validFormData->setRoles([Devices::ROLE]);

            return [];
        }

        return $this->processFormErrors($addNewDeviceForm);
    }

    private function createDevicePasswordHash(Devices $device): string
    {
        $secret = $device->getDeviceName();
        $secret .= time();

        return hash("md5", $secret);
    }

    public function encodeAndSaveNewDevice(Devices $newDevice): bool
    {
        $this->encodeDevicePassword($newDevice);
        try {
            $this->deviceRepository->persist($newDevice);
            $this->deviceRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }

    private function encodeDevicePassword(Devices $device): void
    {
        $device->setPassword(
            $this->passwordEncoder->encodePassword(
                $device,
                $device->getDeviceSecret()
            )
        );
    }

    public function validateDeviceRequestObject(DeviceRequestDTOInterface $deviceRequestDTO): array
    {
        // TODO: Implement validateDeviceRequestObject() method.
    }

    public function saveNewDevice(Devices $device): bool
    {
        // TODO: Implement saveNewDevice() method.
    }
}
