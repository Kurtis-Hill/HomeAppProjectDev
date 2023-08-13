<?php

namespace App\Devices\DeviceServices\Request;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceSettingsUpdateRequestBuilder;
use App\Devices\DTO\Internal\DeviceSettingsUpdateDTO;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Exceptions\DeviceNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

readonly class DeviceSettingsUpdateRequestHandler
{
    use HomeAppAPITrait;
    private const SETTINGS_ENDPOINT = '/settings';

    public const PASSWORD_PRESENT = 'password_present';

    public const PASSWORD_NOT_PRESENT = 'password_not_present';

    public function __construct(
        private DeviceRequestHandler $deviceRequestHandler,
        private DeviceRepositoryInterface $deviceRepository,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws DeviceNotFoundException
     */
    public function handleDeviceSettingsUpdateRequest(DeviceSettingsUpdateDTO $deviceSettingsUpdateDTO): bool
    {
        $device = $this->deviceRepository->find($deviceSettingsUpdateDTO->getDeviceId());
        if ($device === null) {
            throw new DeviceNotFoundException();
        }

        $deviceRequestDTO = DeviceSettingsUpdateRequestBuilder::buildDeviceSettingsUpdateRequestDTO(
            $deviceSettingsUpdateDTO->getUsername(),
            $deviceSettingsUpdateDTO->getPassword(),
        );

        $deviceEncapsulationRequestDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device->getIpAddress(),
            $deviceRequestDTO,
            self::SETTINGS_ENDPOINT
        );

        $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
            $deviceEncapsulationRequestDTO,
            $deviceSettingsUpdateDTO->getPassword() !== null
                ? [self::PASSWORD_PRESENT]
                : [self::PASSWORD_NOT_PRESENT]
        );

        return $deviceResponse->getStatusCode() === Response::HTTP_OK;
    }
}
