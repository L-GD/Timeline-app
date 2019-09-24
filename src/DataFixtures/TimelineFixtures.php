<?php

namespace App\DataFixtures;

use App\Entity\Timeline;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TimelineFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 3; $i++) {
            $timeline = new Timeline();
            $timeline->setName("pwet n°$i")
                ->setDescription("lorem ipsum")
                ->setCreatedAt(new \DateTime());

            $manager->persist($timeline);
        }

        $manager->flush();
    }
}
