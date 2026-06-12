<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AddQuestionFormType;
use App\Form\AddCategorieFormType;
use App\Entity\Questions;
use App\Entity\Categorie;
use App\Entity\Clues;
use App\Entity\Answers;
use App\Repository\CategorieRepository;
use App\Repository\QuestionsRepository;



final class AdminInterfaceController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        $user = $this->getUser();
        if(!$user || $user->getRoles()[0] !== 'ROLE_ADMINSTRATOR') {
            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/admin.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/admin/add-question', name: 'app_add_question')]
    public function addQuestion(Request $request, EntityManagerInterface $entityManager): Response {
        
        $question = new Questions();
        $form = $this->createForm(AddQuestionFormType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('path')->getData();
            $clueText = $form->get('clue')->getData();
            $clue = new Clues;
            if($file){
                $type = explode('/', $file->getMimeType());
                $filename = $file->getClientOriginalName();
                $path = "/src/" . $type[0];
                $file->move($this->getParameter('kernel.project_dir'). '/public' . $path, $filename);
                $clue->setPath($path . '/' . $filename);
            }
            if($clueText){
                $clue->setClue($clueText);
            }
            $answerText = $form->get('answer')->getData();

            $answer = new Answers;
            $answer->setAnswer($answerText);
            $entityManager->persist($answer);

            $question->setClue($clue);
            $question->setAnswer($answer);

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_all_questions');
        }
        return $this->render('home/admin/add_question.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/add-categorie', name: 'app_add_categorie')]
    public function addCategorie(CategorieRepository $repository, Request $request, EntityManagerInterface $entityManager): Response {
        $categories = $repository->findAll();
        $categorie = new Categorie();
        $form = $this->createForm(AddCategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
        }
        return $this->render('home/admin/add_categorie.html.twig', [
            'form' => $form,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/all-question', name: 'app_all_questions')]
    public function showQuestions(QuestionsRepository $repository){
        $questions = $repository->findAllQuestions();

        // dd($questions);
        return $this->render('home/admin/show-questions.html.twig', [
            'questions' => $questions
        ]);
    }

    #[Route('admin/question/{id}', name: 'app_admin_question')]
    public function showOneQuestion(QuestionsRepository $repository, int $id){
        $question = $repository->find($id);
        return $this->render('home/admin/show_question.html.twig',[
            'question' => $question
        ]);
    }
}