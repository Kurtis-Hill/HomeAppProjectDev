<?php


namespace App\Form\CardViewForms;


use App\Entity\Sensors\Analog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SoilFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('highanalog', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 5,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])

            ->add('lowanalog', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 5,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])

            ->add('constrecord', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Analog::class,
        ]);
    }

}