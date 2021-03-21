<?php


namespace App\HomeAppSensorCore;

use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CardViewForms\StandardSensorOutOFBoundsForm;
use App\Form\SensorForms\UpdateReadingForm;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractHomeAppSensorServiceCore
{
    // should work with none standard sensors aswell make sure to find usages and update the new sensor interface to see results
    protected const SENSOR_TYPE_DATA = [
        SensorType::DHT_SENSOR => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ]
            ]
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ]
            ]
        ],

        SensorType::SOIL_SENSOR => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                'analog' =>  Analog::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ]
            ]
        ],

        SensorType::BMP_SENSOR => [
            'alias' => 'bmp',
            'object' => Bmp::class,
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ]
            ]
        ],
    ];

    /**
     * @var int|null
     */
    private int|null $userID;

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
    private array $usersRooms = [];

    /**
     * @var array
     */
    private array $devices = [];

    /**
     * @var ?UserInterface
     */
    private ?UserInterface $user;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected EntityManager|EntityManagerInterface $em;

    /**
     * @var array
     */
    protected array $fatalErrors = [];

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * @var array
     */
    protected array $userInputErrors = [];

    /**
     * HomeAppRoomAbstract constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     *
     */
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->user = $security->getUser();

        try {
            $this->setUserVariables();
        } catch (\Exception | \RuntimeException $e) {
            $this->fatalErrors[] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function setUserVariables()
    {
        $userCredentials = [$this->user, 'getUserID'];

        if (is_callable($userCredentials, true)) {
            $this->userID = $this->user->getUserID();
            $this->groupNameDetails = $this->em->getRepository(GroupnNameMapping::class)->getGroupsForUser($this->userID);
            $this->roles = $this->user->getRoles() ?? [];
            $this->devices = $this->em->getRepository(Devices::class)->getAllUsersDevices($this->getGroupNameIDs());
           // dd($this->devices);
            $this->usersRooms = $this->em->getRepository(Room::class)->getRoomsForUser($this->getGroupNameIDs());
            if (empty($this->groupNameDetails) || empty($this->roles)) {
                throw new \RuntimeException('The User Variables Cannot be set Please try again');
            }
        } else {
            throw new \RuntimeException('Could not find user');
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

    protected function getUser(): ?UserInterface
    {
        return $this->user;
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

    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    /**
     * @return array
     */
    #[Pure] public function getServerErrors(): array
    {
        return array_merge($this->getFatalErrors(), $this->serverErrors);
    }

}
