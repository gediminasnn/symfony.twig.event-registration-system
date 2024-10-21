<?php

namespace App\Tests\Controller;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
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


    public function testRegisterForEventSuccess()
    {
        // Create an event with available spots
        $event = new Event();
        $event->setName('Registration Success Test');
        $event->setDate(new \DateTime('+1 month'));
        $event->setLocation('Success Location');
        $event->setAvailableSpots(10);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Request the registration page
        $crawler = $this->client->request('GET', '/event/' . $event->getId() . '/register');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Fill in the form
        $form = $crawler->selectButton('Register')->form();
        $form['event_registration[name]'] = 'John Doe';
        $form['event_registration[email]'] = 'john.doe@example.com';

        // Submit the form
        $this->client->submit($form);

        // Follow the redirect
        $this->client->followRedirect();

        // Assert that a success flash message is displayed
        $this->assertSelectorExists('.alert-success');
        $this->assertSelectorTextContains('.alert-success', 'You have successfully registered for the event!');

        // Assert that the event's available spots have decreased
        $updatedEvent = $this->entityManager->getRepository(Event::class)->find($event->getId());
        $this->assertEquals(10, $updatedEvent->getAvailableSpots());
    }

    public function testRegisterForEventEventFull()
    {
        // Create an event with no available spots
        $event = new Event();
        $event->setName('Registration Full Test');
        $event->setDate(new \DateTime('+1 month'));
        $event->setLocation('Full Location');
        $event->setAvailableSpots(0);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        // Request the registration page
        $crawler = $this->client->request('GET', '/event/' . $event->getId() . '/register');

        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Fill in the form
        $form = $crawler->selectButton('Register')->form();
        $form['event_registration[name]'] = 'Jane Doe';
        $form['event_registration[email]'] = 'jane.doe@example.com';

        // Submit the form
        $this->client->submit($form);

        // Since the exception is caught and a flash message is added, we need to check for that
        // Assert that an error flash message is displayed
        $this->assertSelectorExists('.alert-error, .alert-danger');
        $this->assertSelectorTextContains('.alert-error, .alert-danger', 'Sorry, this event is full.');

        // Assert that the event's available spots have not changed
        $updatedEvent = $this->entityManager->getRepository(Event::class)->find($event->getId());
        $this->assertEquals(0, $updatedEvent->getAvailableSpots());
    }
}
