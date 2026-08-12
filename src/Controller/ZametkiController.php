<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use App\Form\NoteType;

final class ZametkiController extends AbstractController
{
    #[Route('/', name: 'app_zametki')]
    public function index(EntityManagerInterface $em): Response
    {
        $notes = $em->getRepository(Note::class)->findAll();
        return $this->render('zametki/index.html.twig', [
            'notes' => $notes,
        ]);
    }

        #[Route('/add', name: 'add_zametka')]
    public function addZam(Request $request, EntityManagerInterface $em): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $note->setCreatedAt(new \DateTimeImmutable());
            
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('app_zametki');
        }

        return $this->render('zametki/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
