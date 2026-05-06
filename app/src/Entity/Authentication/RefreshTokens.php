<?php

namespace App\Entity\Authentication;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;

#[
    ORM\Entity(repositoryClass: RefreshTokenRepository::class),
    ORM\Table(name: "refresh_tokens"),
]
class RefreshTokens extends BaseRefreshToken
{
}
