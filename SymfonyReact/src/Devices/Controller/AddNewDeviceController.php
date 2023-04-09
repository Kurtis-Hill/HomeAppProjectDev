<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceServiceInterface;
use App\Devices\DeviceServices\NewDevice\NewDeviceHandlerInterface;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\User;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'add-new-user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('/add', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        ValidatorInterface $validator,
        NewDeviceHandlerInterface $newDeviceHandler,
        DeleteDeviceServiceInterface $deleteDeviceHandler,
    ): JsonResponse {
        $newDeviceRequestDTO = new NewDeviceRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewDeviceRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newDeviceRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([], APIErrorMessages::FORMAT_NOT_SUPPORTED);
        }

        $requestValidationErrors = $validator->validate($newDeviceRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
//            dd($requestValidationErrors);
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $newDeviceCheckDTO = $newDeviceHandler->findObjectNeededForNewDevice($newDeviceRequestDTO, $user);
        } catch (GroupNameNotFoundException|RoomNotFoundException $e) {
            return $this->sendNotFoundResponse([$e->getMessage()]);
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage());

            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Device')]);
        }

        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $newDeviceCheckDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $errors = $newDeviceHandler->processNewDevice($newDeviceCheckDTO);
        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $device = $newDeviceCheckDTO->getNewDevice();
        $deviceSaved = $newDeviceHandler->saveDevice($device);
        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::FAILED_TO_SAVE_OBJECT, 'device')]);
        }

        $newDeviceResponseDTO = DeviceResponseDTOBuilder::buildDeviceIDResponseDTO($device, true);
        try {
            $response = $this->normalizeResponse($newDeviceResponseDTO);
        } catch (ExceptionInterface $e) {
            $deleteDeviceHandler->deleteDevice($device);
            $this->logger->error($e, $e->getTrace());

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }
        $this->logger->info('new device created with id: ' . $device->getDeviceID(), ['user' => $user->getUserIdentifier()]);

        return $this->sendSuccessfulJsonResponse($response);
    }
}
