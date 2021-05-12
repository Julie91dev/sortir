<?php

namespace App\Controller;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/utilisateurs', name: 'admin_utilisateurs')]
    public function listUtilisateurs(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/listUsers.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }
    #[Route('/admin/utilisateurs/activer/{id}', name: 'admin_activer')]
    public function activerUtilisateurs(UserRepository $userRepository,int $id, EntityManagerInterface $manager): Response
    {
        $user = $userRepository->find($id);
        $user->setActif(1);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash("success", "Utilisateur activé : " . $user->getPseudo());
        return $this->redirectToRoute('admin_utilisateurs');

    }
    #[Route('/admin/utilisateurs/desactiver/{id}', name: 'admin_desactiver')]
    public function desactiverUtilisateurs(UserRepository $userRepository,int $id, EntityManagerInterface $manager): Response
    {
        $user = $userRepository->find($id);
        $user->setActif(0);
        $manager->persist($user);
        $manager->flush();
        $this->addFlash("success", "Utilisateur desactivé : " . $user->getPseudo());
        return $this->redirectToRoute('admin_utilisateurs');
    }
    #[Route('/admin/utilisateurs/supprimers/{id}', name: 'admin_supprimer')]
    public function supprimerUtilisateurs(UserRepository $userRepository,int $id, EntityManagerInterface $manager): Response
    {
        $user = $userRepository->find($id);
        $pseudo = $user->getPseudo();
        $manager->remove($user);
        $manager->flush();
        $this->addFlash("success", "Utilisateur supprimé : " . $pseudo);
        return $this->redirectToRoute('admin_utilisateurs');
    }
    #[Route('/admin/sorties', name: 'admin_sorties')]
    public function listSorties(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('admin/listSorties.html.twig', [
            'controller_name' => 'AdminController',
            'sorties' => $sorties
        ]);
    }
    #[Route('/admin/sorties/annuler/{id}', name: 'admin_annuler')]
    public function annulerSortie(SortieRepository $sortieRepository, int $id, EntityManagerInterface $manager, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($id);
        $etat = $etatRepository->findOneBy(['libelle' => "Annulée"]);
        $sortie->setEtat($etat);
        $manager->persist($sortie);
        $manager->flush();
        $this->addFlash("success", "La sortie a été annulée : " . $sortie->getNom());
        return $this->redirectToRoute('admin_sorties');
    }
}
