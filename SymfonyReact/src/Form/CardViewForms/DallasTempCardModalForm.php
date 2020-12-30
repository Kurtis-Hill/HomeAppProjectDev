<?php


namespace App\Form\CardViewForms;



use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Form\CustomFormValidators\DallasTemperatureConstraint;
use App\Form\CustomFormValidators\NoSpecialCharactersContraint;
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
                    new NoSpecialCharactersContraint(),
                    new NotBlank(),
                ],
            ])

            ->add('lowReading', TextType::class, [
                'required' => true,
                'constraints' => [
                    new DallasTemperatureConstraint(),
                    new NoSpecialCharactersContraint(),
                    new NotBlank(),
                ],
            ])

            ->add('constRecord', TextType::class, [
                'required' => false,
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
//            'data_class' => Temperature::class,
        ]);
    }
}
