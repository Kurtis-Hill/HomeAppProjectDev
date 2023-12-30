<?php
declare(strict_types=1);

namespace App\Common\DTO\Request\IPLog;

use Symfony\Component\Validator\Constraints as Assert;

class IPLogRequestDTO
{
    #[
//        Assert\Ip(version: 'all'),
        Assert\Length(
            min: 7,
            max: 13,
            minMessage: "IP address must be at least {{ limit }} characters long",
            maxMessage: "IP address cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private mixed $ipAddress;

    public function getIpAddress(): mixed
    {
        return $this->ipAddress;
    }

    public function setIpAddress(mixed $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }
}
