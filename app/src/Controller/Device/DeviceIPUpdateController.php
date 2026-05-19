<?php

declare(strict_types=1);

namespace App\Controller\Device;

use App\DTOs\Device\Request\DeviceIpUpdateRequestDTO;
use App\Entity\Device\Devices;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'device-ip-update')]
class DeviceIPUpdateController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route(
        path: 'ipupdate',
        name: 'device-ip-update-endpoint',
        methods: [Request::METHOD_PUT, Request::METHOD_POST]
    )]
    #[IsGranted(DeviceVoter::DEVICE_UPDATE_IP)]
    public function updateDeviceIpAddress(
        ValidatorInterface $validator,
        DeviceRepositoryInterface $deviceRepository,
        LoggerInterface $logger,
        #[MapRequestPayload]
        DeviceIpUpdateRequestDTO $deviceIpUpdateRequestDTO,
    ): JsonResponse {
        $device = $this->getUser();
        if (!$device instanceof Devices) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }

        $errors = $validator->validate($deviceIpUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($errors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($errors));
        }

        $device->setIpAddress($deviceIpUpdateRequestDTO->getIpAddress());

        try {
            $deviceRepository->persist($device);
            $deviceRepository->flush();
        } catch (ORMException $e) {
            $logger->error(
                'Failed to update device IP address',
                [
                    'device' => $device->getUserIdentifier(),
                    'error' => $e->getMessage(),
                ]
            );

            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Updating device IP address')]);
        }

        $logger->info(
            sprintf('Device %s updated its IP address to %s', $device->getDeviceName(), $deviceIpUpdateRequestDTO->getIpAddress()),
            ['device' => $device->getUserIdentifier()]
        );

        return $this->sendSuccessfulJsonResponse([], 'Device IP address updated successfully');
    }
}
