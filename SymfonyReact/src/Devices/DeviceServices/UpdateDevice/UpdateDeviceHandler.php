<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Common\API\APIErrorMessages;
use App\Devices\DeviceServices\AbstractESPDeviceHandler;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Response\DeviceUpdateResponseDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateDeviceHandler extends AbstractESPDeviceHandler implements UpdateDeviceHandlerInterface
{
    private DevicePasswordEncoderInterface $devicePasswordEncoder;

    #[Pure]
    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
    ) {
        $this->devicePasswordEncoder = $devicePasswordEncoder;
        parent::__construct($deviceRepository, $validator);
    }

    public function updateDeviceAndValidate(UpdateDeviceDTO $deviceUpdateRequestDTO): array
    {
        $deviceToUpdate = $deviceUpdateRequestDTO->getDeviceToUpdate();
        if (!empty($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getDeviceName())) {
            $deviceToUpdate->setDeviceName($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getDeviceName());
        }

        if ($deviceUpdateRequestDTO->getProposedUpdatedRoom() !== null) {
            $deviceToUpdate->setRoomObject($deviceUpdateRequestDTO->getProposedUpdatedRoom());
        }
        try {
            $this->duplicateDeviceCheck($deviceToUpdate->getDeviceName(), $deviceToUpdate->getRoomObject()->getRoomID());
        } catch (DuplicateDeviceException $e) {
            $errors[] = $e->getMessage();
        } catch (ORMException) {
            $errors[] = sprintf(APIErrorMessages::QUERY_FAILURE, 'Device');
        }

        if (!empty($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo() !== null)) {
            $deviceToUpdate->setGroupNameObject($deviceUpdateRequestDTO->getProposedGroupNameToUpdateTo());
        }

        if (!empty($deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getPassword())) {
            $deviceToUpdate->setDeviceSecret(
                $deviceUpdateRequestDTO->getDeviceUpdateRequestDTO()->getPassword()
            );
            $this->devicePasswordEncoder->encodeDevicePassword($deviceToUpdate);
        }

        $validationConstraintList = $this->validator->validate($deviceToUpdate);

        if (isset($errors) && $this->checkIfErrorsArePresent($validationConstraintList)) {
            return array_merge($errors, $this->getValidationErrorAsArray($validationConstraintList));
        }
        return $this->getValidationErrorAsArray($validationConstraintList);
    }
}
