<?php
declare(strict_types=1);

namespace App\Controller\Device;

use App\Builders\Device\DeviceResponse\DeviceResponseDTOBuilder;
use App\DTOs\RequestDTO;
use App\Entity\Device\Devices;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\DeleteDevice\DeleteDeviceServiceInterface;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Voters\DeviceVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
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
            path: '{deviceID}',
            name: 'delete-esp-device',
            methods: [Request::METHOD_DELETE]
        )
    ]
    public function deleteDevice(
        Devices $deviceToDelete,
        DeleteDeviceServiceInterface $deleteDeviceBuilder,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::DELETE_DEVICE, $deviceToDelete);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
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
            $normalizedResponse = $this->normalize($deviceDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $normalizedResponse = null;
        }

        $this->logger->info('device deleted successfully id: ' . $deviceDeletedID, ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
