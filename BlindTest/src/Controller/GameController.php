<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\QuestionsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
final class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(SessionInterface $session, Request $request, QuestionsRepository $questionsRepo): Response
    {
        $session = $request->getSession();
        $ids = $session->get('filter_categories');

        $questions = $questionsRepo->findAllQuestionWithCategories($ids);
        $questionIds = [];
        foreach($questions as $question){
            array_push($questionIds, $question->getId());
        }
        $session->set('questionsIds', $questionIds);
        $session->set('current_index', 0);
        
        return $this->render('game/index.html.twig');
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

    #[Route('/game/next/{id}', name: 'app_game_one_question')]
    public function viewQuestion(QuestionsRepository $repo, int $id)
    {
        $question = $repo->takeQuestions($id);
        $question = $question[0];
        $categorie = $question->getCategorie()->getName();
        $clue = $question->getClue()->getClue();
        $answer = $question->getAnswer()->getAnswer();
        dd($question->getQuestion(), $categorie, $clue, $answer);
    }
}
