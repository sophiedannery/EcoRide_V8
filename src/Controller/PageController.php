<?php

namespace App\Controller;

use App\Repository\VoitureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $WebsiteName = 'EcoRide';

        return $this->render('page/index.html.twig', [
            'website_name' => $WebsiteName,
        ]);
    }

    #[Route('/mon_espace', name: 'app_mon_espace')]
    public function mon_espace(VoitureRepository $voitureRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $voitures = $voitureRepository->findBy(['user' => $user]);

        return $this->render('page/mon_espace.html.twig', [
            'user' => $user,
            'voitures' => $voitures

        ]);
    }
}
