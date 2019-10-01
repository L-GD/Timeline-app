<?php


namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\TimelineRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/new_event", name="newEvent")
     */
    public function NewEventForm(Request $request, ObjectManager $manager, TimelineRepository $timelineRepository, Event $event = null)
    {
        $event = new Event();
        $id = $request->query->get('id');

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $isFinished = $this->EventForm($manager, $request, $event, $timelineRepository, $form, $id);

        if ($isFinished) {
            return $this->redirectToRoute('selectTimeline', ['id' => $id]);
        } else {
            return $this->render('pages/event.html.twig', [
                'form' => $form->createView(),
                'editMode' => false
            ]);
        }
    }

    /**
     * @Route("/edit_event/{id}", name="editEvent")
     */
    public function EditEventForm(Request $request, ObjectManager $manager, TimelineRepository $timelineRepository, Event $event)
    {
        $id = $request->query->get('id');

        $idTL = $event->getTimeline()->getId();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $isFinished = $this->EventForm($manager, $request, $event, $timelineRepository, $form, $id);

        if ($isFinished) {
            return $this->redirectToRoute('selectTimeline', ['id' => $idTL]);
        } else {
            return $this->render('pages/event.html.twig', [
                'form' => $form->createView(),
                'editMode' => true
            ]);
        }
    }

    public function EventForm(ObjectManager $manager, Request $request, Event $event, TimelineRepository $timelineRepository, $form, $id)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$event->getId()) {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

                $timeline = $timelineRepository->findOneBy([
                    'id' => $id
                ]);
                $event->setTimeline($timeline);

                $this->addFlash('success', 'Votre événement a été créé !');
            } else {
                $this->addFlash('success', 'Votre événement a été modifié !');
            }
            $manager->persist($event);
            $manager->flush();

            return true;
        }
        return false;
    }


    /**
     * @Route("/deleteEvent/{id}", name="deleteEvent")
     */
    public function deleteEvent(ObjectManager $manager, Event $event)
    {
        $id = $event->getTimeline()->getId();
        $manager->remove($event);
        $manager->flush();
        return $this->redirectToRoute('selectTimeline', [
            'id' => $id
        ]);
    }
}