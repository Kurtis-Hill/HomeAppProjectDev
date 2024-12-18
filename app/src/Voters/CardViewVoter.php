<?php

namespace App\Voters;

use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CardViewVoter extends Voter
{
    public const CAN_VIEW_CARD_VIEW_FORM = 'view-card-view-data';

    public const CAN_EDIT_CARD_VIEW_FORM = 'can-edit-card-view-data';

    public const VIEW_DEVICE_CARD_DATA = 'view-device-card-data';

    public const VIEW_ROOM_CARD_DATA = 'view-room-card-data';

    public const VIEW_GROUP_CARD_DATA = 'view-group-card-data';

    public const CAN_ADD_NEW_CARD = 'can-add-new-card';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::CAN_EDIT_CARD_VIEW_FORM,
            self::CAN_VIEW_CARD_VIEW_FORM,
            self::VIEW_DEVICE_CARD_DATA,
            self::VIEW_ROOM_CARD_DATA,
            self::VIEW_GROUP_CARD_DATA,
            self::CAN_ADD_NEW_CARD
        ])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::CAN_VIEW_CARD_VIEW_FORM => $this->canUserViewCardViewObject($user, $subject),
            self::CAN_EDIT_CARD_VIEW_FORM => $this->canUserEditCardViewObject($user, $subject),
            self::VIEW_DEVICE_CARD_DATA => $this->viewDeviceCardData($user, $subject),
            self::VIEW_ROOM_CARD_DATA => $this->viewRoomCardData($user, $subject),
            self::CAN_ADD_NEW_CARD => $this->canAddNewCardView($user, $subject),
            self::VIEW_GROUP_CARD_DATA => $this->canViewGroupCardData($user, $subject),

            default => false
        };
    }

    public function canViewGroupCardData(UserInterface $user, Group $group): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (in_array($group->getGroupID(), $user->getAssociatedGroupIDs())) {
            return true;
        }

        $checkCommon = $this->checkCommon($user);

        return $checkCommon ?? true;
    }

    private function canAddNewCardView(UserInterface $user, Sensor $sensor): bool
    {
        $checkCommon = $this->checkCommon($user);
        if ($checkCommon !== null) {
            return $checkCommon;
        }

        $devices = $sensor->getDevice();
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array(
            $devices->getGroupObject()->getGroupID(),
            $user->getAssociatedGroupIDs(),
            true
        )) {
            return false;
        }

        return true;
    }

    private function canUserViewCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        return $this->canUserEditCardViewObject($user, $cardView);
    }

    private function canUserEditCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        $checkCommon = $this->checkCommon($user);

        if ($checkCommon !== null) {
            return $checkCommon;
        }

        if (!$user instanceof User) {
            return false;
        }

        return $cardView->getUserID()->getUserID() === $user->getUserID();
    }

    private function viewRoomCardData(UserInterface $user, Room $room): bool
    {
        $checkCommon = $this->checkCommon($user);

        return $checkCommon ?? true;
    }

    private function viewDeviceCardData(UserInterface $user, Devices $devices): bool
    {
        $checkCommon = $this->checkCommon($user);

        if ($checkCommon !== null) {
            return $checkCommon;
        }

        if (!$user instanceof User) {
            return false;
        }
        if (!in_array(
            $devices->getGroupObject()->getGroupID(),
            $user->getAssociatedGroupIDs(),
            true
        )) {
            return false;
        }

        return true;
    }

    private function checkCommon(UserInterface $user): ?bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }
}
