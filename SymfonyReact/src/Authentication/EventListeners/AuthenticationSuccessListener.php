<?php

namespace App\Authentication\EventListeners;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\User\Entity\User;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationSuccessListener
{
    private RequestStack $requestStack;

    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(
        RequestStack $requestStack,
        DeviceRepositoryInterface $deviceRepository,
    )
    {
        $this->requestStack = $requestStack;
        $this->deviceRepository = $deviceRepository;
    }

    
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        $user = $authenticationSuccessEvent->getUser();
        $data = $authenticationSuccessEvent->getData();

        if ($user instanceof User) {
            $data['userData'] = [
                'userID' => $user->getUserID(),
                'roles' => $user->getRoles(),
            ];

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
