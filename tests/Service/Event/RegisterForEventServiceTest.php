<?php

namespace App\Tests\Service\Event;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Exception\EventFullException;
use App\Repository\EventRepositoryInterface;
use App\Repository\EventRegistrationRepositoryInterface;
use App\Service\Event\RegisterForEventService;
use App\Service\Event\RegisterForEventServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegisterForEventServiceTest extends TestCase
{
    /** @var EventRepositoryInterface&MockObject */
    private $eventRepositoryMock;

    /** @var EventRegistrationRepositoryInterface&MockObject */
    private $registrationRepositoryMock;

    private RegisterForEventServiceInterface $registerForEventService;

    protected function setUp(): void
    {
        $this->eventRepositoryMock = $this->createMock(EventRepositoryInterface::class);
        $this->registrationRepositoryMock = $this->createMock(EventRegistrationRepositoryInterface::class);

        $this->registerForEventService = new RegisterForEventService(
            $this->eventRepositoryMock,
            $this->registrationRepositoryMock
        );
    }

    public function testRegisterSuccessfullyDecreasesAvailableSpots(): void
    {
        // Arrange: Create an event with available spots
        $event = new Event();
        $event->setName('Test Event');
        $event->setAvailableSpots(5);

        // Capture the original available spots
        $originalAvailableSpots = $event->getAvailableSpots();

        // Create a registration
        $registration = new EventRegistration();
        $registration->setName('John Doe');
        $registration->setEmail('john.doe@example.com');
        $registration->setEvent($event);

        // Expect the save methods to be called with updated event and registration
        $this->eventRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Event $savedEvent) use ($originalAvailableSpots) {
                return $savedEvent->getAvailableSpots() === ($originalAvailableSpots - 1);
            }));

        $this->registrationRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo($registration));

        // Act: Execute the service method
        $this->registerForEventService->register($event, $registration);

        // Assert: Verify that the available spots have decreased
        $this->assertEquals($originalAvailableSpots - 1, $event->getAvailableSpots());
    }


    public function testRegisterThrowsEventFullExceptionWhenNoSpotsAvailable(): void
    {
        // Arrange: Create an event with no available spots
        $event = new Event();
        $event->setName('Full Event');
        $event->setAvailableSpots(0);

        // Create a registration
        $registration = new EventRegistration();
        $registration->setName('Jane Doe');
        $registration->setEmail('jane.doe@example.com');
        $registration->setEvent($event);

        // Expect that save methods are never called
        $this->eventRepositoryMock
            ->expects($this->never())
            ->method('save');

        $this->registrationRepositoryMock
            ->expects($this->never())
            ->method('save');

        // Expect the EventFullException to be thrown
        $this->expectException(EventFullException::class);
        $this->expectExceptionMessage('Sorry, this event is full.');

        // Act: Execute the service method, expecting an exception
        $this->registerForEventService->register($event, $registration);
    }
}
