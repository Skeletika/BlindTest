<?php

namespace App\Controller;

use App\Form\FilterGameFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if($user){
            if($user->getRoles()[0] === 'ROLE_ADMINSTRATOR') {
                return $this->redirectToRoute('app_admin');
            }
        }

        $playForm = $this->createForm(FilterGameFormType::class);
        $playForm->handleRequest($request);
        if($playForm->isSubmitted() && $playForm->isValid()){
            $categories = $playForm->get('categories')->getData();
            $ids = $categories->map(fn($c) => $c->getId())->toArray();

            $session = $request->getSession();
            $session->set('filter_categories', $ids);
            return $this->redirectToRoute('app_game');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'playForm' => $playForm,
        ]);
    }
}
