<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserDatabaseTest extends KernelTestCase
{
    private $entityManager;
    private $user;
    private $user1;


    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();


        $this->user = new User();

        $this->user->setName('John Doe');
        $this->user->setUsername('johndoe');
        $this->user->setRole('VIEWER');
        $this->user->setEmail('john.doe@email.com');
        $this->user->setContact('+237 6 70 00 00 00');
        $this->user->setCompany('MIA ltd');
        $this->user->setShortBio('lorem Ipsum and more.....');
        $this->user->setFacebook('johnFB');
        $this->user->setGithub('johnGH');
        $this->user->setTwitter('johnTT');

        $this->user1 = $this->user;

        $this->user1->setContact('');
        $this->user1->setCompany('');
        $this->user1->setShortBio('');
        $this->user1->setFacebook('');
        $this->user1->setGithub('');
        $this->user1->setTwitter('');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }



    public function test_a_user_record_can_be_created_in_the_database()
    {
        // Set up
        $this->entityManager->persist($this->user);

        // Do something
        $this->entityManager->flush();

        $userRepo = $this->entityManager->getRepository(User::class);

        $userRecord = $userRepo->findOneBy(['username' => 'johndoe']);

        // Make assertions
        $this->assertEquals($userRecord->getName(), 'John Doe');
        $this->assertEquals($userRecord->getUsername(), 'johndoe');
        $this->assertEquals($userRecord->getRole(), 'VIEWER');
        $this->assertEquals($userRecord->getEmail(), 'john.doe@email.com');
        $this->assertEquals($userRecord->getCompany(), 'MIA ltd');
        $this->assertEquals($userRecord->getShortBio(), 'lorem Ipsum and more.....');
        $this->assertEquals($userRecord->getContact(), '+237 6 70 00 00 00');
        $this->assertEquals($userRecord->getFacebook(), 'johnFB');
        $this->assertEquals($userRecord->getTwitter(), 'johnTT');
        $this->assertEquals($userRecord->getGithub(), 'johnGH');
    }

    public function test_record_is_created_in_database_even_when_nullable_fields_are_not_set()
    {
        // Set up
        $this->entityManager->persist($this->user1);

        // Do Something
        $this->entityManager->flush();

        $userRepo = $this->entityManager->getRepository(User::class);

        $userRecord = $userRepo->findOneBy(['username' => 'johndoe']);

        dd($userRecord->getCompany());

        // Make assertions
        $this->assertEquals($userRecord->getName(), 'John Doe');
        $this->assertEquals($userRecord->getUsername(), 'johndoe');
        $this->assertEquals($userRecord->getRole(), 'VIEWER');
        $this->assertEquals($userRecord->getEmail(), 'john.doe@email.com');
        $this->assertEquals($userRecord->getCompany(), '');
        $this->assertEquals($userRecord->getShortBio(), '');
        $this->assertEquals($userRecord->getContact(), '');
        $this->assertEquals($userRecord->getFacebook(), '');
        $this->assertEquals($userRecord->getTwitter(), '');
        $this->assertEquals($userRecord->getGithub(), '');
    }
}
