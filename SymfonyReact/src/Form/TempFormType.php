<?php

namespace App\Form;

use App\Entity\Sensors\Temp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TempFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tempreading')
            ->add('hightemp')
            ->add('lowtemp')
            ->add('constrecord')
            ->add('timez')
            ->add('groupnameid')
            ->add('roomid')
            ->add('sensornameid')
            ->add('cardviewid')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Temp::class,
        ]);
    }
}
