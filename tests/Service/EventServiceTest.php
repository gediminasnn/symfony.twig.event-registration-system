<?php

namespace App\Tests\Service;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Exception\EventFullException;
use App\Repository\EventRepositoryInterface;
use App\Repository\EventRegistrationRepositoryInterface;
use App\Service\EventService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EventServiceTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private $eventRepositoryMock;

    /** @var EventRegistrationRepositoryInterface&MockObject */
    private $registrationRepositoryMock;

    private EventService $eventService;

    protected function setUp(): void
    {
        $this->eventRepositoryMock = $this->createMock(EventRepositoryInterface::class);
        $this->registrationRepositoryMock = $this->createMock(EventRegistrationRepositoryInterface::class);

        $this->eventService = new EventService(
            $this->eventRepositoryMock,
            $this->registrationRepositoryMock
        );
    }

    public function testListEvents()
    {
        // Create two Event entities
        $event1 = new Event();
        $event1->setName('Event 1');

        $event2 = new Event();
        $event2->setName('Event 2');

        // Expect the findAll method of the repository to be called once and return the two events
        $this->eventRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$event1, $event2]);

        // Call the method under test
        $events = $this->eventService->listEvents();

        // Assert that two events were returned and they match the expected objects
        $this->assertCount(2, $events);
        $this->assertSame($event1, $events[0]);
        $this->assertSame($event2, $events[1]);
    }

    public function testRegisterForEventSuccess()
    {
        // Create an Event with available spots
        $event = new Event();
        $event->setAvailableSpots(5);

        // Create an EventRegistration entity
        $registration = new EventRegistration();
        $registration->setName('John Doe');
        $registration->setEmail('john.doe@example.com');
        $registration->setEvent($event);

        // Calculate the expected available spots after registration
        $expectedAvailableSpots = $event->getAvailableSpots() - 1;

        // Expect the event repository's save method to be called once with the updated available spots
        $this->eventRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Event $savedEvent) use ($expectedAvailableSpots) {
                return $savedEvent->getAvailableSpots() === $expectedAvailableSpots;
            }));

        // Expect the registration repository's save method to be called once with the correct registration
        $this->registrationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($registration));

        // Call the method under test
        $this->eventService->registerForEvent($event, $registration);

        // Assert that the available spots on the event were correctly reduced
        $this->assertEquals($expectedAvailableSpots, $event->getAvailableSpots());
    }

    public function testRegisterForEventEventFull()
    {
        // Create an Event with zero available spots
        $event = new Event();
        $event->setAvailableSpots(0);

        // Create an EventRegistration entity
        $registration = new EventRegistration();
        $registration->setName('Jane Doe');
        $registration->setEmail('jane.doe@example.com');
        $registration->setEvent($event);

        // Ensure that the event repository's save method is never called since the event is full
        $this->eventRepositoryMock
            ->expects($this->never())
            ->method('save');

        // Ensure that the registration repository's save method is never called since the event is full
        $this->registrationRepositoryMock
            ->expects($this->never())
            ->method('save');

        // Expect the EventFullException to be thrown
        $this->expectException(EventFullException::class);

        // Call the method under test, expecting it to throw an exception
        $this->eventService->registerForEvent($event, $registration);
    }
}
