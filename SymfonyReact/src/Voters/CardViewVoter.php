<?php


namespace App\Voters;


use App\Entity\Card\CardView;
use App\Entity\Core\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CardViewVoter extends Voter
{
    public const CAN_VIEW_CARD_VIEW_FORM = 'view-card-view-data';

    public const CAN_EDIT_CARD_VIEW_FORM = 'can-edit-card-view-data';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CAN_EDIT_CARD_VIEW_FORM, self::CAN_VIEW_CARD_VIEW_FORM])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::CAN_VIEW_CARD_VIEW_FORM => $this->canUserViewCardViewObject($user, $subject),
            self::CAN_EDIT_CARD_VIEW_FORM => $this->canUserEditCardViewObject($user, $subject),
            default => false
        };
    }

    private function canUserViewCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        return $this->canUserEditCardViewObject($user, $cardView);
    }

    private function canUserEditCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        if (!$user instanceof User) {
//            dd(1);
            return false;
        }

        if ($cardView->getUserID()->getUserID() !== $user->getUserID()) {
//            dd($cardView->getUserID(), $user->getUserID());
            return false;
        }
//dd('f');
        return true;
    }
}
