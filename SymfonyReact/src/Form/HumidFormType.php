<?php

namespace App\Form;

use App\Entity\Sensors\Humid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HumidFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('highhumid', TextType::class, [
                'class' => Humid::class,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 2,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])
            ->add('lowhumid', TextType::class, [
                'class' => Humid::class,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 2,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])
            ->add('humidconstrecord', TextType::class, [
                'class' => Humid::class,
                'constraints' => [
                    new NotBlank()
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Humid::class,
        ]);
    }
}
