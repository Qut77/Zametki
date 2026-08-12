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
    #[Route('/', name: 'appNote')]
    public function show(EntityManagerInterface $em): Response
    {
        $notes = $em->getRepository(Note::class)->findAll();
        return $this->render('zametki/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/add', name: 'addNote')]
    public function addNote(Request $request, EntityManagerInterface $em): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $note->setCreatedAt(new \DateTimeImmutable());
            
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('appNote');
        }

        return $this->render('zametki/add.html.twig', [
            'form' => $form->createView(),
            'h2' => 'Добавление',
        ]);
    }
    #[Route('/update/{id}', name: 'updNote')]
    public function updNote(Request $request, EntityManagerInterface $em, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute('appNote');
        }

        return $this->render('zametki/add.html.twig', [
            'form' => $form->createView(),
            'h2' => 'Изменение',
        ]);
    }
}
