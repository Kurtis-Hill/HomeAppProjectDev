<?php

namespace App\Common\Validation\Traits;

use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

trait FormProcessorTrait
{
    private array $formInputErrors = [];

    public function processForm(FormInterface|FormFactoryInterface $form, array $formData): array
    {
        $form->submit($formData);
        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->processFormErrors($form);
        }

        return [];
    }

    public function processFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $this->formInputErrors[] = $error->getMessage();
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @return array
     */
    #[Deprecated]
    public function getAllFormInputErrors(): array
    {
        return $this->formInputErrors;
    }
}
