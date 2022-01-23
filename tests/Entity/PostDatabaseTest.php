<?php

namespace App\Tests\Entity;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\User;
use App\Tests\DatabasePrimer;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostDatabaseTest extends KernelTestCase
{
    private $entityManager;
    private $user;
    private $blogPost;
    private $category;



    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        // $container = $kernel->getContainer();
        // $this->user = $this->getMockBuilder(User::class);

        $this->user = new User();
        $this->user->setName('John Doe');
        $this->user->setUsername('johndoe');
        $this->user->setRole('VIEWER');
        $this->user->setEmail('john.doe@email.com');

        $this->entityManager->persist($this->user);

        $this->category = new Category();
        $this->category->setTitle('Category title');

        $this->entityManager->persist($this->category);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }



    public function test_a_post_record_can_be_created_in_the_database()
    {
        // Set up
        $userRecord = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'johndoe']);
        $categoryRecord = $this->entityManager->getRepository(Category::class)->findOneBy(['title' => 'Category title']);

        $this->blogPost = new BlogPost();

        $this->blogPost->setTitle('Second blog blogPost example');
        $this->blogPost->setSlug('second-blogPost');
        $this->blogPost->setDescription('Lorem Ipsum is simply placeholder text of the printing and type setting industry. Lorem Ipsum has been the industry\'s standard placeholder text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.');
        $this->blogPost->setBody('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.');
        $this->blogPost->setAuthor($userRecord);
        $this->blogPost->setCategory($categoryRecord);
        $this->blogPost->setCreatedAt(new DateTime());
        $this->blogPost->setUpdatedAt(new DateTime());

        $this->entityManager->persist($this->blogPost);

        // Do something
        $this->entityManager->flush();

        $postRepo = $this->entityManager->getRepository(BlogPost::class);

        $postRecord = $postRepo->findOneBy(['slug' => 'second-blogPost']);

        // Make assertions
        $this->assertEquals($postRecord->getTitle(), 'Second blog blogPost example');
        $this->assertEquals($postRecord->getAuthor()->getName(), $this->user->getName());
        $this->assertEquals($postRecord->getCategory(), $this->category);
    }
}
