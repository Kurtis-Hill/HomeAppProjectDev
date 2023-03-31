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

    public const DELETE_ROOM = 'delete-room';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_ROOM, self::VIEW_USER_ROOMS, self::DELETE_ROOM], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::ADD_NEW_ROOM => $this->canAddNewRoom($user, $subject),
            self::VIEW_USER_ROOMS => $this->canViewRooms($user, $subject),
            self::DELETE_ROOM => $this->canDeleteRoom($user, $subject),
            default => false,
        };
    }

    private function canAddNewRoom(User $user, Room $room): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    private function canViewRooms(User $user, Room $room): bool
    {
        return true;
    }

    private function canDeleteRoom(User $user, Room $room): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->canAddNewRoom($user, $room);
    }
}
