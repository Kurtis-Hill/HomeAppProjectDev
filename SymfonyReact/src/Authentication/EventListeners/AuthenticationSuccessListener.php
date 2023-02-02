<?php

namespace App\Authentication\EventListeners;

use App\Authentication\DTOs\Request\DeviceAuthenticationIPRequestDTO;
use App\Authentication\DTOs\Response\DeviceAuthenticationResponse;
use App\Authentication\DTOs\Response\UserAuthenticationResponseDTO;
use App\Authentication\DTOs\Response\UserDataDTO;
use App\Common\API\APIErrorMessages;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthenticationSuccessListener
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private RequestStack $requestStack;

    private DeviceRepositoryInterface $deviceRepository;

    private ValidatorInterface $validator;

    private LoggerInterface $logger;

    public function __construct(
        RequestStack $requestStack,
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        LoggerInterface $elasticLogger,
    ) {
        $this->requestStack = $requestStack;
        $this->deviceRepository = $deviceRepository;
        $this->validator = $validator;
        $this->logger = $elasticLogger;
    }


    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        $user = $authenticationSuccessEvent->getUser();
        $data = $authenticationSuccessEvent->getData();

        if ($user instanceof User) {
            $userDataDTO = new UserDataDTO(
                $user->getUserID(),
                $user->getRoles(),
            );
            $userAuthenticationDTO = new UserAuthenticationResponseDTO(
                $userDataDTO,
                $data['token'],
            );

            try {
                $responseMessage = $this->normalizeResponse($userAuthenticationDTO);
            } catch (ExceptionInterface) {
                $responseMessage['error'] = sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'UserExceptions authentication ');
            }

            $authenticationSuccessEvent->setData($responseMessage);
        }
        if ($user instanceof Devices) {
            $deviceIpRequestDTO = new DeviceAuthenticationIPRequestDTO();
            try {
                $this->deserializeRequest(
                    $this->requestStack->getCurrentRequest()?->getContent(),
                    DeviceAuthenticationIPRequestDTO::class,
                    'json',
                    [AbstractNormalizer::OBJECT_TO_POPULATE => $deviceIpRequestDTO],
                );

                $validationErrors = $this->validator->validate($deviceIpRequestDTO);
                if (empty($this->getValidationErrorAsArray($validationErrors))) {
                    if ($deviceIpRequestDTO->getIpAddress() !== null) {
                        $user->setIpAddress($deviceIpRequestDTO->getIpAddress());
                    }
                    if ($deviceIpRequestDTO->getExternalIpAddress() !== null) {
                        $user->setExternalIpAddress($deviceIpRequestDTO->getExternalIpAddress());
                    }
                    try {
                        $this->deviceRepository->persist($user);
                        $this->deviceRepository->flush();
                    } catch (ORMInvalidArgumentException|ORMException|OptimisticLockException $e) {
                        $this->logger->error(
                            'failed to save login IP address data for: '.$user->getUsername(),
                            $e->getTrace()
                        );
                    }
                }

                $deviceAuthenticationResponse = new DeviceAuthenticationResponse($data['token']);
                try {
                    $responseMessage = $this->normalizeResponse($deviceAuthenticationResponse);
                } catch (ExceptionInterface) {
                    $responseMessage['error'] = sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'Device authentication ');
                    $this->logger->error($responseMessage['error']);
                }
            } catch (NotEncodableValueException) {
                $responseMessage['error'] = APIErrorMessages::FORMAT_NOT_SUPPORTED;
                $this->logger->error($responseMessage['error']);
            }

            $authenticationSuccessEvent->setData($responseMessage);
        }
    }
}
