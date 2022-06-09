<?php

namespace App\Devices\Forms;

use App\Common\API\APIErrorMessages;
use App\Common\CustomValidators\NoSpecialCharactersConstraint;
use App\Devices\Entity\Devices;
use App\Form\FormMessages;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddNewDeviceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deviceName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NoSpecialCharactersConstraint(),
                    new NotBlank(['message' => sprintf(APIErrorMessages::SHOULD_NOT_BE_BLANK, 'Device')]),
                    new Length(
                        [
                            'min' => 1, 'max' => 20,
                            'minMessage' => 'Device name too short',
                            'maxMessage' => 'Device name too long'
                        ]
                    )
                ]
            ])

            ->add('groupNameObject', EntityType::class, [
                'class' => GroupNames::class,
            ])

            ->add('roomObject', EntityType::class, [
                'class' => Room::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devices::class,
            'csrf_protection' => false,
        ]);
    }
}
