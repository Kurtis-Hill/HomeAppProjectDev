<?php

namespace App\UserInterface\Controller\Card;

use App\API\APIErrorMessages;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/user/card-data')]
class CardViewFormController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/v2/card-sensor-form', name: 'card-view-form-v2', methods: [Request::METHOD_GET])]
    public function getCardViewForm(
        Request $request,
        CardViewRepositoryInterface $cardViewRepository
    ): JsonResponse {
        $cardViewID = $request->query->get('card-view-id');

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $cardViewObject = $cardViewRepository->findOneById($cardViewID);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Card view')]);
        }
        if ($cardViewObject === null) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'CardView')]);
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_VIEW_CARD_VIEW_FORM, $cardViewObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

//        $cardViewFormDTO =
    }
}
