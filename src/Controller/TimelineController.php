<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Form\TimelineType;
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
     * @Route("/pages/new", name="newTL")
     * @Route("/pages/{id}/edit", name="TL_edit")
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
                //add calendar
                $this->addFlash('success', 'Votre chronologie a été créée !');
            } else {
                $this->addFlash('success', 'Votre chronologie a été modifiée !');
            }
            $manager->persist($timeline);
            $manager->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render('pages/timeline.html.twig', [
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

        return $this->render('pages/show.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/delete/{id}", name="removeTL")
     */
    public function delete(ObjectManager $manager, $id)
    {
        $timeline = $manager->getRepository(Timeline::class)->find($id);

        $manager->remove($timeline);
        $manager->flush();

        return $this->redirectToRoute('list');
    }

    /**
     * @Route("/event", name="event")
     */
    public function event()
    {
        return $this->render('pages/event.html.twig');
    }
}
