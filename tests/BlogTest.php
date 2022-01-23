<?php

namespace App\Tests;

use App\Tests\DatabasePrimer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlogTest extends KernelTestCase
{

    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime(self::$kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testItWorks()
    {
        # code...
        $this->assertTrue(true);
    }
}
