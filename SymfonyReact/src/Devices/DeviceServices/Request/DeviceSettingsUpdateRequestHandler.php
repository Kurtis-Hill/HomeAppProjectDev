<?php

namespace App\Devices\DeviceServices\Request;

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
    private const SETTINGS_ENDPOINT = '/settings';

    public const WIFI_PASSWORD_GROUP = 'wifi_password';

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
            $deviceSettingsUpdateDTO->getPassword() !== null ? [self::WIFI_PASSWORD_GROUP] : []
        );

        return $deviceResponse->getStatusCode() === Response::HTTP_OK;
    }
}
