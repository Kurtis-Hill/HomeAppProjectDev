<?php


namespace App\Services;


use App\HomeAppCore\HomeAppSensorServiceCoreAbstract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SensorServiceCoreService extends HomeAppSensorServiceCoreAbstract
{
    public function __construct(EntityManagerInterface $em, Security $security, Request $request)
    {
        parent::__construct($em, $security);

        $this->currentRoom = $request->get('room');
    }
}
