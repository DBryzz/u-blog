<?php

namespace App\Tests;

use App\Entity\User;
use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserDatabaseTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function a_user_record_can_be_created_in_the_database()
    {
        // Set up
        $user = new User();

        $user->setName('John Doe');
        $user->setUsername('johndoe');
        $user->setRole('VIEWER');
        $user->setContact('+237 6 70 00 00 00');
        $user->setCompany('MIA ltd');
        $user->setShortBio('lorem Ipsum and more.....');
        $user->setEmail('john.doe@email.com');
        $user->setFacebook('johnFB');
        $user->setGithub('johnGH');
        $user->setTwitter('johnTT');

        $this->entityManager->persist($user);

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
}
