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
            ->andWhere('c.date_heure_depart BETWEEN :startOfDay AND :endOfDay')
            ->andWhere('c.nb_place > 0')
            ->setParameter('depart', $depart)
            ->setParameter('arrivee', $arrivee)
            ->setParameter('startOfDay', $dateObject->format('Y-m-d 00:00:00'))
            ->setParameter('endOfDay', $dateObject->format('Y-m-d 23:59:59'))
            ->orderBy('c.date_heure_depart', 'ASC')
            ->getQuery()
            ->getResult();

        if (!$covoiturages) {
            $prochainCovoiturage = $covoiturageRepository->createQueryBuilder('c')
                ->where('c.depart = :depart')
                ->andWhere('c.arrivee = :arrivee')
                ->andWhere('c.date_heure_depart > :date')
                ->andWhere('c.nb_place > 0')
                ->setParameter('depart', $depart)
                ->setParameter('arrivee', $arrivee)
                ->setParameter('date', $dateObject)
                ->orderBy('c.date_heure_depart', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($prochainCovoiturage) {
                return $this->render('covoiturages/covoiturages_V2.html.twig', [
                    'prochainCovoiturage' => $prochainCovoiturage,
                ]);
            }

            // return new Response("Désolé, aucun itinéraire trouvé.");
            return $this->render('covoiturages/covoiturages_V2.html.twig');
        }

        return $this->render('covoiturages/covoiturages_V2.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/covoiturages_v2', name: 'app_covoiturages_v2')]
    public function covoiturageV2(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {

        $depart = null;
        $arrivee = null;
        $date = null;
        $covoiturages = [];

        if ($request->isMethod('GET') && $request->query->get('depart') && $request->query->get('arrivee') && $request->query->get('date')) {
            $depart = $request->query->get('depart');
            $arrivee = $request->query->get('arrivee');
            $date = $request->query->get('date');

            $dateObject = new \DateTime($date);

            $covoiturages = $covoiturageRepository->createQueryBuilder('c')
                ->where('c.depart = :depart')
                ->andWhere('c.arrivee = :arrivee')
                ->andWhere('c.date_heure_depart BETWEEN :startOfDay AND :endOfDay')
                ->andWhere('c.nb_place > 0')
                ->setParameter('depart', $depart)
                ->setParameter('arrivee', $arrivee)
                ->setParameter('startOfDay', $dateObject->format('Y-m-d 00:00:00'))
                ->setParameter('endOfDay', $dateObject->format('Y-m-d 23:59:59'))
                ->orderBy('c.date_heure_depart', 'ASC')
                ->getQuery()
                ->getResult();
        }


        if (!$covoiturages  && $depart && $arrivee && $date) {
            $prochainCovoiturage = $covoiturageRepository->createQueryBuilder('c')
                ->where('c.depart = :depart')
                ->andWhere('c.arrivee = :arrivee')
                ->andWhere('c.date_heure_depart > :date')
                ->andWhere('c.nb_place > 0')
                ->setParameter('depart', $depart)
                ->setParameter('arrivee', $arrivee)
                ->setParameter('date', $dateObject)
                ->orderBy('c.date_heure_depart', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($prochainCovoiturage) {
                return $this->render('covoiturages/covoiturages_V2.html.twig', [
                    'prochainCovoiturage' => $prochainCovoiturage,
                ]);
            }

            return $this->render('covoiturages/covoiturages_V2.html.twig');
        }

        return $this->render('covoiturages/covoiturages_V2.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }
}
