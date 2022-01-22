<?php

namespace App\DataFixtures\ORM;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Fixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        # code...
        $user = new User();

        $user
            ->setName('Domou Brice')
            ->setUsername('dbryzz')
            ->setRole('AUTHOR')
            ->setEmail('me@email.com')
            ->setCompany('uTrains')
            ->setShortBio('Lorem Ipsum is simply placeholder text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard placeholder text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages.')
            ->setContact('(+237) 6 70 00 00 00')
            ->setFacebook('joebloggs')
            ->setTwitter('joe.bloggs')
            ->setGithub('joe-bloggs');
        $manager->persist($user);


        $category1 = new Category();
        $category1
            ->setTitle('TECHNOLOGY')
            ->setDescription('Something on tech...');
        $manager->persist($category1);

        $category2 = new Category();
        $category2
            ->setTitle('EDUCATION')
            ->setDescription('Something on education...');
        $manager->persist($category2);

        $category3 = new Category();
        $category3
            ->setTitle('RELIGION')
            ->setDescription('Something on religion...');
        $manager->persist($category3);

        $category4 = new Category();
        $category4
            ->setTitle('SOCIAL')
            ->setDescription('Something on social...');
        $manager->persist($category4);


        $blogPost1 = new BlogPost();
        $blogPost1
            ->setTitle('First blog post example')
            ->setSlug('first-post')
            ->setDescription('Lorem Ipsum is simply placeholder text of the printing and type setting industry. Lorem Ipsum has been the industry\'s standard placeholder text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.')
            ->setBody('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.')
            ->setAuthor($user)
            ->setCategory($category1)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
        $manager->persist($blogPost1);

        $blogPost2 = new BlogPost();
        $blogPost2
            ->setTitle('Second blog post example')
            ->setSlug('second-post')
            ->setDescription('Lorem Ipsum is simply placeholder text of the printing and type setting industry. Lorem Ipsum has been the industry\'s standard placeholder text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.')
            ->setBody('Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.')
            ->setAuthor($user)
            ->setCategory($category4)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime());
        $manager->persist($blogPost2);

        $manager->flush();
    }
}
