<?php

namespace App\Controller;

use App\Repository\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/search', name: 'search_covoiturage')]
    public function search(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        $depart = $request->query->get('depart');
        $arrivee = $request->query->get('arrivee');
        $date = $request->query->get('date');

        $dateObject = new \DateTime($date);

        $covoiturages = $covoiturageRepository->createQueryBuilder('c')
            ->where('c.depart = :depart')
            ->andWhere('c.arrivee = :arrivee')
            ->andWhere('c.date_heure_depart >= :date')
            ->andWhere('c.nb_place > 0')
            ->setParameter('depart', $depart)
            ->setParameter('arrivee', $arrivee)
            ->setParameter('date', $dateObject)
            ->orderBy('c.date_heure_depart', 'ASC')
            ->getQuery()
            ->getResult();

        if (!$covoiturages) {
            $prochainCovoiturage = $covoiturageRepository->createQueryBuilder('c')
                ->where('c.depart = :depart')
                ->andWhere('c.arrivee = :arrivee')
                ->andWhere('c.date_heure_depart >= :date')
                ->andWhere('c.nb_place > 0')
                ->setParameter('depart', $depart)
                ->setParameter('arrivee', $arrivee)
                ->setParameter('date', $dateObject)
                ->orderBy('c.date_heure_depart', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($prochainCovoiturage) {
                return $this->render('covoiturages/suggestion.html.twig', [
                    'covoiturage' => $prochainCovoiturage,
                ]);
            }

            return new Response("Désolé, aucun itinéraire trouvé.");
        }

        return $this->render('covoiturages/results.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }
}
