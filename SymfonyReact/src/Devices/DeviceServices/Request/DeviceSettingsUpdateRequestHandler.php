<?php

namespace App\Devices\DeviceServices\Request;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Devices\Builders\Request\DeviceLoginCredentialsUpdateRequestDTOBuilder;
use App\Devices\DTO\Internal\DeviceSettingsUpdateDTO;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Exceptions\DeviceNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Response;

readonly class DeviceSettingsUpdateRequestHandler
{
    private const SETTINGS_ENDPOINT = 'settings';

    public const PASSWORD_PRESENT = 'password_present';

    public const PASSWORD_NOT_PRESENT = 'password_not_present';

    public function __construct(
        private DeviceRequestHandler $deviceRequestHandler,
        private DeviceRepositoryInterface $deviceRepository,
        private DeviceSettingsRequestDTOBuilder $deviceSettingsRequestDTOBuilder,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws DeviceNotFoundException
     * @throws DeviceIPNotSetException
     */
    public function handleDeviceSettingsUpdateRequest(DeviceSettingsUpdateDTO $deviceSettingsUpdateDTO): bool
    {
        $device = $this->deviceRepository->find($deviceSettingsUpdateDTO->getDeviceID());
        if ($device === null) {
            throw new DeviceNotFoundException();
        }

        $deviceLoginCredentialsUpdateRequestDTO = DeviceLoginCredentialsUpdateRequestDTOBuilder::buildLoginCredentialsUpdateRequestDTO(
            $deviceSettingsUpdateDTO->getUsername(),
            $deviceSettingsUpdateDTO->getPassword(),
        );

        $deviceSettingsRequestDTO = $this->deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
        );
        $deviceEncapsulationRequestDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $deviceSettingsRequestDTO,
            self::SETTINGS_ENDPOINT
        );

        $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
            $deviceEncapsulationRequestDTO,
            $deviceSettingsUpdateDTO->getPassword() !== null
                ? [self::PASSWORD_PRESENT, DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS]
                : [self::PASSWORD_NOT_PRESENT, DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS]
        );

        return $deviceResponse->getStatusCode() === Response::HTTP_OK;
    }
}
