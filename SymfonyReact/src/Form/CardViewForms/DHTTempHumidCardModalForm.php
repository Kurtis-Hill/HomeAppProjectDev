<?php


namespace App\Form\CardViewForms;


use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DHTTempHumidCardModalForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //     dd($options['cardSensorState']);
        $builder

            ->add('highReading', TextType::class, [
                'required' => true,
                'constraints' => [
                  new NotBlank(),
                  new Length(['min' => 1, 'max' => 3,
                      'minMessage' => 'You must enter a value',
                      'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])

            ->add('lowReading', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 3,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])

            ->add('icon', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])

            ->add('colour', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])

            ->add('cardViewState', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])

            ->add('constRecord', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
        //If adding sensor with more than one reading type add the sensor type name in the if statement
        if ($options['sensorType'] == 'DHT') {
            $builder
                ->add('secondHighReading', TextType::class, [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 1, 'max' => 3,
                            'minMessage' => 'You must enter a value',
                            'maxMessage' => 'This number is too high {{ value }}']),
                    ],
                ])
            ->add('secondLowReading', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 3,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])
            ->add('secondConstRecord', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 3,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => null,
            'sensorType' => null,
        ]);
    }
}