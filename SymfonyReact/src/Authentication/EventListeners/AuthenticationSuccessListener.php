<?php

namespace App\Authentication\EventListeners;

use App\API\APIErrorMessages;
use App\API\Traits\HomeAppAPITrait;
use App\Authentication\DTOs\Response\UserAuthenticationResponseDTO;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class AuthenticationSuccessListener
{
    use HomeAppAPITrait;

    private RequestStack $requestStack;

    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(
        RequestStack $requestStack,
        DeviceRepositoryInterface $deviceRepository,
    ) {
        $this->requestStack = $requestStack;
        $this->deviceRepository = $deviceRepository;
    }

    
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        $user = $authenticationSuccessEvent->getUser();

        if ($user instanceof User) {
            $data = new UserAuthenticationResponseDTO($user->getUserID(), $user->getRoles());

            try {
                $data = $this->normalizeResponse($data);
            } catch (ExceptionInterface) {
                $data = ['error' => sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'User data')];
            }

            $authenticationSuccessEvent->setData($data);
        }
        if ($user instanceof Devices) {
            $deviceLoginRequest = json_decode($this->requestStack->getCurrentRequest()?->getContent(), true);
            $ipAddress = $deviceLoginRequest['ipAddress'] ?? null;
            $externalIpAddress = $deviceLoginRequest["externalIpAddress"] ?? null;

            $ipAddress === null ?: $user->setIpAddress($ipAddress);
            $externalIpAddress === null ?: $user->setExternalIpAddress($externalIpAddress);
            try {
                $this->deviceRepository->persist($user);
                $this->deviceRepository->flush();
            } catch (Exception) {
                error_log('failed to save login IP address data for: '.$user->getUsername());
            }
        }
    }
}
