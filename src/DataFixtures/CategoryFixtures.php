<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('war')
            ->setColour('red')
            ->setIcon('fab fa-old-republic');
        $manager->persist($category);

        $category = new Category();
        $category->setName('death')
            ->setColour('grey')
            ->setIcon('fas fa-skull-crossbones');
        $manager->persist($category);

        $category = new Category();
        $category->setName('birth')
            ->setColour('darkslateblue')
            ->setIcon('fas fa-birthday-cake');
        $manager->persist($category);

        $category = new Category();
        $category->setName('politics')
            ->setColour('darkcyan')
            ->setIcon('fas fa-handshake');
        $manager->persist($category);

        $category = new Category();
        $category->setName('environment')
            ->setColour('forestgreen')
            ->setIcon('fas fa-meteor');
        $manager->persist($category);

        $category = new Category();
        $category->setName('relationship')
            ->setColour('hotpink')
            ->setIcon('fas fa-heartbeat');
        $manager->persist($category);

        $manager->flush();
    }
}
