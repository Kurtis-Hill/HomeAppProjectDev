<?php

namespace App\User\Controller\UserControllers;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\API\CommonURL;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Forms\RegistrationForm;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(CommonURL::HOMEAPP_WEBAPP_URL_BASE . 'register', name: 'app_register', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function register(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordEncoder,
        GroupNameRepository $groupNameRepository,
        GroupNameMappingRepository $groupNameMappingRepository,
        UserRepository $userRepository,
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $groupName = $form->get('groupName')->getData();

                $groupNameCheck = $groupNameRepository->findOneBy(['groupName' => $groupName]);
                if ($groupNameCheck instanceof GroupNames) {
                    throw new BadRequestException($groupName.' has already been taken by another user');
                }
//                $boo;
                $groupNameObject = new GroupNames();
                $groupNameObject->setGroupName($groupName);
                $groupNameObject->setCreatedAt();

                $groupNameRepository->persist($groupNameObject);
                $groupNameRepository->flush();

                $user->setPassword(
                    $passwordEncoder->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

//                $user->setEmail($form->get('email')->getData());
//                $user->setFirstName($form->get('firstName')->getData());
//                $user->setLastName($form->get('lastName')->getData());
                $user->setRoles(['ROLE_USER']);
                $user->setGroupNameID($groupNameObject);
                $user->setCreatedAt(new DateTimeImmutable('now'));

                $errors = $validator->validate($user);
                if ($this->checkIfErrorsArePresent($errors)) {
                    throw new BadRequestException(implode(', ', $this->getValidationErrorAsArray($errors)));
                }

                $groupNameMapping = new GroupNameMapping();
                $groupNameMapping->setGroupnameid($groupNameObject);
                $groupNameMapping->setUserID($user);

                $userRepository->persist($user);
                $groupNameMappingRepository->persist($groupNameMapping);

                $userRepository->flush();
                $this->logger->info('User '.$user->getFirstName().' '.$user->getLastName().' has been registered at' . $user->getCreatedAt()?->format('Y-m-d H:i:s'));
                $this->addFlash('success', 'Registration successful!');
            } catch (BadRequestException $e) {
                $form->addError(new FormError($e->getMessage()));
            } catch (ORMException | \Exception $e) {
//                $
                $userRepository->remove($user);
                if (isset($groupNameObject)) {
                    $groupNameRepository->remove($groupNameObject);
                }
                if (isset($groupNameMapping)) {
                    $groupNameMappingRepository->remove($groupNameMapping);
                }
//                dd($e->getMessage());
                $this->logger->error($e->getMessage());
               $this->addFlash('error', 'Registration failed! Something went wrong');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
