<?php

namespace App\DTOs\UserInterface\Response\NavBar;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NavBarListLinkDTO
{
    private string $displayName;

    private string $link;

    public function __construct(string $displayName, string $link)
    {
        $this->displayName = $displayName;
        $this->link = $link;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}
