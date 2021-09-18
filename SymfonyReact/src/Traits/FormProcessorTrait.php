<?php

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

trait FormProcessorTrait
{
    private array $formInputErrors = [];

    /**
     * @param FormInterface|FormFactoryInterface $form
     * @param array $formData
     * @return bool
     */
    public function processForm(FormInterface|FormFactoryInterface $form, array $formData): bool
    {
        $form->submit($formData);
//dd($formData);
        if ($form->isSubmitted() && !$form->isValid()) {
            $this->processFormErrors($form);
//dd('false');
            return false;
        }

//        dd($form->getData());
        return true;
    }

    /**
     * @param FormInterface $form
     */
    public function processFormErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true, true) as $error) {
            $this->formInputErrors[] = $error->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getAllFormInputErrors(): array
    {
        return $this->formInputErrors;
    }
}
