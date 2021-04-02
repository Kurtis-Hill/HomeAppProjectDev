<?php


namespace App\Form\SensorForms;


use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\CustomFormValidators\NoSpecialCharactersConstraint;
//use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddNewSensorForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sensorName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NoSpecialCharactersConstraint(),
                    new NotBlank(),
                    new Length(['min' => 1, 'max' => 20,
                        'minMessage' => 'Device name too short',
                        'maxMessage' => 'Device name too long'])
                ]
            ])
            ->add('sensorTypeID', EntityType::class, [
                'class' => SensorType::class
            ])
            ->add('deviceNameID', EntityType::class, [
                'class' => Devices::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sensors::class,
            'csrf_protection' => false,
        ]);
    }
}
