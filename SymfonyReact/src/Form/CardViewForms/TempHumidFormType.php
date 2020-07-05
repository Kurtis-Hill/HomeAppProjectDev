<?php


namespace App\Form\CardViewForms;


use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TempHumidFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //     dd($options['cardSensorState']);
        $builder

            ->add('highReading', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'High Reading Cannot be blank']),
                ]
//                'class'          => $options["sensorType"],

//                'choice_label'   => 'iconname',
//                'data'           => $options['cardIcon'],
//                'error_bubbling' => true,
//                'label'          => 'Choose Card Icon',
//
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('i')
//                        ->orderBy('i.iconname', 'ASC');
//                },
            ]);

//            ->add('lowReading', TextType::class, [
//                'class'          => Temp::class,

//                'choice_label'   => 'iconname',
//                'data'           => $options['cardIcon'],
//                'error_bubbling' => true,
//                'label'          => 'Choose Card Icon',
//
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('i')
//                        ->orderBy('i.iconname', 'ASC');
//                },
//            ]);

//            ->add('icon', TextType::class, [
////                'class'          => Cardview::class,
//
////                'choice_label'   => 'colour',
////                'data'           => $options['cardColour'],
////                'error_bubbling' => true,
////                'label'          => 'Choose Card Colour',
////
////                'query_builder' => function(EntityRepository $er) {
////                    return $er->createQueryBuilder('cc')
////                        ->orderBy('cc.colour', 'ASC');
////                },
//            ])
//
//            ->add('colour', TextType::class, [
////                'class'          => Cardview::class,
//
////                'choice_label'   => 'colour',
////                'data'           => $options['cardColour'],
////                'error_bubbling' => true,
////                'label'          => 'Choose Card Colour',
////
////                'query_builder' => function(EntityRepository $er) {
////                    return $er->createQueryBuilder('cc')
////                        ->orderBy('cc.colour', 'ASC');
////                },
//            ])
//
//            ->add('cardViewState', TextType::class, [
//
//
//            ])
//
//            ->add('constRecord', TextType::class, [
//
//
//
//
//            ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => null,
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