<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Entity\Category;
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

        $colour = array([
            'pwet1',
            'pwet2'
        ]);
        dump($colour);

        $pwet = new Category();
        $pwet->color = ['red', 'blue', 'green'];
        dump($pwet);

        return $this->render('pages/index.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/pages/new_TL", name="newTimeline")
     */
    public function NewTimelineForm(Request $request, ObjectManager $manager, Timeline $timeline = null)
    {
        $timeline = new Timeline();

        $form = $this->createForm(TimelineType::class, $timeline);
        $form->handleRequest($request);
        $isFinished = $this->TimelineForm($manager, $timeline, $form);

        if ($isFinished) {
            return $this->redirectToRoute('timelineList');
        } else {
            return $this->render('pages/timeline.html.twig', [
                'form' => $form->createView(),
                'editMode' => false
            ]);
        }
    }

    /**
     * @Route("/pages/{id}/edit_TL", name="editTimeline")
     */
    public function EditTimelineForm(Request $request, ObjectManager $manager, Timeline $timeline)
    {
        $form = $this->createForm(TimelineType::class, $timeline);
        $form->handleRequest($request);
        $isFinished = $this->TimelineForm($manager, $timeline, $form);

        if ($isFinished) {
            return $this->redirectToRoute('timelineList');
        } else {
            return $this->render('pages/timeline.html.twig', [
                'form' => $form->createView(),
                'editMode' => true
            ]);
        }
    }

    public function TimelineForm(ObjectManager $manager, Timeline $timeline, $form)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$timeline->getId()) {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
                $user = $this->getUser();
                $timeline->setUser($user);
                $timeline->setCreatedAt(new \DateTime());

                $this->addFlash('success', 'Votre chronologie a été créée !');
            } else {
                $this->addFlash('success', 'Votre chronologie a été modifiée !');
            }

            $manager->persist($timeline);
            $manager->flush();

            return true;
        }
        return false;
    }

    /**
     * @Route("/show", name="timelineList")
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
    public function delete(ObjectManager $manager, Timeline $timeline)
    {
        $manager->remove($timeline);
        $manager->flush();
        return $this->redirectToRoute('timelineList');
    }

    /**
     * @Route("/select/{id}", name="selectTimeline")
     */
    public function selectTimeline(Timeline $timeline, EventRepository $eventRepository, Request $request)
    {
        $option = $request->query->get('option');

        if ($option == "asc") {
            $events = $eventRepository->findBy(['timeline' => $timeline], ['date' => 'ASC']);
        } elseif ($option == "desc") {
            $events = $eventRepository->findBy(['timeline' => $timeline], ['date' => 'DESC']);
        } else {
            $events = $eventRepository->findBy(['timeline' => $timeline]);
        }


        return $this->render('pages/content.html.twig', [
            'timeline' => $timeline,
            'events' => $events
        ]);
    }
}