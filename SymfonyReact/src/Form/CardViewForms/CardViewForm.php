<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form\CardViewForms;

use App\User\Entity\UserInterface\Card\CardColour;
use App\User\Entity\UserInterface\Card\Cardstate;
use App\User\Entity\UserInterface\Card\CardView;
use App\User\Entity\UserInterface\Icons;
use App\Form\FormMessages;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

//CSRF tokens are off, there is an end point that kind of defeates the prupose of them, implenting better methods later
class CardViewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardColourID', EntityType::class, [
                'class' => CardColour::class,
                'constraints' => [
                    new NotBlank(['message' => sprintf(FormMessages::SHOULD_NOT_BE_BLANK, 'card colour')]),
                ],
            ])
            ->add('cardIconID', EntityType::class, [
                'class' => Icons::class,
                'constraints' => [
                    new NotBlank(['message' => sprintf(FormMessages::SHOULD_NOT_BE_BLANK, 'card icon')]),
                ],
            ])
            ->add('cardStateID', EntityType::class, [
                'class' => Cardstate::class,
                'constraints' => [
                    new NotBlank(['message' => sprintf(FormMessages::SHOULD_NOT_BE_BLANK, 'card state')]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CardView::class,
            'csrf_protection' => false,
        ]);
    }
}
