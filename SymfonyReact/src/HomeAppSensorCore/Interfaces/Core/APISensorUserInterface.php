<?php


namespace App\HomeAppSensorCore\Interfaces\Core;


interface APISensorUserInterface
{
    /**
     * @param array $userGroupMappingEntities
     */
    public function setUserGroupMappingEntities(array $userGroupMappingEntities): void;

    /**
     * @TODO remove this just use below
     * @return array
     */
    public function getUserGroupMappingEntities(): array;

    /**
     * @return array
     */
    public function getGroupNameIds(): array;
}
