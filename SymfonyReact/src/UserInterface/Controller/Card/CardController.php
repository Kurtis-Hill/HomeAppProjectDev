<?php

namespace App\UserInterface\Controller\Card;

use App\API\Traits\HomeAppAPIResponseTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use App\Form\FormMessages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/card-data/v2/')]
class CardController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('all-cards', name: 'card-data-v2', methods: [Request::METHOD_GET])]
    public function index(Request $request, DeviceRepositoryInterface $deviceRepository): Response
    {
        $route = $request->get('view');
        $deviceId = $request->get('device-id');

        if (isset($deviceId) && is_numeric($deviceId)) {
            $device = $deviceRepository->findOneById($deviceId);

            if ($device instanceof Devices) {
                try {
                    $this->denyAccessUnlessGranted(SensorVoter::VIEW_DEVICE_CARD_DATA, $device);
                } catch (AccessDeniedException $exception) {
                    return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
                }
            } else {
                return $this->sendBadRequestJsonResponse(['No device found']);
            }
        }


    }
}
