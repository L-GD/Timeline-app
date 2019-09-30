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
    public function Form(Request $request, ObjectManager $manager, TimelineRepository $TLRepository, Event $event = null)
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$event->getId()) {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

                $id = $request->query->get('id');
                $timeline = $TLRepository->findOneBy([
                    'id' => $id
                ]);
                $event->setTimeline($timeline);

                $this->addFlash('success', 'Votre événement a été créé !');
            }
            $manager->persist($event);
            $manager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('pages/event.html.twig', [
            'form' => $form->createView(),
            'editMode' => $event->getId() !== null
        ]);


    }
}