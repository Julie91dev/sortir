<?php

namespace App\Controller;

use App\Entity\Campus;
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

class MainController extends AbstractController
{
    #[Route('/main', name: 'main_home')]
    public function index(SortieRepository $sortieRepository, Request $request, EntityManagerInterface $manager, CampusRepository $campusRepository): Response
    {
        //recupération de l'utilisateur connecté
        $user = $request->getUser();
        //creation du formulaire de recherche
        $formSearch = $this->createForm(SearchSortieType::class);
        //recupération de toutes les sorties
        $sorties = $sortieRepository->findAll();
        //gestion des etats en fonction de la date
        $dateDuJour = new \DateTime('now');
        foreach($sorties as $sortie) {
            //Si la date d'inscrition est inférieur a la date du jour et dateSortie superieur a la date du jour = etat cloturée
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
        $manager->flush();
        //filtre sur les sorties - traitement du formualaire
        //recupération des info dans la requete
             $formSearch->handleRequest($request);
            //Campus selectionné
        if($formSearch->isSubmitted() && $formSearch->isValid()) {
            $criteres = $formSearch->getData();
            //Si il y a des critères
           /* if ($criteres){
                $campusSelect = $campusRepository->find($criteres['campus']);
                $search = $criteres['search'];
                $dateDebut = $criteres['dateDebut'];
                $dateFin = $criteres['dateFin'];

                $sorties = $sortieRepository->searchSorties(['campus' => $campusSelect] == null,
                                                            $search == null,
                                                         $dateDebut == null,
                                                           $dateFin == null);
                dd($sorties);
            }*/
            if($criteres['campus']) {
                // site sélectionné
                $campusSelect = $campusRepository->find($criteres['campus']);
                $sorties = $sortieRepository->findBy(['campus' => $campusSelect]);
            }
            //le nom contient
            if ($criteres['search']){
                $search = $criteres['search'];

               $sorties = $sortieRepository->findSearchCharSortie($search);
            }
            //Entre les dates
            if ($criteres['dateDebut'] && $criteres['dateFin']){
                $dateDebut = $criteres['dateDebut'];
                $dateFin = $criteres['dateFin'];

                $sorties = $sortieRepository->findDatesSorties($dateDebut, $dateFin);
            }
            if($criteres['isInscrit']) {
                //sortie sur lesquelle je suis inscrit/e
                $sorties = $this->getUser()->getSorties();
           }
            if($criteres['organiser']) {
                //sortie sur lesquelle je suis organisateur
                $user = $this->getUser();
                $sorties = $sortieRepository->findBy(['organisateur' => $user]);
            }
           // if($criteres['isNotInscrit']) {}
            if($criteres['passee']) {
                $dateDuJour = new \DateTime('now');
               $sorties = $sortieRepository->findSortiesPassees($dateDuJour);

            }
        }
        return $this->render('sortie/index.html.twig', [
            'formSearch' => $formSearch->createView(),
            'sorties' => $sorties
        ]);
    }
    private function changerEtat(Sortie $sortie, string $etatString, EntityManagerInterface $manager)
    {
        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etatNouveau = $etatRepo->findOneBy(['libelle' => $etatString]);
        $sortie->setEtat($etatNouveau);
        $manager->persist($sortie);
    }
}
