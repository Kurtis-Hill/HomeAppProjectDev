<?php

namespace App\Repository\Core;

use App\Entity\Core\Sensors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

/**
 * @method Sensors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensors[]    findAll()
 * @method Sensors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorNamesRepository extends EntityRepository
{
    private function queryForUserSensors()
    {

    }

//    public function getAllSensorsForUser($rooms, $groupNameid)
//    {
//        $qb = $this->createQueryBuilder('sn');
//
//        $sensorByRoom = [];
//        foreach ($rooms as $value) {
//            $qb->select('sn')
//            ->where(
//                $qb->expr()->eq('sn.roomid', ':roomid'),
//                $qb->expr()->eq('sn.groupnameid', ':groupnameid')
//            )
//            ->setParameters(['roomid' => $value['r_roomid'], 'groupnameid' => $groupNameid]);
//
//            $result = $qb->getQuery()->getScalarResult();
//            $sensorByRoom[$value['r_room']] = $result;
//        }
//       // dd($sensorByRoom);
//
//        return $sensorByRoom;
//
//    }

}
