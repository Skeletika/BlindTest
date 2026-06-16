<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\QuestionsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


use Symfony\Component\HttpFoundation\JsonResponse;

#[IsGranted('ROLE_USER', message: 'Accès réservé aux utilisateurs.')]

final class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(SessionInterface $session, Request $request, QuestionsRepository $questionsRepo): Response
    {
        $session = $request->getSession();
        $filter = $session->get('filters_game');
        $timer = $session->get('timer') * 1000; // convertion en ms
        $questions = $questionsRepo->findAllQuestionWithFilters($filter['types'], $filter['categories'], $filter['nbQuestions']);
        $questionIds = $questions;
        foreach($questions as $question){
            array_push($questionIds, $question->getId());
        }
        if($questionIds == []){
            return $this->redirectToRoute('app_home');
        }
        // re-melange deuxieme fois
        $r = new \Random\Randomizer();
        $questionIds = $r->shuffleArray($questionIds);
        
        $session->set('questionsIds', $questionIds);
        $session->set('current_index', 0);
        
        return $this->render('game/index.html.twig', [
            'timer' => $timer
        ]);
    }

    #[Route('/game/next', name: 'app_game_get_question')]
    public function nextQuestion(SessionInterface $session, QuestionsRepository $repo)
    {
        $ids = $session->get('questionsIds', []);
        $index = $session->get('current_index', 0);
        
        if (!isset($ids[$index])) {
            return $this->json(['finished' => true]);
        }

        $question = $repo->takeQuestions($ids[$index]);
        $question = $question[0];
        $categorie = $question->getCategorie()->getName();

        $clueText = $question->getClue()?->getClue();
        $cluePath = $question->getClue()?->getPath();
        $clueType = null;
        if ($cluePath) {
            $extension = strtolower(pathinfo($cluePath, PATHINFO_EXTENSION));

            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $clueType = 'image';
            } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                $clueType = 'video';
            } elseif (in_array($extension, ['mp3', 'wav', 'oga'])) {
                $clueType = 'audio';
            }
        }
        $answer = $question->getAnswer()->getAnswer();

        $session->set('current_index', $index + 1);

        return $this->json([
            'question' => $question->getQuestion(),
            'categorie' => $categorie,
            'indice' => [
                'text' => $clueText,
                'path' => $cluePath,
                'type' => $clueType,
            ],
            'answer' => $answer
        ]);
    }

    #[Route('/game/result/{score}', name: 'app_game_result')]
    public function viewResult(int $score, EntityManagerInterface $em){
        $user = $this->getUser();
        if ($score !== null && is_numeric($score)) {
            $user->setPoints($user->getPoints() + (int) $score);
            $em->flush();
        }
        return $this->json([
            'redirect' => $this->generateUrl('app_profile'),
            'score' => $score
        ]);
    }
}
