<?php


namespace App\Services;


use App\Entity\Core\Sensortype;
use App\HomeAppCore\HomeAppRoomAbstract;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SensorsService extends HomeAppRoomAbstract
{
    protected $currentSensorType;


    public function __construct(EntityManagerInterface $em, Security $security, Request $request)
    {
        parent::__construct($em, $security);

        $this->currentSensorType = $request->get('readingtype');
    }

    public function getAllSensorTypes()
    {
        $sensorTypeRepository = $this->em->getRepository(Sensortype::class);

        return $sensorTypeRepository->findAll();
    }
}