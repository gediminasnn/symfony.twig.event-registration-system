<?php

namespace App\Tests\Repository;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventRepositoryTest extends KernelTestCase
{
    private EventRepository $eventRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->eventRepository = static::getContainer()->get(EventRepository::class);
    }

    public function testSaveAndFindAll()
    {
        // Create a new Event
        $event = new Event();
        $event->setName('Test Event');
        $event->setDate(new \DateTime('2023-01-01'));
        $event->setLocation('Test Location');
        $event->setAvailableSpots(100);

        // Save the Event
        $this->eventRepository->save($event);

        // Retrieve all events
        $events = $this->eventRepository->findAll();

        // Assert that there is one event in the database
        $this->assertCount(1, $events);

        // Assert that the event's properties are as expected
        $this->assertSame('Test Event', $events[0]->getName());
        $this->assertEquals(new \DateTime('2023-01-01'), $events[0]->getDate());
        $this->assertSame('Test Location', $events[0]->getLocation());
        $this->assertSame(100, $events[0]->getAvailableSpots());
    }

    public function testSaveUpdatesExistingEvent()
    {
        // Create and save a new Event
        $event = new Event();
        $event->setName('Original Event');
        $event->setDate(new \DateTime('2023-01-01'));
        $event->setLocation('Original Location');
        $event->setAvailableSpots(50);
        $this->eventRepository->save($event);

        // Update event properties
        $event->setName('Updated Event');
        $event->setLocation('Updated Location');
        $event->setAvailableSpots(25);

        // Save the updated Event
        $this->eventRepository->save($event);

        // Retrieve the event by ID
        $updatedEvent = $this->eventRepository->find($event->getId());

        // Assert that the event's properties have been updated
        $this->assertSame('Updated Event', $updatedEvent->getName());
        $this->assertSame('Updated Location', $updatedEvent->getLocation());
        $this->assertSame(25, $updatedEvent->getAvailableSpots());
    }
}
