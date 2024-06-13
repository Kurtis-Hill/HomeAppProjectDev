<?php

namespace App\Controller\User\UserControllers;

use App\Builders\User\User\UserResponseBuilder;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

#[Route(CommonURL::USER_HOMEAPP_API_URL )]
class GetSingleUserController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('{user}/get', name: 'get-single-user', methods: [Request::METHOD_GET])]
    public function getSingleUser(User $user, Request $request, UserResponseBuilder $userResponseBuilder): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(UserVoter::CAN_GET_USER, $user);
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::SENSITIVE_FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $userResponse = $userResponseBuilder->buildFullUserResponseDTO($user);
        try {
            $normalizedDTO = $this->normalize($userResponse, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], 'Get User');
        }

        return $this->sendSuccessfulJsonResponse($normalizedDTO);
    }
}
