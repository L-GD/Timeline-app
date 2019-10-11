<?php

namespace App\DataFixtures;

use App\Entity\Timeline;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TimelineFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setLogin('fixtureProfile')
            ->setUsername('fixtureProfile')
            ->setEmail('fixture@pwet.fr')
            ->setPassword('osef');
        $manager->persist($user);

        for ($i = 1; $i <= 3; $i++) {
            $timeline = new Timeline();
            $timeline->setName("pwet n°$i")
                ->setDescription("lorem ipsum")
                ->setCreatedAt(new \DateTime())
                ->setUser($user);

            $manager->persist($timeline);
        }
        $manager->flush();
    }
}
