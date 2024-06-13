<?php
declare(strict_types=1);

namespace App\Services\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use Exception;
use HttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface DeviceRequestHandlerInterface
{
    /**
    * @throws ExceptionInterface
    * @throws Exception
    * @throws TransportExceptionInterface
    * @throws HttpException
    */
    public function handleDeviceRequest(DeviceRequestEncapsulationDTO $deviceRequestEncapsulationDTO, array $groups = []): ResponseInterface;
}
