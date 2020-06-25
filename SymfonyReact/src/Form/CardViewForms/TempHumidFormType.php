<?php


namespace App\Form\CardViewForms;

use App\Entity\Card\Cardview;
use App\Entity\Sensors\Humid;
use App\Entity\Sensors\Temp;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TempHumidFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //     dd($options['cardSensorState']);
        $builder

            ->add('highReading', EntityType::class, [
                'class'          => Temp::class,
                'mapped' => false,
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

            ->add('lowReading', EntityType::class, [
                'class'          => Temp::class,
                'mapped' => false,
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

            ->add('icon', EntityType::class, [
                'class'          => Cardview::class,
                'mapped' => false,
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

            ->add('colour', EntityType::class, [
                'class'          => Cardview::class,
                'mapped' => false,
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

            ->add('cardViewState', EntityType::class, [
                'class'          => Cardview::class,
                'mapped' => false,
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

            ->add('constRecord', EntityType::class, [
                'class'  => Cardview::class,
                'mapped' => false,
//                'label' => 'Card View For Temperature',
//                'data' => $options['cardSensorStateOne'],
//                'choices' => [
//                    'Show' => 'on',
//                    'Hide' => 'off',
//                ],
            ])

            ->add('secondConstRecord', EntityType::class, [
                'class'  => Cardview::class,
                'mapped' => false,

            ])

            ->add('state', EntityType::class, [
                'class'          => Cardview::class,
                'mapped' => false,
//                'label' => 'Card View For Temperature',
//                'data' => $options['cardSensorStateOne'],
//                'choices' => [
//                    'Show' => 'on',
//                    'Hide' => 'off',
//                ],
            ]);
        if($options['sensorType'] === "TempHumid") {
            $builder
                ->add('secondHighReading', EntityType::class, [
                    'class'          => Humid::class,
                    'mapped' => false,
                ])
                ->add('secondLowReading', EntityType::class, [
                    'class'          => Humid::class,
                    'mapped' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Cardview::class,
            'sensorType' => null,
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