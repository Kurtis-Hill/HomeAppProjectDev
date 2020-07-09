<?php

namespace App\Form;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CardViewModalFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cardcolourid', EntityType::class, [
                'class' => Cardcolour::class,
                'constraints' => [
                    new NotBlank(),
                    ]
            ])
            ->add('cardiconid', EntityType::class, [
                'class' => Icons::class,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('cardstateid', EntityType::class, [
                'class' => Cardstate::class,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cardview::class,
            'csrf_protection' => false,
        ]);
    }
}
