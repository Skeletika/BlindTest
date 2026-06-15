<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER', message: 'Accès réservé aux utilisateurs.')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        // Ajout des points via ?score=n
        $score = $request->query->get('score');

        // Upload de la photo
        $image = $request->files->get('profile_picture');

        if ($image) {
            $filename = uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/profile',
                    $filename
                );

                $user->setProfilePath('/uploads/profile/' . $filename);

                $em->flush();
            } catch (FileException $e) {
                $this->addFlash(
                    'error',
                    'Impossible de téléverser l\'image.'
                );
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'scoreAdded' => $score
        ]);
    }
}