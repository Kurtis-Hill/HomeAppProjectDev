<?php

namespace App\Repository\Core;

use App\Entity\Core\Sensornames;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sensornames|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensornames|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensornames[]    findAll()
 * @method Sensornames[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorNamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensornames::class);
    }

    private function queryForUserSensors()
    {

    }

    public function getAllSensorsForUser($rooms, $groupNameid)
    {
        $qb = $this->createQueryBuilder('sn');

        $sensorByRoom = [];
        foreach ($rooms as $value) {
            $qb->select('sn')
            ->where(
                $qb->expr()->eq('sn.roomid', ':roomid'),
                $qb->expr()->eq('sn.groupnameid', ':groupnameid')
            )
            ->setParameters(['roomid' => $value['r_roomid'], 'groupnameid' => $groupNameid]);

            $result = $qb->getQuery()->getScalarResult();
            $sensorByRoom[$value['r_room']] = $result;
        }
       // dd($sensorByRoom);

        return $sensorByRoom;

    }

}
