<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRendererEngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;



class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('home');
         }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();
            
            // do anything else you need here, like send an email
            // generate a signed url and email it to the user
            // $this->emailVerifier->send Email Confirmation ('app_verify_email', $user,
            // (new TemplatedEmail())
            // ->from (new Address('admin@security-demo.com', 'Security'))
            // ->to($user->getEmail())
            // ->subject( 'Please Confirm your Email')
            // ->htmlTemplate('registration/confirmation_email.html.twig')
            // );

            // $email = (new Email())
            // ->from(new Address('admin@security-demo.com', 'Security'))
            // ->to($user->getEmail())
            // ->subject('Please Confirm your Email')
            // ->htmlTemplate('registration/confirmation_email.html.twig');

            // $this->$mailer->send($email);

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
