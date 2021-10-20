<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class MainController extends AbstractController
{

    /**
     * @Route("/main", name="main_home")
     */
    public function index(SortieRepository $sortieRepository,
                          Request $request,
                          EntityManagerInterface $manager,
                          CampusRepository $campusRepository,
                          UserInterface $userInterface): Response
    {
        //recupération de l'utilisateur connecté // recuperation de l'Id
        $user = $request->getUser();

        //creation du formulaire de recherche
        $formSearch = $this->createForm(SearchSortieType::class);


        //gestion de l'etat en fonction de la date
         $this->dateModifieEtat($sortieRepository, $manager);

         //filtre sur les sorties - traitement du formualaire
            //recupération des info dans la requete
             $formSearch->handleRequest($request);

        //affichage de base

        $campusSelect = 4;
        $userId = $userInterface->getId();
        $search = null;
        $dateDebut = null;
        $dateFin = null;
        $isInscrit = true;
        $organiser = true;
        $isNotInscrit = true;
        $passee = false;
        $dateDuJour = new \DateTime('now');
        $dateDuJourMoinsUnMois = $dateDuJour->sub(new  \DateInterval('P1M'));
        $dateDuJour = new \DateTime('now');
        $sorties = $sortieRepository->searchSorties($campusSelect, $search, $dateDebut, $dateFin, $isInscrit, $organiser,
            $isNotInscrit, $passee, $userId, $dateDuJour, $dateDuJourMoinsUnMois);
            //Campus selectionné
        if($formSearch->isSubmitted() && $formSearch->isValid()) {
            // site sélectionné
            $criteres = $formSearch->getData();
            $campusSelect = $campusRepository->find($criteres['campus']);
            $campusSelect = $campusSelect->getId();

            $userId = $userInterface->getId();
            $search = $criteres['search'];
            $dateDebut = $criteres['dateDebut'];
            $dateFin = $criteres['dateFin'];
            $isInscrit = $criteres['isInscrit'];
            $organiser = $criteres['organiser'];
            $isNotInscrit = $criteres['isNotInscrit'];
            $passee = $criteres['passee'];
            $dateDuJour = new \DateTime('now');
            $dateDuJourMoinsUnMois = $dateDuJour->sub(new  \DateInterval('P1M'));
            $dateDuJour = new \DateTime('now');
            $sorties = $sortieRepository->searchSorties($campusSelect, $search, $dateDebut, $dateFin, $isInscrit, $organiser,
                                                       $isNotInscrit, $passee, $userId, $dateDuJour, $dateDuJourMoinsUnMois);

        }

        return $this->render('sortie/index.html.twig', [
            'formSearch' => $formSearch->createView(),
            'sorties' => $sorties
        ]);
    }
    public function dateModifieEtat(SortieRepository $sortieRepository, EntityManagerInterface $manager){
        //recupération de toutes les sorties
        $sorties = $sortieRepository->findAll();
        //gestion des etats en fonction de la date
        $dateDuJour = new \DateTime('now');
        foreach($sorties as $sortie) {
            //Si la date d'inscrition est inférieur a la date du jour et dateSortie superieur a la date du jour = etat cloturée
            if ($sortie->getEtat()->getLibelle() != "Annulée" && $sortie->getEtat()->getLibelle() != "Creer") {
                if ($sortie->getDateLimiteInscription() < $dateDuJour && $sortie->getDateHeureDebut() > $dateDuJour) {
                    $this->changerEtat($sortie, "Cloturee", $manager);

                }

                //Si la date d'inscrition est superieur a la date du jour et la date sortie aussi = etat ouverte
                if ($sortie->getDateLimiteInscription() > $dateDuJour && $sortie->getDateHeureDebut() > $dateDuJour) {
                    $this->changerEtat($sortie, "Ouverte", $manager);

                }
                //Si la date d'insciption est inferieur a la date du jour et dateSortie = dateDuJour = etat en cours
                if ($sortie->getDateLimiteInscription() < $dateDuJour && $sortie->getDateHeureDebut() == $dateDuJour) {
                    $this->changerEtat($sortie, "Activite, en cours", $manager);
                }
                //Si la date d'inscription est inférieur a la date du jour et dateSortie aussi = etat passée
                if ($sortie->getDateLimiteInscription() < $dateDuJour && $sortie->getDateHeureDebut() < $dateDuJour) {
                    $this->changerEtat($sortie, "Passee", $manager);
                }
            }
        }
    }
    private function changerEtat(Sortie $sortie, string $etatString, EntityManagerInterface $manager)
    {
        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etatNouveau = $etatRepo->findOneBy(['libelle' => $etatString]);
        $sortie->setEtat($etatNouveau);
        $manager->persist($sortie);
    }
}
