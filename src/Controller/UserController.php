<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/user/create", name="register")
     * Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        $user = new User();
        $form = $this->createFormBuilder($user)

            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'role',
                ChoiceType::class,
                [
                    'choices' => array(
                        ' Author ' => 'AUTHOR',
                        ' Viewer ' => 'VIEWER'
                    ),
                    'attr' => ['class' => 'my-2'],

                    // 'choices_as_values' => true, 
                    'multiple' => false,
                    'expanded' => true
                ]
            )
            ->add(
                'company',
                TextType::class,
                [
                    'constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'shortBio',
                TextareaType::class,
                [
                    'constraints' => [new NotBlank()],
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'contact',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false
                ]
            )
            ->add(
                'facebook',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false
                ]
            )
            ->add(
                'twitter',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control'],
                    'required' => false
                ]
            )
            ->add(
                'github',
                TextType::class,
                [
                    'attr' => ['class' => 'form-control col-xs-2'],
                    'required' => false
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => ['class' => 'form-control btn-primary pull-right'],
                    'label' => 'Become an author!'
                ]
            )
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('posts');
        }

        return $this->render('user/create_user.html.twig', array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/user/login", name="login")
     * Method({"POST"})
     */
    public function login(Request $request)
    {
        // retrieve messages
        /*  foreach ($session->getFlashBag()->get('notice', []) as $message) {
            echo '<div class="flash-notice">' . $message . '</div>';
        } */

        $username = $request->request->get('username');
        $session = new Session();
        $session->start();

        $user = new User();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        // dd($user);
        $msg = "";

        if ($user == null) {
            $msg = "User does not exist, please create an account";
            $session->getFlashBag()->add('error', $msg);
            return $this->redirectToRoute('posts');
        }


        // set and get session attributes
        $session->set('user', $user);
        // dd($session->get('user'));
        $msg = "Logged in";

        $session->getFlashBag()->add('success', $msg);
        return $this->redirectToRoute('posts');
    }

    /**
     * @Route("/user/logout", name="logout")
     * Method({"GET"})
     */
    public function logout(Request $request)
    {
        $session = $request->getSession();
        // dd($session->get('user'));

        $session->invalidate();

        $msg = "";

        $session = new Session();
        $session->set('user', new User());
        // dd($session->get('user'));
        $msg = "Logged out";

        $session->getFlashBag()->add('success', $msg);
        return $this->redirectToRoute('posts');
    }
}
