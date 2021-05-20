<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UploadFichierType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    #[Route('/admin/charger', name: 'admin_charger')]
    public function chargerFichierCSV(Request $request,
                                      UserPasswordEncoderInterface $passwordEncoder,
                                        EntityManagerInterface $manager,
                                      CampusRepository $campusRepository){
        $csvForm = $this->createForm(UploadFichierType::class);

        $csvForm->handleRequest($request);
        if ($csvForm->isSubmitted() && $csvForm->isValid()){
            $data = $csvForm->getData();
            $filecCSV = $data['csv'];

            //On ouvre le fichier
            $handle=fopen($filecCSV->getRealPath(), 'r');
            //on récupère les lignes une par une

            while (($data = fgetcsv($handle, 1000, ";")) !== false){

                //On recupère le campus
                $idCampus = $data[0];
                $campus = $campusRepository->find($idCampus);

                //On créer un nouveau utilisateur
                $user = new  User();
                $user->setCampus($campus);
                $user->setEmail($data[1]);
                $user->setRoles([$data[2]]);
                //On recupère le mot de passe et on le hash
                $mdp = $data[3];
                $hash = $passwordEncoder->encodePassword($user, $mdp);
                $user->setPassword($hash);

                $user->setNom($data[4]);
                $user->setPrenom($data[5]);
                $user->setTelephone($data[6]);
                $user->setAdministrateur($data[7]);
                $user->setActif($data[8]);
                $user->setPseudo($data[9]);
                $user->setPicture($data[10]);

                $manager->persist($user);
            }
            $manager->flush();
            fclose($handle);

            $this->addFlash('succes', 'Les données du fichiers ont été intégré a la base de donnée');
            $this->redirectToRoute('admin_utilisateurs');
        }
        return $this->render('admin/uploadFichier.html.twig', [
            'controller_name' => 'AdminController',
            'csvForm' => $csvForm->createView()]);

    }
}
