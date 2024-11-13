<?php

namespace App\Tests\Controller\Event;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShowControllerTest extends WebTestCase
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

    public function testShowEvent()
    {
        // Create an event in the database
        $event = new Event();
        $event->setName('Event Details Test');
        $event->setDate(new \DateTime('+1 week'));
        $event->setLocation('Test Location');
        $event->setAvailableSpots(20);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Request the event detail page
        $this->client->request('GET', '/event/' . $event->getId());

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the page contains the event details
        $this->assertSelectorTextContains('h1', 'Event Details Test');

        // Get the crawler
        $crawler = $this->client->getCrawler();

        // Assert the date
        $dateText = $crawler->filter('.card-text')->eq(0)->text();
        $expectedDateText = 'Date: ' . $event->getDate()->format('Y-m-d H:i');
        $this->assertSame($expectedDateText, $dateText);

        // Assert the location
        $locationText = $crawler->filter('.card-text')->eq(1)->text();
        $expectedLocationText = 'Location: ' . $event->getLocation();
        $this->assertSame($expectedLocationText, $locationText);

        // Assert the available spots
        $spotsText = $crawler->filter('.card-text')->eq(2)->text();
        $expectedSpotsText = 'Available Spots: ' . $event->getAvailableSpots();
        $this->assertSame($expectedSpotsText, $spotsText);
    }
}
