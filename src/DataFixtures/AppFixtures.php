<?php

namespace App\DataFixtures;

use App\Entity\Announcement;
use App\Entity\Comment;
use App\Entity\Image;
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
        //$slugger = new Slugify(); // generate a slug from a given sentence(the announcement title)

        for($i=0; $i<6; $i++)
        {
            $announcement = new Announcement('en_US');
            $announcement->setTitle($faker->sentence(3, false));
            $announcement->setDescription($faker->text(200));
            $announcement->setPrice(mt_rand(10000, 60000));
            $announcement->setAddress($faker->streetAddress());
            $announcement->setRooms(mt_rand(1, 7));
            $announcement->setCreatedAt($faker->dateTimeBetween('-3 month', 'now'));
            $announcement->setIsAvalaible(mt_rand(0, 1));
            $announcement->setBaths(mt_rand(1, 10));
            $announcement->setArea(mt_rand(100, 1000));
            $announcement->setCoverImage("https://picsum.photos/id/".mt_rand(1, 200)."/500/600");

            for($j=0; $j<6; $j++)
            {
                $comment = new Comment('en_US');
                $comment->setAuthor($faker->name());
                $comment->setEmail($faker->email());
                $comment->setContent($faker->text(100));
                $comment->setCreatedAt($faker->dateTimeBetween('-3 month', 'now'));
                $comment->setAnnouncement($announcement);

                $manager->persist($comment);
                $announcement->addComment($comment);
            }

            for($k=0; $k<mt_rand(0, 9); $k++)
            {
                $image = new Image('en_US');
                $image->setImageUrl("https://picsum.photos/id/".mt_rand(1, 200)."/850/800");
                $image->setDescription($faker->sentence(50));
                $image->setAnnouncement($announcement);

                $manager->persist($image);
                $announcement->addImage($image);
            }

            $manager->persist($announcement);
        }
        $manager->flush();
    }
}
