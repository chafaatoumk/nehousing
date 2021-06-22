<?php

namespace App\DataFixtures;

use App\Entity\Announcement;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Cocur\Slugify\Slugify;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // use the factory to create a Faker\Generator instance
        $faker = Factory::create();
        $slugger = new Slugify(); // generate a slug from a given sentence(the announcement title)

        for($i=0; $i<6; $i++)
        {
            $announcement = new Announcement('en_US');
            $announcement->setTitle($faker->sentence(3, false));
            $announcement->setDescription($faker->text(50));
            $announcement->setPrice(mt_rand(10000, 60000));
            $announcement->setAddress($faker->streetAddress());
            $announcement->setRooms(mt_rand(1, 7));
            $announcement->setCreatedAt($faker->dateTimeBetween('-3 month', 'now'));
            $announcement->setIsAvalaible(mt_rand(0, 1));
            $announcement->setCoverImage("https://i.pinimg.com/originals/74/a1/ce/74a1ce39517604d4812123b25e256f0c.jpg");

            $manager->persist($announcement);
        }
        $manager->flush();
    }
}
