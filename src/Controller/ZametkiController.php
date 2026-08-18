<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Note;
use App\Form\NoteType;

final class ZametkiController extends AbstractController
{
    #[Route('/', name: 'appNote')]
    public function show(EntityManagerInterface $em, Security $security): Response
    {
        $notes = $em->getRepository(Note::class)->findBy(['user' => $security->getUser()]);
        return $this->render('zametki/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/add', name: 'addNote')]
    public function addNote(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $note->setCreatedAt(new \DateTimeImmutable());
            $note->setUser($security->getUser());

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
    public function updNote(Request $request, EntityManagerInterface $em, Note $note, Security $security): Response
    {
        if ($note->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException('Это не ваша заметка');
        }
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

        #[Route('/delete/{id}', name: 'delNote', methods: ['POST'])]
    public function delNote(Request $request, EntityManagerInterface $em, Note $note, Security $security): Response
    {
        if ($note->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException('Это не ваша заметка');
        }
        if (!$this->isCsrfTokenValid('delete' . $note->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Неверный токен');
        }
        $em->remove($note);
        $em->flush();
        return $this->redirectToRoute('appNote');
    }
}
