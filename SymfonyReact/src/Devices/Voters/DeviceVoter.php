<?php

namespace App\Devices\Voters;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
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

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_DEVICE, self::UPDATE_DEVICE, self::DELETE_DEVICE])) {
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
            default => false
        };
    }

    private function canAddNewDevice(UserInterface $user, NewDeviceDTO $newDeviceCheckDTO): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $newDeviceCheckDTO->getGroupNameObject(),
        );

        if ($checkCommon !== null) {
            return $checkCommon;
        }

        if (!in_array($newDeviceCheckDTO->getGroupNameObject()->getGroupNameID(), $user->getAssociatedGroupNameIds(), true)) {
            return false;
        }

        return true;
    }

    private function canUpdateDevice(UserInterface $user, UpdateDeviceDTO $updateDeviceDTO): bool
    {
        $commonSuccess = $this->checkCommon(
            $user,
            $updateDeviceDTO->getDeviceToUpdate()->getGroupNameObject(),
        );
        if ($commonSuccess !== null) {
            return $commonSuccess;
        }

//        dd('lol', $updateDeviceDTO->getProposedGroupNameToUpdateTo());
        if ($updateDeviceDTO->getProposedGroupNameToUpdateTo() !== null) {
            if (!in_array(
                $updateDeviceDTO->getProposedGroupNameToUpdateTo()->getGroupNameID(),
                $user->getAssociatedGroupNameIds(),
                true
            )) {
                return false;
            }
        }
        if (!in_array(
            $updateDeviceDTO->getDeviceToUpdate()->getGroupNameObject()->getGroupNameID(),
            $user->getAssociatedGroupNameIds(),
            true
        )
        ) {
            return false;
        }
//dd('lol');

        return true;
    }

    private function cadDeleteDevice(UserInterface $user, Devices $devices): bool
    {

        $checkCommon = $this->checkCommon(
                $user,
                $devices->getGroupNameObject(),
        );

        return $checkCommon ?? true;
    }

    private function checkCommon(UserInterface $user, GroupNames $proposedGroupName): ?bool
    {
        if (!$user instanceof User) {
            return false;
        }
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        if (!in_array($proposedGroupName->getGroupNameID(), $user->getAssociatedGroupNameIds(), true)) {
            return false;
        }

        return null;
    }
}
