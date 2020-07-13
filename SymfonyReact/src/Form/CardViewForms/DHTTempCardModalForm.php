<?php


namespace App\Form\CardViewForms;



use App\Entity\Sensors\Temp;


use App\Form\CustomFormValidators\DHTTemperatureConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DHTTempCardModalForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hightemp', TextType::class, [
                'required' => true,
                'constraints' => [
                    new DHTTemperatureConstraint(),
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 2,
                        'minMessage' => 'You must enter a value',
                        'maxMessage' => 'This number is too high {{ value }}']),
                ],
            ])
            ->add('lowtemp', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 2,
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
            'data_class' => Temp::class,
        ]);
    }
}