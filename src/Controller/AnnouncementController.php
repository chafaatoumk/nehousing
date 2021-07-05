<?php

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use App\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnouncementController extends AbstractController
{
    /**
     * @Route("/property", name="property-grid")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Announcement::class);
        $announcements = $repository->findAll();

        return $this->render('announcement/property-grid.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * @Route("/property/{id}", name="property-single")
     */
    public function show(int $id, AnnouncementRepository $announcementRepository): Response
    {
        $announcement = $announcementRepository
            ->find($id);
        $images = $announcement->getImages();
        $comments = $announcement->getComments();

        if (!$announcement) {
            throw $this->createNotFoundException(
                'No Property found for id '.$id
            );
        }

        // render a template
        return $this->render('announcement/property-single.html.twig', [
            'announcement' => $announcement,
            'images' => $images,
            'comments' => $comments,
        ]);
    }
}
