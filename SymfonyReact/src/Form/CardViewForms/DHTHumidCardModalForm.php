<?php


namespace App\Form\CardViewForms;


use App\Entity\Sensors\Humid;
use App\Form\CustomFormValidators\DHTHumidityConstraint;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DHTHumidCardModalForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('highSensorReading', TextType::class, [
                'required' => true,
                'constraints' => [
                   new DHTHumidityConstraint(),
                    new NotBlank(['message' => 'High Humidity Cannot be Blank']),
                ],
            ])

            ->add('lowSensorReading', TextType::class, [
                'required' => true,
                'constraints' => [
                    new DHTHumidityConstraint(),
                    new NotBlank(),
                ],
            ])

            ->add('constrecord', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Const Record Cannot be Blank']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Humid::class,
        ]);
    }
}