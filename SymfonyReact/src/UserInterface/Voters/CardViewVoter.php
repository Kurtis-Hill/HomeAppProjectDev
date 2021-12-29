<?php

namespace App\UserInterface\Voters;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\CardView;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CardViewVoter extends Voter
{
    public const CAN_VIEW_CARD_VIEW_FORM = 'view-card-view-data';

    public const CAN_EDIT_CARD_VIEW_FORM = 'can-edit-card-view-data';

    public const VIEW_DEVICE_CARD_DATA = 'view-device-card-data';

    public const VIEW_ROOM_CARD_DATA = 'view-room-card-data';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::CAN_EDIT_CARD_VIEW_FORM,
            self::CAN_VIEW_CARD_VIEW_FORM,
            self::VIEW_DEVICE_CARD_DATA,
            self::VIEW_ROOM_CARD_DATA,
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
            default => false
        };
    }

    #[Pure]
    private function canUserViewCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        return $this->canUserEditCardViewObject($user, $cardView);
    }

    #[Pure]
    private function canUserEditCardViewObject(UserInterface $user, CardView $cardView): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($cardView->getUserID()->getUserID() !== $user->getUserID()) {
            return false;
        }

        return true;
    }

    private function viewRoomCardData(UserInterface $user, Room $room): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array($room->getGroupNameID(), $user->getGroupNameIds(), true)) {
            return false;
        }

        return true;
    }

    private function viewDeviceCardData(UserInterface $user, Devices $devices): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array(
            $devices->getGroupNameObject()->getGroupNameID(),
            $user->getGroupNameIds(),
            true
        )
        ) {
            return false;
        }

        return true;
    }

}
