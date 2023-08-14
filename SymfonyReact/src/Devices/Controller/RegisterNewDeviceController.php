<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\DTO\Request\IPLog\IPLogRequestDTO;
use App\Common\Entity\IPLog;
use App\Common\Repository\IPLogRepository;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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

        $newIpLog = new IPLog();
        $newIpLog->setIpAddress($ipRequestDTO->getIpAddress());

        $ipLogRepository->persist($newIpLog);
        $ipLogRepository->flush();

        return $this->sendSuccessfulJsonResponse([], 'Device registered successfully');
    }
}
