<?php


namespace App\Form\CardViewForms;


use App\Entity\Sensors\Temp;
use App\Form\CustomFormValidators\DallasTemperatureConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DallasTempCardModalForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('highReading', TextType::class, [
                'required' => true,
                'constraints' => [
                    new DallasTemperatureConstraint(),
                    new NotBlank(),
                ],
            ])

            ->add('lowReading', TextType::class, [
                'required' => true,
                'constraints' => [
                    new DallasTemperatureConstraint(),
                    new NotBlank(),
                ],
            ])

            ->add('constRecord', TextType::class, [
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
