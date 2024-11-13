<?php

namespace App\Tests\Controller\Event;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // Close the entity manager and connection
        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function testListEvents()
    {
        // Create some events in the database
        $event1 = new Event();
        $event1->setName('Event 1');
        $event1->setDate(new \DateTime('+1 day'));
        $event1->setLocation('Location 1');
        $event1->setAvailableSpots(100);

        $event2 = new Event();
        $event2->setName('Event 2');
        $event2->setDate(new \DateTime('+2 days'));
        $event2->setLocation('Location 2');
        $event2->setAvailableSpots(50);

        $this->entityManager->persist($event1);
        $this->entityManager->persist($event2);
        $this->entityManager->flush();

        // Request the event list page
        $crawler = $this->client->request('GET', '/');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the page contains the events
        $this->assertSelectorTextContains('h1', 'Available Events');

        // Check that both events are listed
        $this->assertSelectorTextContains('table', 'Event 1');
        $this->assertSelectorTextContains('table', 'Event 2');
    }
}
