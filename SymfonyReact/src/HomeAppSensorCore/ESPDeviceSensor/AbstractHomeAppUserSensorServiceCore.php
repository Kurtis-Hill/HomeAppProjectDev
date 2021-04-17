<?php


namespace App\HomeAppSensorCore\ESPDeviceSensor;

use App\Entity\Core\User;
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
use App\HomeAppSensorCore\Interfaces\Core\APISensorUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;


abstract class AbstractHomeAppUserSensorServiceCore implements APIErrorInterface
{
    public const SENSOR_TYPE_DATA = [
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
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class,
            ],
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
     * @var ?User
     */
    private ?User $user;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

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
        $this->user = $security->getUser() instanceof User ? $security->getUser() : null;

        try {
            $this->checkUserInstance();
        } catch (\Exception | \RuntimeException $e) {
            $this->fatalErrors[] = $e->getMessage();
        }
    }

    /**
     * @throws \Exception
     */
    private function checkUserInstance(): void
    {
        if (!$this->user instanceof APISensorUserInterface) {
            throw new BadRequestException('This entity cannot use this service');
        }
    }

    /**
     * @param int|string $groupRequest
     * @return bool
     */
    public function checkIfUserIsPartOfGroup(int|string $groupRequest): bool
    {
        if (!is_numeric($groupRequest)) {
            throw new BadRequestException('the group provided is not correct');
        }

        $groupRequest = (int) $groupRequest;

        return in_array($groupRequest, $this->user->getGroupNameIds(), true);
    }

    /**
     * @return int|null
     */
    protected function getUserID()
    {
        return $this->user->getUserID();
    }

    /**
     * @return APISensorUserInterface|null
     */
    protected function getUser(): ?User
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
