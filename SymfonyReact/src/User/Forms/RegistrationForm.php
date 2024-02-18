<?php

namespace App\User\Forms;

use App\Common\CustomValidators\NoSpecialCharactersNameConstraint;
use App\Common\CustomValidators\NotNumericConstraint;
use App\User\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class RegistrationForm  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                    new NoSpecialCharactersNameConstraint(),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'First Name',
                ],
                'label' => false,
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                    new NoSpecialCharactersNameConstraint(),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Last Name',
                ],
                'label' => false,
            ])
            ->add('email', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email name',
                    ]),
                    new Email(),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Email',
                ],
                'label' => false,
            ])
            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4000k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (jpeg, jpg, png)',
                    ]),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'invalid_message' => 'The password fields must match.',
                'first_options'  => ['label' => false],
                'second_options' => ['label' => false],
                'type' => PasswordType::class,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 50,
                    ]),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                ],
                'label' => false,
            ])
//            ->add('groupID', EntityType::class, [
//                'label' => 'Please Select Group Name',
//                'class' => GroupNames::class,
//                'required' => 'true',
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Please enter your first name',
//                    ]),
//                ],
//                'query_builder' => function (GroupNameRepository $er) {
//                    return $er->createQueryBuilder('g')
//                        ->orderBy('g.groupName', 'ASC');
//                },
//                'error_bubbling' => true,
//                'mapped' => false,
//                'attr' => [
//                    'class' => 'form-control form-control-user',
//                ],
//            ])
            ->add('groupName', TextType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                    new Type([
                        'type' => 'string',
                        'message' => 'The value {{ value }} is not a valid {{ type }}.',
                    ]),
                    new NotNumericConstraint(),
                ],
                'error_bubbling' => true,
                'attr' => [
                    'class' => 'form-control form-control-user',
                    'placeholder' => 'Enter a Group Name',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Register Account',
                'attr' => [
                    'class' => 'btn btn-primary btn-user btn-block',
                ],
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
