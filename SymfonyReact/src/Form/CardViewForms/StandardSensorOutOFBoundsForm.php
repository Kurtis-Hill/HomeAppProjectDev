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
use App\Form\CustomFormValidators\NoSpecialCharactersContraint;
use App\Form\CustomFormValidators\SensorDataValidators\DHTTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DHTHumidityConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\DallasTemperatureConstraint;
use App\Form\CustomFormValidators\SensorDataValidators\SoilContraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardSensorOutOFBoundsForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getData() instanceof Temperature) {
            if ($options['formSensorType'] instanceof Dht) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])
                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTTemperatureConstraint(),
                            new NoSpecialCharactersContraint(),
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
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DallasTemperatureConstraint(),
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ]
                    ]);
            }

            if ($options['formSensorType'] instanceof Bmp) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }

        if ($builder->getData() instanceof Humidity) {
            if ($options['formSensorType'] instanceof Dht) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTHumidityConstraint(),
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])
                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new DHTHumidityConstraint(),
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ]);
            }

            if ($options['formSensorType'] instanceof Bmp) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }

        if ($builder->getData() instanceof Analog) {
            if ($options['formSensorType'] instanceof Soil) {
                $builder
                    ->add('highReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new SoilContraint(),
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new SoilContraint(),
                            new NoSpecialCharactersContraint(),
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
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ])

                    ->add('lowReading', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new NoSpecialCharactersContraint(),
                            new NotBlank(),
                        ],
                    ]);
            }
        }
        else {
            $builder
                ->add('highReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NoSpecialCharactersContraint(),
                        new NotBlank(),
                    ],
                ])

                ->add('lowReading', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new NoSpecialCharactersContraint(),
                        new NotBlank(),
                    ],
                ])
            ;
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
