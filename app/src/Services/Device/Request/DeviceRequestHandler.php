<?php
declare(strict_types=1);

namespace App\Services\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use App\Traits\HomeAppAPITrait;
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
        $normalizedRequest = $this->normalize(
            $deviceRequestEncapsulationDTO->getDeviceRequestDTO(),
            $groups,
        );
        $this->elasticLogger->info(
            'Sending request to device',
            [
                'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                'request' => $normalizedRequest,
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
                    'json' => $normalizedRequest,
                ]
            );
        } catch (Exception $e) {
            $this->elasticLogger->error(
                'Sending request to device failed',
                [
                    'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                    'request' => $normalizedRequest,
                    'exception' => $e->getMessage(),
                ]
            );
            throw $e;
        } catch (TransportExceptionInterface $e) {
            $this->elasticLogger->error(
                'Sending request to device failed',
                [
                    'device' => $deviceRequestEncapsulationDTO->getFullDeviceUrl(),
                    'request' => $normalizedRequest,
                    'exception' => $e->getMessage(),
                ]
            );
            throw $e;
        }
    }
}
