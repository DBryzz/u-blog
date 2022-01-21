<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
