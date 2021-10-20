<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\AjouterCampusType;
use App\Form\SearchCampusType;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus")
     */
    public function index(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        $formSearchCampus = $this->createForm(SearchCampusType::class);

        if($formSearchCampus->isSubmitted() && $formSearchCampus->isValid()) {

            $searchCampus = $formSearchCampus->getData();

            $campus = $campusRepository->searchCampus($searchCampus);
        }
        return $this->render('admin/campus/gererCampus.html.twig', [
            'controller_name' => 'CampusController',
            'formSearchCampus' => $formSearchCampus->createView(),
            'campus' => $campus
        ]);
    }

    /**
     * @Route("/campus/ajouter", name="campus_ajouter")
     */
    public function ajouterCampus(EntityManagerInterface $manager, Request $request): Response
    {
        $campus = new Campus();

        $formCampus = $this->createForm(AjouterCampusType::class, $campus);

        $formCampus->handleRequest($request);

        if($formCampus->isSubmitted() && $formCampus->isValid()) {

            $manager->persist($campus);
            $manager->flush();

            $this->addFlash('success', 'Le campus a été enregistré ' . $campus->getNom());
            return $this->redirectToRoute('campus');
        }
        return $this->render('admin/campus/ajoutCampus.html.twig', [
            'controller_name' => 'CampusController',
            'formCampus' => $formCampus->createView(),
            'campus' => $campus
        ]);
    }

    /**
     * @Route("/campus/modifier/{id}", name="campus_modifier")
     */
    public function modifierCampus(int $id, CampusRepository $campusRepository, Request $request){
        $campus = $campusRepository->find($id);

        $formCampus = $this->createForm(AjouterCampusType::class, $campus);

        $formCampus->handleRequest($request);
        if($formCampus->isSubmitted() && $formCampus->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($campus);
            $manager->flush();
            $this->addFlash('success', 'Le campus a bien été modifiée');

            return $this->redirectToRoute('campus');
        }
        return $this->render('admin/campus/ajoutCampus.html.twig', [
            'formCampus' => $formCampus->createView(),
            'campus' => $campus
        ]);
    }

    /**
     * @Route("/campus/supprimer/{id}", name="campus_supprimer")
     */
    public function supprimerCampus(int $id, CampusRepository $campusRepository){
        $campus = $campusRepository->find($id);
        $nomCampus = $campus->getNom();
        try{
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($campus);
            $manager->flush();
            $this->addFlash('success', 'Le campus ' .$nomCampus . ' a été supprimé');

        }catch (Exception $e){
            $this->get('session')->getFlashBag()->add('error', 'Ce campus ne peut pas être supprimée, une sortie y est organisée');
            return $this->redirectToRoute('campus');
        }
        return $this->redirectToRoute('campus');


    }
}
