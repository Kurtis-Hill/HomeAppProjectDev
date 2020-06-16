<?php

namespace App\Form;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Sensors\Temp;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardViewFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
   //     dd($options['cardSensorState']);
        $builder
            ->add('icon', EntityType::class, [
                'class'          => Icons::class,
//                'choice_label'   => 'iconname',
//                'data'           => $options['cardIcon'],
//                'error_bubbling' => true,
//                'label'          => 'Choose Card Icon',
//
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('i')
//                        ->orderBy('i.iconname', 'ASC');
//                },
            ])

            ->add('colour', EntityType::class, [
                'class'          => Cardcolour::class,
//                'choice_label'   => 'colour',
//                'data'           => $options['cardColour'],
//                'error_bubbling' => true,
//                'label'          => 'Choose Card Colour',
//
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('cc')
//                        ->orderBy('cc.colour', 'ASC');
//                },
            ])

            ->add('state', ChoiceType::class, [
                'mapped' => false,
//                'label' => 'Card View For Temperature',
//                'data' => $options['cardSensorStateOne'],
//                'choices' => [
//                    'Show' => 'on',
//                    'Hide' => 'off',
//                ],
            ])
        ;
//        if ($options['sensorType'] == 'Temp&Humid') {
//            $builder
//                ->add('cardSensorStateTwo', ChoiceType::class, [
//                    'mapped' => false,
////                    'label' => 'Card View For Humidity',
////                    'data' => $options['cardSensorStateTwo'],
////                    'choices'  => [
////                        'Show' => 'on',
////                        'Hide' => 'off',
////                    ],
//                ]);
//        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Cardview::class,
//            'cardIcon' => $resolver->setRequired('cardIcon'),
//            'cardColour' => $resolver->setRequired('cardColour'),
//            'cardSensorStateOne' => $resolver->setRequired('cardSensorStateOne'),
//            'cardSensorStateTwo' => $resolver->setDefault('cardSensorStateTwo', null),
//            'sensorType' => $resolver->setRequired('sensorType'),
        ]);

//        if ($options['sensorType'] == 'Temp&Humid') {
//            $resolver->setDefaults([
//                'cardSensorStateTwo' => $resolver->setRequired('cardSensorStateTwo'),
//            ]);
//        }
    }
}
