<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureFormType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('ajout_voiture', name: 'app_ajout_voiture')]
    public function ajoutVoiture(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $voiture = new Voiture();
        $form = $this->createForm(VoitureFormType::class, $voiture);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $voiture->setUser($user);

            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('app_mon_espace');
        }

        return $this->render('page/ajout_voiture.html.twig', [
            'voitureForm' => $form->createView(),
        ]);
    }
}
