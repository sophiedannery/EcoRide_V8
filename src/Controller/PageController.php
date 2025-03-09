<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Form\CovoiturageFormType;
use App\Form\VoitureFormType;
use App\Repository\CovoiturageRepository;
use App\Repository\UserRepository;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();


        $WebsiteName = 'EcoRide';

        return $this->render('page/index.html.twig', [
            'website_name' => $WebsiteName,

        ]);
    }

    #[Route('/mon_espace', name: 'app_mon_espace')]
    public function mon_espace(VoitureRepository $voitureRepository, CovoiturageRepository $covoiturageRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $voitures = $voitureRepository->findBy(['user' => $user]);
        $covoiturages = $covoiturageRepository->findBy(['chauffeur' => $user]);

        $covoituragesPassager = $covoiturageRepository->createQueryBuilder('c')
            ->join('c.passagers', 'p')
            ->where('p = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();


        return $this->render('page/mon_espace.html.twig', [
            'user' => $user,
            'voitures' => $voitures,
            'covoiturages' => $covoiturages,
            'covoituragesPassager' => $covoituragesPassager
        ]);
    }

    #[Route('/covoiturages', name: 'app_covoiturages')]
    public function covoiturages(CovoiturageRepository $covoiturageRepository): Response
    {
        $covoiturages = $covoiturageRepository->findAllOrderedByDateDepart();


        return $this->render('page/covoiturages.html.twig', [
            'covoiturages' => $covoiturages
        ]);
    }

    #[Route('/covoiturages/{id}', name: 'app_covoiturages_show')]
    public function show(Covoiturage $covoiturage): Response
    {

        return $this->render('covoiturages/show.html.twig', [
            'covoiturage' => $covoiturage
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

    #[Route('ajout_trajet', name: 'app_ajout_trajet')]
    public function ajoutTrajet(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $covoiturage = new Covoiturage();
        $form = $this->createForm(CovoiturageFormType::class, $covoiturage);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $chauffeur = $this->getUser();
            $covoiturage->setChauffeur($chauffeur);

            $voiture = $covoiturage->getVoiture();
            if ($voiture && strtolower($voiture->getEnergie()) === 'electrique') {
                $covoiturage->setIsEcologique(true);
            } else {
                $covoiturage->setIsEcologique(false);
            }

            $entityManager->persist($covoiturage);
            $entityManager->flush();

            $this->addFlash('success', 'Trajet ajouté avec succès !');

            return $this->redirectToRoute('app_mon_espace');
        }

        return $this->render('page/ajout_trajet.html.twig', [
            'covoiturageForm' => $form->createView(),
        ]);
    }


    #[Route('participer_trajet/{id}', name: 'app_participer_trajet')]
    public function participerTrajet(int $id, CovoiturageRepository $covoiturageRepository, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $covoiturage = $covoiturageRepository->find($id);

        if (!$covoiturage) {
            $this->addFlash('error', 'Covoiturage non trouvé.');
            return $this->redirectToRoute('app_covoiturages');
        }

        if ($covoiturage->getPassagers()->contains($user)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à ce trajet.');
            return $this->redirectToRoute('app_covoiturages');
        }

        if ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('error', 'Il n\'y as plus de places disponibles pour ce trajet.');
            return $this->redirectToRoute('app_covoiturages');
        }

        $covoiturage->addPassagers($user);

        $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1);

        $entityManager->persist($covoiturage);
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez rejoint ce trajet en tant que passager.');

        return $this->redirectToRoute('app_covoiturages');
    }
}
