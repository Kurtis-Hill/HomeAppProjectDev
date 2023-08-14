<?php

namespace App\Common\Services;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\DTO\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DeviceRequestHandler implements DeviceRequestHandlerInterface
{
    use HomeAppAPITrait;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    public function handleDeviceRequest(
        DeviceRequestEncapsulationDTO $deviceRequestEncapsulationDTO,
        array $groups = []
    ): ResponseInterface {
        $normalizedResponse = $this->normalizeResponse(
            $deviceRequestEncapsulationDTO->getDeviceRequestDTO(),
            $groups,
        );

//        dd($normalizedResponse);
        return $this->httpClient->request(
            Request::METHOD_POST,
            $deviceRequestEncapsulationDTO->getFullSensorUrl(),
            [
                'headers' =>
                    [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                'json' => $normalizedResponse,
            ]
        );
    }
}
