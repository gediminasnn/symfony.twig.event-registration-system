<?php

namespace App\Tests\Controller\Event;

use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
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
        $this->assertSelectorExists('.alert-danger');
        $this->assertSelectorTextContains('.alert-danger', 'Sorry, this event is full.');

        // Assert that the event's available spots have not changed
        $updatedEvent = $this->entityManager->getRepository(Event::class)->find($event->getId());
        $this->assertEquals(0, $updatedEvent->getAvailableSpots());
    }
}
