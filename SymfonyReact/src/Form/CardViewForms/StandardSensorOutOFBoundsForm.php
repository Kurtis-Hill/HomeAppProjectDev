<?php


namespace App\Form\CardViewForms;



use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CustomFormValidators\NoSpecialCharactersConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\BMP280TemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DHTTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\HumidityConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DallasTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\LatitudeConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\SoilContraint;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class StandardSensorOutOFBoundsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $highLowCheck = new Callback(function(int|float $highReading, ExecutionContextInterface $context) {
            $lowReading = $context->getRoot()->getData()->getLowReading();
            $readingType = $context->getRoot()->getData();

            if ($readingType instanceof AllSensorReadingTypeInterface) {
                if ($highReading < $lowReading) {
                    $context
                        ->buildViolation('High reading for ' . $readingType->getSensorTypeName() . ' cannot be lower than low reading')
                        ->addViolation();
                }
            } else {
                $context
                    ->buildViolation('App needs updating to support this sensor type')
                    ->addViolation();
            }
        });

//        $booleanCheck = new Callback(function ($userInput, ExecutionContextInterface $context) {
//           if ($userInput !== true )
//        });

        if ($builder->getData() instanceof Temperature) {
            if ($options['formSensorType'] instanceof Dht) {
//dd('here');
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
//                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])
                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
//                            new NoSpecialCharactersConstraint(),
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
//                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DallasTemperatureConstraint(),
//                            new NoSpecialCharactersConstraint(),
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
//                        new NoSpecialCharactersConstraint(),
                        new NotBlank(),
                        $highLowCheck,
                    ],
                ])
                ->add('lowReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new HumidityConstraint(),
//                        new NoSpecialCharactersConstraint(),
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
                            new SoilContraint(),
//                            new NoSpecialCharactersConstraint(),
                            new NotBlank(),
                            $highLowCheck,
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new SoilContraint(),
//                            new NoSpecialCharactersConstraint(),
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
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'formSensorType' => false,
        ]);
    }
}
