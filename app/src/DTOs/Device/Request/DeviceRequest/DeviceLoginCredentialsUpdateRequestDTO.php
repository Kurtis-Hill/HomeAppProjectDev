<?php
declare(strict_types=1);

namespace App\DTOs\Device\Request\DeviceRequest;

use App\Services\Device\Request\DeviceSettingsUpdateRequestHandler;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class DeviceLoginCredentialsUpdateRequestDTO
{
    public function __construct(
        private string $username,
        private ?string $password,
    ) {}

    #[Groups([
        DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
        DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT
    ])]
    public function getUsername(): string
    {
        return $this->username;
    }

    #[Groups([DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT])]
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
