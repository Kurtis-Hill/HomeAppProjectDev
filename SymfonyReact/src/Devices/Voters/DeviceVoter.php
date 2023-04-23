<?php

namespace App\Devices\Voters;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceVoter extends Voter
{
    public const ADD_NEW_DEVICE = 'add-new-device';

    public const UPDATE_DEVICE = 'update-device';

    public const DELETE_DEVICE = 'delete-device';

    public const GET_DEVICE = 'get-device';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::ADD_NEW_DEVICE,
            self::UPDATE_DEVICE,
            self::DELETE_DEVICE,
            self::GET_DEVICE,
        ])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return match ($attribute) {
            self::ADD_NEW_DEVICE => $this->canAddNewDevice($user, $subject),
            self::UPDATE_DEVICE => $this->canUpdateDevice($user, $subject),
            self::DELETE_DEVICE => $this->cadDeleteDevice($user, $subject),
            self::GET_DEVICE => $this->canGetDevice($user, $subject),
            default => false
        };
    }

    private function checkCommon(UserInterface $user, Group $proposedGroupName): ?bool
    {
        if (!$user instanceof User) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }

        if (!in_array($proposedGroupName->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return null;
    }


    private function canAddNewDevice(UserInterface $user, NewDeviceDTO $newDeviceCheckDTO): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $newDeviceCheckDTO->getGroupNameObject(),
        );

        return $checkCommon ?? false;

    }

    private function canUpdateDevice(UserInterface $user, UpdateDeviceDTO $updateDeviceDTO): bool
    {
        $commonSuccess = $this->checkCommon(
            $user,
            $updateDeviceDTO->getDeviceToUpdate()->getGroupObject(),
        );
        if ($commonSuccess !== null) {
            return $commonSuccess;
        }

        if (($updateDeviceDTO->getProposedGroupNameToUpdateTo() !== null) && !in_array(
                $updateDeviceDTO->getProposedGroupNameToUpdateTo()->getGroupID(),
                $user->getAssociatedgroupIDs(),
                true
            )) {
                return false;
            }

        return true;
    }

    private function cadDeleteDevice(UserInterface $user, Devices $devices): bool
    {

        $checkCommon = $this->checkCommon(
                $user,
                $devices->getGroupObject(),
        );

        return $checkCommon ?? true;
    }

    public function canGetDevice(UserInterface $user, Devices $devices): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $devices->getGroupObject(),
        );

        return $checkCommon ?? true;
    }
}
