<?php
declare(strict_types=1);

namespace App\Voters;

use App\DTOs\Device\Internal\NewDeviceDTO;
use App\DTOs\Device\Internal\UpdateDeviceDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceVoter extends Voter
{
    public const ADD_NEW_DEVICE = 'add-new-device';

    public const UPDATE_DEVICE = 'update-device';

    public const DELETE_DEVICE = 'delete-device';

    public const GET_DEVICE = 'get-device';

    public const PING_DEVICE = 'ping-device';

    public const RESTART_DEVICE = 'restart-device';

    public const RESET_DEVICE = 'reset-device';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::ADD_NEW_DEVICE,
            self::UPDATE_DEVICE,
            self::DELETE_DEVICE,
            self::GET_DEVICE,
            self::PING_DEVICE,
            self::RESTART_DEVICE,
            self::RESET_DEVICE,
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
            self::PING_DEVICE => $this->canPing($user, $subject),
            self::RESTART_DEVICE => $this->canRestart($user, $subject),
            self::RESET_DEVICE => $this->canReset($user, $subject),
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

        return $checkCommon ?? true;
    }

    private function canUpdateDevice(UserInterface $user, UpdateDeviceDTO $updateDeviceDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }
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

    private function canGetDevice(UserInterface $user, Devices $devices): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $devices->getGroupObject(),
        );

        return $checkCommon ?? true;
    }

    private function canPing(UserInterface $user, Devices $devices): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $devices->getGroupObject(),
        );

        return $checkCommon ?? true;
    }

    private function canRestart(UserInterface $user, Devices $devices): bool
    {
        $checkCommon = $this->checkCommon(
            $user,
            $devices->getGroupObject(),
        );

        return $checkCommon ?? true;
    }

    private function canReset(UserInterface $user, Devices $devices): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $user->isAdmin();
    }
}
