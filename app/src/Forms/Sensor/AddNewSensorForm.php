<?php

namespace App\Forms\Sensor;

use App\CustomValidators\NoSpecialCharactersNameConstraint;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\Sensor;
use App\Services\API\APIErrorMessages;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddNewSensorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sensorName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NoSpecialCharactersNameConstraint(),
                    new NotBlank(['message' => sprintf(APIErrorMessages::SHOULD_NOT_BE_BLANK, 'Sensor name')]),
                    new Length(['min' => 1, 'max' => 20,
                        'minMessage' => 'Sensor name too short',
                        'maxMessage' => 'Sensor name too long'])
                ]
            ])
            ->add('sensorTypeID', EntityType::class, [
                'class' => AbstractSensorType::class
            ])
            ->add('deviceNameID', EntityType::class, [
                'class' => Devices::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sensor::class,
            'csrf_protection' => false,
        ]);
    }
}
