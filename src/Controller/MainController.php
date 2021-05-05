<?php

namespace App\Controller;

use App\Form\SearchSortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/main', name: 'main_home')]
    public function index(SortieRepository $sortieRepository): Response
    {
        //creation du formulaire de recherche
        $formSearch = $this->createForm(SearchSortieType::class);
        //affichage de toutes les sorties
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/index.html.twig', [
            'formSearch' => $formSearch->createView(),
            'sorties' => $sorties
        ]);
    }
}
