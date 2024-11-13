<?php

namespace App\Controller\Event;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Form\EventRegistrationType;
use App\Service\Event\RegisterForEventServiceInterface;
use App\Exception\EventFullException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private RegisterForEventServiceInterface $registerForEventService;
    private LoggerInterface $logger;

    public function __construct(
        RegisterForEventServiceInterface $registerForEventService,
        LoggerInterface $logger
    ) {
        $this->registerForEventService = $registerForEventService;
        $this->logger = $logger;
    }

    #[Route('/event/{id}/register', name: 'event_register', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, Event $event): RedirectResponse|Response
    {
        $registration = new EventRegistration();
        $registration->setEvent($event);

        $form = $this->createForm(EventRegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->registerForEventService->register($event, $registration);
                $this->addFlash('success', 'You have successfully registered for the event!');

                return $this->redirectToRoute('event_list');
            } catch (EventFullException $e) {
                $this->addFlash('danger', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('danger', 'An unexpected error occurred: ' . $e->getMessage());
                $this->logger->error('Unexpected error during event registration: ' . $e->getMessage());
            }
        }

        return $this->render('event/register.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
