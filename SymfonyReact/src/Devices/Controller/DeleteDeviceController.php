<?php
declare(strict_types=1);

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceServiceInterface;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/', name: 'delete-user-devices')]
class DeleteDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    /**
     * @throws RoomNotFoundException
     * @throws GroupNotFoundException
     */
    #[
        Route(
            path: '{deviceID}/delete',
            name: 'delete-esp-device',
            methods: [Request::METHOD_DELETE]
        )
    ]
    public function deleteDevice(
        Devices $deviceToDelete,
        Request $request,
        DeleteDeviceServiceInterface $deleteDeviceBuilder,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::DELETE_DEVICE, $deviceToDelete);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $deviceDeletedID = $deviceToDelete->getDeviceID();
        $deviceDeleteSuccess = $deleteDeviceBuilder->deleteDevice($deviceToDelete);
        if ($deviceDeleteSuccess !== true) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'Delete device'));

            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::FAILURE, 'Delete device')]);
        }

        $deviceToDelete->setDeviceID($deviceDeletedID);
        $deviceDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTOWithDevicePermissions($deviceToDelete);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $normalizedResponse = null;
        }

        $this->logger->info('device deleted successfully id: ' . $deviceDeletedID, ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
