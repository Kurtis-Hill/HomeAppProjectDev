<?php

namespace App\Sensors\Builders\MessageDTOBuilders;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\User\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class UpdateSensorCurrentReadingDTOBuilder
{
    public function __construct(private readonly Security $security) {}

    public static function buildUpdateSensorCurrentReadingConsumerMessageDTO(
        string $sensorType,
        string $sensorName,
        array $readingTypeCurrentReadingDTOs,
        int $deviceID,
    ): UpdateSensorCurrentReadingMessageDTO {
        return new UpdateSensorCurrentReadingMessageDTO(
            $sensorType,
            $sensorName,
            $readingTypeCurrentReadingDTOs,
            $deviceID
        );
    }

    public function buildSensorSwitchRequestConsumerMessageDTO(
        int $sensorID,
        BoolCurrentReadingUpdateRequestDTO $readingTypeCurrentReadingDTO,
    ): RequestSensorCurrentReadingUpdateMessageDTO {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $userType = User::USER_TYPE;
        } elseif ($user instanceof Devices) {
            $userType = Devices::USER_TYPE;
        } else {
            throw new \Exception('User type not found');
        }

        return new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $readingTypeCurrentReadingDTO,
            $userType,
            $user->getUserID()
        );
    }
}
