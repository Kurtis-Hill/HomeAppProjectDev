<?php


namespace App\HomeAppSensorCore;

use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

abstract class AbstractHomeAppSensorServiceCore
{
    //make sure to add a lowercase version of the sensor type name thats store in the DB

    protected const STANDARD_SENSOR_TYPE_DATA = [
        SensorType::DHT_SENSOR => [
            'alias' => 'dht',
            'object' => Dht::class,
            'forms' =>  [
                'temperature' =>  DHTTempCardModalForm::class,
                'humidity' => DHTHumidCardModalForm::class,
            ],
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'forms' =>  [
                'temperature' => DallasTempCardModalForm::class
            ],
        ],

        SensorType::SOIL_SENSOR => [
            'alias' => 'soil',
            'object' => Soil::class,
            'forms' => [
                'analog' => SoilFormType::class
            ],
        ],

        SensorType::BMP_SENSOR => [
            'alias' => 'bmp',
            'object' => Bmp::class,
            'forms' => [
                'latitude',
                'temperature',
                'humidity'
            ],
        ],
    ];


    protected const STANDARD_SENSOR_TYPE_DATA_TWO = [
        SensorType::DHT_SENSOR => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
            ],
        ],

        SensorType::SOIL_SENSOR => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                'analog' =>  Analog::class,
            ],
        ],

        SensorType::BMP_SENSOR => [
            'alias' => 'bmp',
            'object' => Bmp::class,
            'readingTypes' => [
                'latitude' => Latitude::class,
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
            ],
        ],
    ];


    /**
     * @var int
     */
    private int $userID;

    /**
     * @var array
     */
    private array $roles;

    /**
     * @var array
     */
    private array $groupNameDetails = [];

    /**
     * @var array
     */
    protected array $fatalErrors = [];

    /**
     * @var array
     */
    private array $usersRooms = [];

    /**
     * @var array
     */
    private array $devices = [];

    /**
     * @var Security
     */
    private Security $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected EntityManager|EntityManagerInterface $em;


    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     *
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security;

        try {
            $this->setUserVariables();
        } catch (\Exception $e) {
            $this->fatalErrors[] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $this->userID = $this->user->getUser()->getUserID();
        $this->groupNameDetails = $this->groupNameDetails = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($this->userID);
        $this->roles = $this->user->getUser()->getRoles();
        $this->devices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->getGroupNameIDs());
        $this->usersRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->getGroupNameIDs());

        if (empty($this->groupNameDetails) || !isset($this->userID) || empty($this->roles)) {
            throw new \Exception('The User Variables Cannot be set Please try again');
        }
    }

    #[Pure] public function getGroupNameIDs()
    {
        return array_column($this->groupNameDetails, 'groupNameID');
    }

    protected function getGroupNameDetails()
    {
        return $this->groupNameDetails;
    }

    protected function getUserID()
    {
        return $this->userID;
    }

    protected function getUserRoles()
    {
        return $this->roles;
    }

    protected function getUser(): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        return $this->user->getUser();
    }

    /**
     * @return array
     */
    protected function getUsersRooms(): array
    {
        return $this->usersRooms;
    }

    /**
     * @return array
     */
    protected function getUsersDevices(): array
    {
        return $this->devices;
    }

    public function getFatalErrors(): array
    {
        return $this->fatalErrors;
    }

}
