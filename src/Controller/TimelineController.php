<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Entity\User;
use App\Form\TimelineType;
use App\Repository\TimelineRepository;
use App\Repository\UserRepository;
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

        return $this->render('timeline/index.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/timeline/new", name="newTL")
     * @Route("/timeline/{id}/edit", name="TL_edit")
     */

    public function Form(Timeline $timeline = null, Request $request, ObjectManager $manager)
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
            }
            $manager->persist($timeline);
            $manager->flush();

            $this->addFlash('success', 'Votre chronologie a été modifiée !');
            return $this->redirectToRoute('list');
        }

        return $this->render('timeline/timeline.html.twig', [
            'form' => $form->createView(),
            'editMode' => $timeline->getId() !== null
        ]);
    }

    /**
     * @Route("/show", name="list")
     */

    public function list(TimelineRepository $timeline)
    {
        /*$user = $this->getUser();
        $timelines = $timeline->findBy([
            'user_id' => $this->getUser()
        ]);*/
        $timelines = $timeline->findAll();

        return $this->render('timeline/show.html.twig', [
            'timelines' => $timelines
        ]);
    }

    /**
     * @Route("/delete/{id}", name="removeTL")
     */

    public function delete(ObjectManager $manager, $id)
    {
        $manager = $this->getDoctrine()->getManager();
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
        return $this->render('timeline/event.html.twig');
    }
}
