<?php

namespace App\Controller\User\UserControllers;

use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupValidationException;
use App\Exceptions\User\UserExceptions\UserCreationValidationErrorsException;
use App\Forms\User\RegistrationForm;
use App\Services\API\CommonURL;
use App\Services\User\User\UserCreationHandler;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(
        CommonURL::HOMEAPP_WEBAPP_URL_BASE . 'register',
        name: 'app_register',
        methods: [Request::METHOD_POST, Request::METHOD_GET]
    )]
    public function register(
        Request $request,
        UserCreationHandler $userCreationHandler,
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userCreationHandler->handleNewUserCreation(
                    $form->get('firstName')->getData(),
                    $form->get('lastName')->getData(),
                    $form->get('email')->getData(),
                    $form->get('groupName')->getData(),
                    $form->get('plainPassword')->getData(),
                    $form->get('profilePicture')->getData(),
                );
                $this->addFlash('success', 'Registration successful!');
            } catch (BadRequestException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (UniqueConstraintViolationException $e) {
                $form->addError(new FormError('Email already exists!'));
            } catch (UserCreationValidationErrorsException|GroupValidationException $e) {
                foreach ($e->getValidationErrors() as $error) {
                    $form->addError(new FormError($error));
                }
            } catch (ORMException|Exception $e) {
                $this->logger->error($e->getMessage());
                $this->addFlash('error', 'Registration failed! Something went wrong');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
