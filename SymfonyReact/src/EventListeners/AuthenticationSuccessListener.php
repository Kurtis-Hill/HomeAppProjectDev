<?php

namespace App\EventListeners;

use App\Devices\Entity\Devices;
use App\Entity\Core\User;
use App\ESPDeviceSensor\Repository\ORM\Device\DeviceRepositoryInterface;
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
    /**
     * @param AuthenticationSuccessEvent $authenticationSuccessEvent
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $authenticationSuccessEvent): void
    {
        $user = $authenticationSuccessEvent->getUser();

        if ($user instanceof User) {
            $data = $authenticationSuccessEvent->getData();
            $data['userData'] = [
                'userID' => $user->getUserID(),
                'roles' => $user->getRoles(),
            ];

            $authenticationSuccessEvent->setData($data);
        }
        if ($user instanceof Devices) {
            $deviceLoginRequest = json_decode($this->requestStack->getCurrentRequest()->getContent(), true);
            $ipAddress = $deviceLoginRequest['ipAddress'] ?? null;
            $externalIpAddress = $deviceLoginRequest["externalIpAddress"] ?? null;

            $user->setIpAddress($ipAddress);
            $user->setExternalIpAddress($externalIpAddress);
            $this->deviceRepository->persist($user);
            $this->deviceRepository->flush();
        }

    }
}
