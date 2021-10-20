<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/update", name="profil_update")
     */
    public function update(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger): Response
    {
        //recuperation du repository et recuperation des info utilisateur
        $user = $manager->getRepository(User::class)->find($this->getUser()->getId());
        $user2 = $user->getPassword();

        //creation du formulaire
        $formUser = $this->createForm(ProfilType::class, $user);
        //recupération des info dans la requete
        $formUser->handleRequest($request);
        //traitement du formulaire

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $PicFile = $formUser->get('picture')->getData();
            if ($PicFile) {
                $originalFilename = pathinfo($PicFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$PicFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $PicFile->move(
                        $this->getParameter('picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $user->setPicture($newFilename);
            }
            $password = $user->getPassword();
            if (!is_null($password)) {
                $hashed = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($hashed);
            } else {
                $user->setPassword($user2);
            }
            //insertion en bdd
            $manager->persist($user);
            $manager->flush();
            //Redirection
            $this->addFlash('success', 'Votre profil a bien été modifié.');
            return $this->redirectToRoute("profil_detail", ["id" => $user->getId()]);
        }

       $this->addFlash('verify_email_error', 'adresse mail déjà utilisé');
        return $this->render("profil/update.html.twig", [
            'formUser' => $formUser->createView(),
            'user' => $user
        ]);

    }

    /**
     * @Route("/profil/detail/{id}", name="profil_detail")
     */
    public function detail(EntityManagerInterface $manager, int $id): Response
    {
        $user = $manager->getRepository(User::class)->find($id);

        $campus = $user->getCampus();

        if(!$user){
            throw $this->createNotFoundException("Utilisateur inconnu");
        }
        return $this->render('profil/details.html.twig', [
            'user' => $user,
            'campus' => $campus
        ]);
    }


}
