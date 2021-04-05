<?php


namespace App\HomeAppSensorCore\ESPDeviceSensor;

use App\Entity\Core\GroupnNameMapping;
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
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractHomeAppUserSensorServiceCore implements APIErrorInterface
{
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
     * @var ?UserInterface
     */
    private ?UserInterface $user;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

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
         //   $this->setUserVariables();
        } catch (\Exception | \RuntimeException $e) {
            $this->fatalErrors[] = $e->getMessage();
        }
    }

//    /**
//     * @throws \Exception
//     */
//    private function setUserVariables()
//    {
//        $userCredentials = [$this->user, 'getUserID'];
//
//        if (is_callable($userCredentials, true)) {
//            if ($this->user instanceof User) {
//               $this->groupNameDetails = $this->user->getUserGroupMappingEntities();
//            }
//            if ($this->user instanceof Devices) {
//                $this->groupNameDetails[] = ['groupNameID' => $this->user->getGroupNameID()];
//            }
//            if (empty($this->groupNameDetails)) {
//                throw new BadRequestException('The User Groups Cannot Be Set');
//            }
//        } else {
//            throw new \RuntimeException('Could not find user');
//        }
//    }


    /**
     * @return int|null
     */
    protected function getUserID()
    {
        return $this->user->getUserID();
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getFatalErrors(): array
    {
        return $this->fatalErrors;
    }

    /**
     * @return array
     */
    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

}
