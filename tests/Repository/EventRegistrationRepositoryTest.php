<?php

namespace App\Tests\Repository;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Repository\EventRegistrationRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventRegistrationRepositoryTest extends KernelTestCase
{
    private EventRegistrationRepository $registrationRepository;
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->registrationRepository = $container->get(EventRegistrationRepository::class);
        $this->eventRepository = $container->get(EventRepository::class);
    }

    public function testSaveRegistration()
    {
        // Create and save an Event
        $event = new Event();
        $event->setName('Test Event');
        $event->setDate(new \DateTime('2023-01-01'));
        $event->setLocation('Test Location');
        $event->setAvailableSpots(100);
        $this->eventRepository->save($event);

        // Create a new EventRegistration
        $registration = new EventRegistration();
        $registration->setName('John Doe');
        $registration->setEmail('john.doe@example.com');
        $registration->setEvent($event);

        // Save the registration
        $this->registrationRepository->save($registration);

        // Retrieve registrations from the database
        $registrations = $this->registrationRepository->findAll();

        // Assert that there is one registration
        $this->assertCount(1, $registrations);

        // Assert registration details
        $this->assertSame('John Doe', $registrations[0]->getName());
        $this->assertSame('john.doe@example.com', $registrations[0]->getEmail());
        $this->assertSame($event->getId(), $registrations[0]->getEvent()->getId());
    }

    public function testSaveMultipleRegistrations()
    {
        // Create and save an Event
        $event = new Event();
        $event->setName('Test Event');
        $event->setDate(new \DateTime('2023-01-01'));
        $event->setLocation('Test Location');
        $event->setAvailableSpots(100);
        $this->eventRepository->save($event);

        // Create multiple registrations
        $registration1 = new EventRegistration();
        $registration1->setName('John Doe');
        $registration1->setEmail('john.doe@example.com');
        $registration1->setEvent($event);

        $registration2 = new EventRegistration();
        $registration2->setName('Jane Smith');
        $registration2->setEmail('jane.smith@example.com');
        $registration2->setEvent($event);

        // Save registrations
        $this->registrationRepository->save($registration1);
        $this->registrationRepository->save($registration2);

        // Retrieve registrations
        $registrations = $this->registrationRepository->findAll();

        // Assert that there are two registrations
        $this->assertCount(2, $registrations);
    }
}
