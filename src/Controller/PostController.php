<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use PhpParser\Node\Expr\Cast\Object_;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostController extends AbstractController
{


    /**
     * @Route("/posts", name="posts")
     */
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        if ($session->get('user') == null) {
            $session = new Session();
            $session->set('user', new User());
        }

        $posts = $this->getDoctrine()->getRepository(BlogPost::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('post/index.html.twig', [
            'blogPosts' => $posts, 'categories' => $categories
        ]);
    }

    /**
     * @Route("/posts/category/{id}", name="category_posts")
     */
    public function getCategoryPosts(Request $request, $id): Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $session = $request->getSession();

        if ($session->get('user') == null) {
            $session = new Session();
            $session->set('user', new User());
        }

        $posts = $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['category' => $category]);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('post/index.html.twig', [
            'blogPosts' => $posts, 'categories' => $categories
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

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('post/create_post.html.twig', array(
            'form' => $form->createView(), 'categories' => $categories
        ));
    }

    /**
     * @Route("/post/{id}", name="show_post")
     */
    public function show($id): Response
    {
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['blogPost' => $post]);

        return $this->render('post/show_post.html.twig', [
            'blogPost' => $post, 'categories' => $categories, 'comments' => $comments
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

        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('post/edit_post.html.twig', array(
            'form' => $form->createView(), 'categories' => $categories
        ));
    }


    /**
     * @Route("/post/delete/{id}", name="delete_post")
     * Method({"DELETE})
     */
    public function delete($id)
    {
        $post = new BlogPost();
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
        $comments = $this->getDoctrine()->getRepository(Comment::class)->findBy(['blogPost' => $post]);
        foreach ($comments as $comment) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }


    /**
     * @Route("/post/{id}/comment", name="comment_post")
     * Method({"POST"})
     */
    public function comment(Request $request, $id): Response
    {
        $session = $request->getSession()->get('user');
        $comment = new Comment();

        $body = $request->request->get('body');
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
        $commenter = $this->getDoctrine()->getRepository(User::class)->find($session->getId());

        $comment->setBody($body);
        $comment->setAuthor($commenter);
        $comment->setBlogPost($post);
        $comment->prePersist();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($comment);
        $entityManager->flush();


        return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
    }
}
