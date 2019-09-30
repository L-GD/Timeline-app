<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Timeline;
use App\Form\TimelineType;
use App\Repository\EventRepository;
use App\Repository\TimelineRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimelineController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TimelineRepository $repo)
    {
        $timelines = $repo->findAll();

        return $this->render('pages/index.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/pages/new_TL", name="newTL")
     * @Route("/pages/{id}/edit_TL", name="TL_edit")
     */
    public function Form(Request $request, ObjectManager $manager, Timeline $timeline = null)
    {
        if (!$timeline) {
            $timeline = new Timeline();
        }
        $form = $this->createForm(TimelineType::class, $timeline);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$timeline->getId()) {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
                $user = $this->getUser();
                $timeline->setCreatedAt(new \DateTime());
                $timeline->setUser($user);
                $this->addFlash('success', 'Votre chronologie a été créée !');
            } else {
                $this->addFlash('success', 'Votre chronologie a été modifiée !');
            }
            $manager->persist($timeline);
            $manager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('pages/selectTimeline.html.twig', [
            'form' => $form->createView(),
            'editMode' => $timeline->getId() !== null
        ]);
    }

    /**
     * @Route("/show", name="list")
     */
    public function list(TimelineRepository $timeline)
    {
        $user = $this->getUser()->getId();
        $timelines = $timeline->findBy([
            'user' => $user
        ]);

        return $this->render('/pages/show.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/delete/{id}", name="removeTL")
     */
    public function delete(ObjectManager $manager, TimelineRepository $timeline)
    {
        $manager->remove($timeline);
        $manager->flush();

        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/select/{id}", name="selectTimeline")
     */
    public function selectTimeline(Timeline $timeline, EventRepository $eventRepository)
    {
        $events = $eventRepository->findBy([
            'timeline' => $timeline
        ]);

        return $this->render('pages/content.html.twig', [
            'timeline' => $timeline,
            'events' => $events
        ]);
    }
}
