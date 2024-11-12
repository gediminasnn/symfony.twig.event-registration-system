<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Form\EventRegistrationType;
use App\Service\EventServiceInterface;
use App\Exception\EventFullException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/', name: 'event_list')]
    public function listEvents(EventServiceInterface $eventService): Response
    {
        $events = $eventService->listEvents();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/{id}', name: 'event_show')]
    public function showEvent(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/{id}/register', name: 'event_register')]
    public function registerForEvent(
        Request $request,
        Event $event,
        EventServiceInterface $eventService,
        LoggerInterface $logger
    ): RedirectResponse|Response {
        $registration = new EventRegistration();
        $registration->setEvent($event);

        $form = $this->createForm(EventRegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $eventService->registerForEvent($event, $registration);
                $this->addFlash('success', 'You have successfully registered for the event!');

                return $this->redirectToRoute('event_list');
            } catch (EventFullException $e) {
                $this->addFlash('danger', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An unexpected error occurred: ' . $e->getMessage());
                $logger->error('Unexpected error during event registration: ' . $e->getMessage());
            }
        }

        return $this->render('event/register.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
