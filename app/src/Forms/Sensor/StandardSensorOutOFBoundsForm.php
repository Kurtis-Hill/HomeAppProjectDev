<?php


namespace App\Forms\Sensor;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Services\CustomValidators\Sensor\SensorDataValidators\BMP280TemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use App\Services\CustomValidators\Sensor\SensorDataValidators\SoilConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class StandardSensorOutOFBoundsForm extends AbstractType implements SensorReadingUpdateInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $highLowCheck = new Callback(function(int|float $highReading, ExecutionContextInterface $context) {
            $lowReading = $context->getRoot()->getData()->getLowReading();
            $readingType = $context->getRoot()->getData();

            if ($readingType instanceof AllSensorReadingTypeInterface) {
                if ($highReading < $lowReading) {
                    $context
                        ->buildViolation('High reading for ' . $readingType->getReadingType() . ' cannot be lower than low reading')
                        ->addViolation();
                }
            } else {
                $context
                    ->buildViolation('App needs updating to support this sensor type')
                    ->addViolation();
            }
        });

        if ($builder->getData() instanceof Temperature) {
            if ($options['formSensorType'] instanceof Dht) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])
                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }

            if ($options['formSensorType'] instanceof Dallas) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DallasTemperatureConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DallasTemperatureConstraint(),
                            new NotBlank(),
                        ]
                    ]);
            }

            if ($options['formSensorType'] instanceof Bmp) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new BMP280TemperatureConstraint(),
                            new NotBlank(),
                            $highLowCheck,

                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new BMP280TemperatureConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }

        if ($builder->getData() instanceof Humidity) {
            $builder
                ->add('highReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new HumidityConstraint(),
                        new NotBlank(),
                        $highLowCheck,
                    ],
                ])
                ->add('lowReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new HumidityConstraint(),
                        new NotBlank(),
                    ],
                ]);
        }

        if ($builder->getData() instanceof Analog) {
            if ($options['formSensorType'] instanceof Soil) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new SoilConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new SoilConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }

        if ($builder->getData() instanceof Latitude) {
            if ($options['formSensorType'] instanceof Bmp) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new LatitudeConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new LatitudeConstraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }

        $builder
            ->add('constRecord', TextType::class, [
                'required' => false,
                'empty_data' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'formSensorType' => false,
        ]);
    }
}
