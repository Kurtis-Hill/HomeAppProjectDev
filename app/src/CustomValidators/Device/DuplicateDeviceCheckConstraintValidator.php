<?php

namespace App\CustomValidators\Device;

use App\DTOs\Device\Request\DeviceUpdateInterface;
use App\Repository\User\ORM\RoomRepository;
use App\Services\Device\DuplicateDeviceChecker;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DuplicateDeviceCheckConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly DuplicateDeviceChecker $duplicateDeviceChecker,
        private readonly RoomRepository $roomRepository,
    ) {
    }

    public function validate($value, $constraint)
    {
        if (!$constraint instanceof DuplicateDeviceCheckConstraint) {
            throw new UnexpectedTypeException($constraint, DuplicateDeviceCheckConstraint::class);
        }

        if (
            !$value instanceof DeviceUpdateInterface
            || $value->getDeviceRoom() === null
            || $value->getDeviceName() === null
        ) {
            return;
        }

        if ($this->duplicateDeviceChecker->duplicateDeviceCheck(
            $value->getDeviceName(),
            $value->getDeviceRoom(),
        )) {
            $room = $this->roomRepository->find($value->getDeviceRoom());
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value->getDeviceName())
                ->setParameter('{{ room }}', $room->getRoom())
                ->addViolation();
        }
    }
}
