<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\AjouterVilleType;
use App\Form\SearchVilleType;
use App\Repository\VilleRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{

    /**
     * @Route("/ville", name="ville")
     */
    public function index(EntityManagerInterface $manager, VilleRepository $villeRepository, Request $request): Response
    {
        $ville = $villeRepository->findAll();

        //Formulaire de recherche
        $formSearchVille= $this->createForm(SearchVilleType::class);

        $formSearchVille->handleRequest($request);

        if($formSearchVille->isSubmitted() && $formSearchVille->isValid()) {
            $criteres = $formSearchVille->getData();
            $searchVille = $criteres['search'];

            $ville = $villeRepository->searchVille($searchVille);
        }


        return $this->render('admin/ville/gererVille.html.twig', [
            'controller_name' => 'VilleController',
            'formSearchVille' => $formSearchVille->createView(),
            'ville' => $ville
        ]);
    }

    /**
     * @Route("/ville/ajouter", name="ville_ajouter")
     */
    public function ajouterVille(EntityManagerInterface $manager, Request $request): Response
    {
        $ville = new Ville();

        $formVille = $this->createForm(AjouterVilleType::class, $ville);

        $formVille->handleRequest($request);

        if($formVille->isSubmitted() && $formVille->isValid()) {
           // $manager = $this->getDoctrine()->getManager();
            $manager->persist($ville);
            $manager->flush();

            $this->addFlash('success', 'La ville a été enregistrée ' . $ville->getNom() . ' ' . $ville->getCodePostal());
            return $this->redirectToRoute('ville');
        }
        return $this->render('admin/ville/ajoutVille.html.twig', [
            'controller_name' => 'VilleController',
            'formVille' => $formVille->createView()
        ]);
    }

    /**
     * @Route("/ville/modifier/{id}", name="ville_modifier")
     */
    public function modifierVille(int $id, VilleRepository $villeRepository, Request $request){
        $ville = $villeRepository->find($id);

        $formVille = $this->createForm(AjouterVilleType::class, $ville);

        $formVille->handleRequest($request);
        if($formVille->isSubmitted() && $formVille->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ville);
            $manager->flush();
            $this->addFlash('success', 'La ville a bien été modifiée');

            return $this->redirectToRoute('ville');
        }
        return $this->render('admin/ville/ajoutVille.html.twig', [
            'formVille' => $formVille->createView(),
            'ville' => $ville
        ]);
    }

    /**
     * @Route("/ville/supprimer/{id}", name="ville_supprimer")
     */
    public function supprimerVille(int $id, VilleRepository $villeRepository){
        $ville = $villeRepository->find($id);
        $nomVille = $ville->getNom();
        try{
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($ville);
        $manager->flush();

            $this->addFlash('success', 'La ville '. $nomVille .' a bien été supprimée');

        }catch (Exception $e){
            $this->get('session')->getFlashBag()->add('error', 'Cette ville ne peut pas être supprimée, une sortie y est organisée');
            return $this->redirectToRoute('ville');
        }
        return $this->redirectToRoute('ville');

    }
}
