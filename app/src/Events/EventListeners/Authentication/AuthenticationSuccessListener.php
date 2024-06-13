<?php

namespace App\EventListeners\Authentication;

use App\DTOs\Authentication\Request\DeviceAuthenticationIPRequestDTO;
use App\DTOs\Authentication\Response\DeviceAuthenticationResponse;
use App\DTOs\Authentication\Response\UserAuthenticationResponseDTO;
use App\DTOs\Authentication\Response\UserDataDTO;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
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
                $responseMessage = $this->normalize($userAuthenticationDTO);
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
                if (!$this->checkIfErrorsArePresent($validationErrors)) {
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
                    $responseMessage = $this->normalize($deviceAuthenticationResponse);
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
