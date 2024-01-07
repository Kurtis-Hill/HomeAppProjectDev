<?php
declare(strict_types=1);

namespace App\Common\Services;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\DTO\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use Exception;
use HttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DeviceRequestHandler implements DeviceRequestHandlerInterface
{
    use HomeAppAPITrait;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $elasticLogger,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws Exception
     * @throws TransportExceptionInterface
     * @throws HttpException
     */
    public function handleDeviceRequest(
        DeviceRequestEncapsulationDTO $deviceRequestEncapsulationDTO,
        array $groups = []
    ): ResponseInterface {
        $normalizedResponse = $this->normalizeResponse(
            $deviceRequestEncapsulationDTO->getDeviceRequestDTO(),
            $groups,
        );
        $this->elasticLogger->info(
            'Sending request to device',
            [
                'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                'request' => $normalizedResponse,
            ]);

        try {
            return $this->httpClient->request(
                Request::METHOD_POST,
                $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                [
                    'headers' =>
                        [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                    'json' => $normalizedResponse,
                ]
            );
        } catch (Exception $e) {
            $this->elasticLogger->error(
                'Sending request to device failed',
                [
                    'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                    'request' => $normalizedResponse,
                    'exception' => $e->getMessage(),
                ]
            );
            throw $e;
        } catch (TransportExceptionInterface $e) {
            $this->elasticLogger->error(
                'Sending request to device failed',
                [
                    'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                    'request' => $normalizedResponse,
                    'exception' => $e->getMessage(),
                ]
            );
            throw $e;
        }
    }
}
