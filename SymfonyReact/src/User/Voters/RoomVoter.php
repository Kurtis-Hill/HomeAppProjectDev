<?php

namespace App\User\Voters;

use App\User\Entity\Room;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RoomVoter extends Voter
{
    public const ADD_NEW_ROOM = 'add-new-room';

    public const VIEW_USER_ROOMS = 'view-users-rooms';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_ROOM, self::VIEW_USER_ROOMS], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        return match($attribute) {
            self::ADD_NEW_ROOM => $this->canAddNewRoom($user, $subject),
            self::VIEW_USER_ROOMS => $this->canViewRooms($user, $subject),
            default => false,
        };
    }

    private function canAddNewRoom(UserInterface $user, Room $room): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array($room->getGroupNameID()->getGroupNameID(), $user->getGroupNameIds(), true)) {
            return false;
        }

        return true;
    }

    private function canViewRooms(UserInterface $user, Room $room): bool
    {
        if (in_array($user->getRoles(), ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'])) {
            return true;
        }

        return $this->canAddNewRoom($user, $room);
    }
}
