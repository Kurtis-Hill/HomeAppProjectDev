<?php


namespace App\Core\APIInterface;


interface APIErrorInterface
{
    public function getServerErrors(): array;
    public function getUserInputErrors(): array;
}
