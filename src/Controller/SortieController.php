<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\AnnulerSortieType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{


    /**
     * @Route("/sorties", name="sortie")
     */
    public function list(SortieRepository $sortieRepository): Response
    {

       $sorties = $sortieRepository->findAll();

        return $this->render('sortie/index.html.twig', [
            "sorties" => $sorties
        ]);
    }


    /**
     * @Route("/sorties/creer", name="sortie_creer")
     */
    public function creerSortie(SortieRepository $sortieRepository,
                                EntityManagerInterface $manager, Request $request): Response
    {


        $sortie = new Sortie();

        $formSorties = $this->createForm(SortieType::class, $sortie);
        $formSorties->handleRequest($request);
        //on donne l'état de la sortie en fonction du bouton cliqué
        $bouttonEnregistrer =  $request->request->get('enregistrer');

        $bouttonPublier =  $request->request->get('publier');
        if ($formSorties->isSubmitted() && $formSorties->isValid()){

            //changement de statut de en création à ouvert

            if ($bouttonEnregistrer) {
                $etat = $manager->getRepository(Etat::class)->findBy(['libelle' => 'Creer'])[0];

                $sortie->setEtat($etat);
            }
            if ($bouttonPublier){
                $etat = $manager->getRepository(Etat::class)->findBy(['libelle' => 'Ouverte'])[0];
                $sortie->setEtat($etat);
            }
            //on renseigne son organisateur (le user actuel)
            $user = $manager->getRepository(User::class)->find($this->getUser());

            $sortie->setOrganisateur($user);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($sortie);
            $manager->flush();
            $this->addFlash('success', 'Votre sortie a bien été enregistrée.');

            return $this->redirectToRoute('sortie_detail', [
                "id" => $sortie->getId(),
                "sortie" => $sortie
            ]);
        }

        return $this->render('sortie/creerSortie.html.twig', [
            "formSorties" => $formSorties->createView()
        ]);
    }



    /**
     * @Route("/sorties/detail/{id}", name="sortie_detail")
     */
    public function detailSortie($id, SortieRepository $sortieRepository){
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/detail.html.twig', [
            "sortie" => $sortie
        ]);
    }



    /**
     * @Route("/sorties/publier/{id}", name="sortie_publier")
     */
    public function publierSortie($id){

        $manager = $this->getDoctrine()->getManager();
        $SortieRepository = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $SortieRepository->find($id);

        $etat = $manager->getRepository(Etat::class)->findBy(['libelle' => 'Ouverte'])[0];
        $sortie->setEtat($etat);
        $manager->persist($sortie);
        $manager->flush();
        $this->addFlash('success', 'Votre sortie a bien été publiée.');
        return $this->redirectToRoute('main_home');;

    }



    /**
     * @Route("/sorties/modifier/{id}", name="sortie_modifier")
     */
    public function modifierSortie($id,SortieRepository $sortieRepository, Request $request, EntityManagerInterface $manager){
        $sortie = $sortieRepository->find($id);
        $formModifierSorties = $this->createForm(SortieType::class, $sortie);

        $formModifierSorties->handleRequest($request);

        $boutonEnregistrer = $request->request->get('enregistrer');
        $boutonPublier = $request->request->get('publier');
        $boutonSupprimer = $request->request->get('supprimer');

        if ($boutonEnregistrer){
            $etat = $manager->getRepository(Etat::class)->findBy(['libelle' => 'Creer'])[0];
            $sortie->setEtat($etat);
            $manager->persist($sortie);
            $manager->flush();

            $this->addFlash('success', 'Votre sortie a bien été enregistrée.');
            return $this->redirectToRoute('main_home');
        }
        if ($boutonPublier){
            $this->publierSortie($id);
        }
        if ($boutonSupprimer){
            $manager->remove($sortie);
            $manager->flush();

            $this->addFlash('success', 'Votre sortie a bien été supprimée.');
            return $this->redirectToRoute('main_home');
        }
        return $this->render('sortie/modifierSortie.html.twig', [
            "formModifierSorties" => $formModifierSorties->createView()
        ]);
    }



    /**
     * @Route("/sorties/supprimer/{id}", name="sortie_supprimer")
     */
    public function supprimerSortie($id, SortieRepository $sortieRepository,EntityManagerInterface $manager, Request $request)
    {
        $sortie = $sortieRepository->find($id);
        $formAnnuleeSortie = $this->createForm(AnnulerSortieType::class, $sortie);
        $formAnnuleeSortie->handleRequest($request);
        // Etat Annulée
        if ($formAnnuleeSortie->isSubmitted() && $formAnnuleeSortie->isValid()) {

            $etat = $manager->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
            $sortie->setEtat($etat);
            $manager->persist($sortie);
            $manager->flush();
            $this->addFlash('success', 'Votre sortie a bien été annulée.');
            return $this->redirectToRoute('main_home');
        }
        return $this->render('sortie/annulerSortie.html.twig', [
            "formAnnuleeSortie" => $formAnnuleeSortie->createView(),
            'sortie' => $sortie
        ]);
    }

}
