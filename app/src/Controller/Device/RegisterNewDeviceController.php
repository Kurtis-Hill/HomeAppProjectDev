<?php
declare(strict_types=1);

namespace App\Controller\Device;

use App\DTOs\IPLog\Request\IPLogRequestDTO;
use App\Entity\Common\IPLog;
use App\Repository\Common\ORM\IPLogRepository;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterNewDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    use ValidatorProcessorTrait;

    #[Route(CommonURL::DEVICE_HOMEAPP_API_URL . 'register', name: 'register-new-device', methods: [Request::METHOD_POST])]
    public function registerNewDevice(
        Request $request,
        ValidatorInterface $validator,
        IPLogRepository $ipLogRepository,
        DeviceRepositoryInterface $deviceRepository,
    ): JsonResponse {
        $ipRequestDTO = new IPLogRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                IPLogRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $ipRequestDTO],
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($ipRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }
        $device = $deviceRepository->findOneBy(['ipAddress' => $ipRequestDTO->getIpAddress()]);
        if ($device !== null) {
            return $this->sendSuccessfulJsonResponse(['Device already registered']);
        }
        $ipExist = $ipLogRepository->findOneBy(['ipAddress' => $ipRequestDTO->getIpAddress()]);
        if ($ipExist) {
            return $this->sendSuccessfulJsonResponse(['Device already registered']);
        }

        $newIpLog = new IPLog();
        $newIpLog->setIpAddress($ipRequestDTO->getIpAddress());

        $ipLogRepository->persist($newIpLog);
        $ipLogRepository->flush();

        return $this->sendSuccessfulJsonResponse([], 'Device registered successfully');
    }
}
