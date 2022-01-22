<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\User;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostController extends AbstractController
{


    /**
     * @Route("/posts", name="posts")
     */
    public function index(): Response
    {
        $posts = $this->getDoctrine()->getRepository(BlogPost::class)->findAll();
        return $this->render('post/index.html.twig', [
            'blogPosts' => $posts,
        ]);
    }



    /**
     * @Route("/post/new", name="create_post")
     * Method({"GET", "POST"})
     */
    public function new(Request $request)
    {
        $post = new BlogPost();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $form = $this->createFormBuilder($post)
            ->add(
                'title',
                TextType::class,
                array(
                    'constraints' => [new NotBlank()],
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add(
                'description',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => array(
                        'Technology' => $categories[0],
                        'Education' => $categories[1],
                        'Religion' => $categories[2],
                        'Social' => $categories[3]

                    ),
                    'attr' => ['class' => 'my-4'],

                    // 'choices_as_values' => true,
                    'multiple' => false,
                    'expanded' => false,
                ]
            )
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();



        // dd($form);

        $session = $request->getSession();
        $userSession = $session->get('user');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userSession->getId());

        // dd($user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $post->prePersist();
            $post->setAuthor($user);
            $post->setCategory($post->getCategory());
            $slug = $post->getAuthor()->getId() . "-"
                . substr($post->getTitle(), 6, 8) . "-"
                . substr($post->getTitle(), 0, 2) . "-"
                . substr($post->getTitle(), 3, 5);
            $post->setSlug($slug);
            $entityManager->persist($post);
            $entityManager->flush();
            $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $post->getSlug()]);
            // dd($post);

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }

        return $this->render('post/create_post.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/post/{id}", name="show_post")
     */
    public function show($id): Response
    {
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
        return $this->render('post/show_post.html.twig', [
            'blogPost' => $post,
        ]);
    }


    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        $post = new BlogPost();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);

        $form = $this->createFormBuilder($post)
            ->add(
                'title',
                TextType::class,
                array(
                    'constraints' => [new NotBlank()],
                    'attr' => array('class' => 'form-control')
                )
            )

            ->add(
                'description',
                TextType::class,
                [
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add(
                'body',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('class' => 'form-control')
                )
            )
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => array(
                        'Technology' => $categories[0],
                        'Education' => $categories[1],
                        'Religion' => $categories[2],
                        'Social' => $categories[3]

                    ),
                    'attr' => ['class' => 'my-4'],

                    // 'choices_as_values' => true,
                    'multiple' => false,
                    'expanded' => false,
                ]
            )
            ->add('save', SubmitType::class, array(
                'label' => 'Create',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();


        $session = $request->getSession();
        $userSession = $session->get('user');
        $user = $this->getDoctrine()->getRepository(User::class)->find($userSession->getId());
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $post->getSlug()]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postModified = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $postModified->preUpdate();
            $postModified->setAuthor($user);
            $postModified->setCategory($postModified->getCategory());
            $slug = $post->getAuthor()->getId() . "-"
                . substr($post->getTitle(), 6, 8) . "-"
                . substr($post->getTitle(), 0, 2) . "-"
                . substr($post->getTitle(), 3, 5);
            $post->setSlug($slug);
            $entityManager->persist($post);
            $entityManager->flush();
            $post = $this->getDoctrine()->getRepository(BlogPost::class)->findOneBy(['slug' => $post->getSlug()]);

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }

        return $this->render('post/edit_post.html.twig', array(
            'form' => $form->createView()
        ));
    }




    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    /* public function edit(Request $request, $id)
    {
        $article = new Article();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('body', TextareaType::class, array(
                'required' => false,
                'attr' => array('class' => 'form-control')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Update',
                'attr' => array('class' => 'btn btn-primary mt-3')
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }

        return $this->render('articles/edit.html.twig', array(
            'form' => $form->createView()
        ));
    } */
}
