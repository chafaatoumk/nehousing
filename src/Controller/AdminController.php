<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Announcement;
use App\Form\AnnouncementType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/examples/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/properties", name="properties-table")
     */
    public function properties_table(): Response
    {
        $announcements = $this->getDoctrine()->getRepository(Announcement::class)
            ->findAll();

        return $this->render('admin/examples/tables.html.twig', [
            'announcements' => $announcements,
        ]);
    }

    /**
     * @Route("/admin/property/new", name="property-new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $em)
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $coverImage */
            $coverImage = $form->get('coverImage')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($coverImage) {
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $filename = $originalFilename . '-' . uniqid() . '.' . $coverImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                        $coverImage->move(
                        $this->getParameter('coverImages_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $announcement->setCoverImage($filename);
            }
            // Get form data from view in an array
            $announcement->setIsAvalaible(mt_rand(0, 1));

            $em->persist($announcement);
            $em->flush();
            $this->addFlash('message', 'Announcement added successfully !');

            return $this->redirectToRoute('properties-table');
        }

        return $this->render('./announcement/create.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/property/remove/{id}", name="property-remove")
     */
    public function remove_property(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $announcement = $entityManager->getRepository(Announcement::class)->find($id);

        if (!$announcement) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        else{
            $entityManager->remove($announcement);
            $entityManager->flush();
            $this->addFlash('message', 'Announcement deleted successfully !');

            return $this->redirectToRoute('properties-table');
        }

        return $this->render('admin/examples/tables.html.twig');
    }

    
    /**
     * @Route("admin/property/edit/{id}", name="property-edit", methods={"GET", "POST"})
     */
    public function edit_property(Announcement $announcement, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $coverImage */
            $coverImage = $form->get('coverImage')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($coverImage) {
                $originalFilename = pathinfo($coverImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $filename = $originalFilename . '-' . uniqid() . '.' . $coverImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                        $coverImage->move(
                        $this->getParameter('coverImages_directory'),
                        $filename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $announcement->setCoverImage($filename);
            }
            // Get form data from view in an array
            $announcement->setIsAvalaible(mt_rand(0, 1));

            $em->persist($announcement);
            $em->flush();
            $this->addFlash('message', 'Announcement updated successfully !');

            return $this->redirectToRoute('properties-table');
        }

        return $this->render('announcement/update.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }
}
