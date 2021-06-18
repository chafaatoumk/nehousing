<?php

namespace App\DataFixtures;

use App\Entity\Announcement;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i=0; $i<20; $i++)
        {
            $announcement = new Announcement();
            $announcement->setTitle("room $i");
            $announcement->setslug("room-$i");
            $announcement->setDescription("Sells of that beautifull house !");
            $announcement->setPrice(mt_rand(10000, 60000));
            $announcement->setAddress("$i ROAD MEDINA-$i");
            $announcement->setCoverImage("https://via.placeholder.com/500*300");
            $announcement->setRooms(mt_rand(1, 7));
            $announcement->setCreatedAt(new DateTime());
            $announcement->setIsAvalaible(mt_rand(0, 1));

            $manager->persist($announcement);
        }
        $manager->flush();
    }
}
