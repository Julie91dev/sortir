<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class InscriptionController extends AbstractController
{
    #[Route('/sortie/inscription/{id}', name: 'inscription', methods: ["GET","POST"])]
    /**
     * @ParamConverter("sortie", options={"id"="id"})
     *
     */
        public function inscription(EntityManagerInterface $manager, SortieRepository $sortieRepository,int $id): Response
    {

        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);
        $participant = $this->getUser();



        //la sortie doit être dans l'état Ouverte pour qu'on puisse s'y inscrire
        if ($sortie->getEtat()->getLibelle() !== "Ouverte"){
            $this->addFlash("danger", "Cette sortie n'est pas ouverte aux inscriptions !");
            return $this->redirectToRoute('sortie_detail', ["id" => $sortie->getId()]);
        }
        //désincription si on trouve cette inscription
        $listeParticipants = $sortie->getParticipants();
        foreach ($listeParticipants as $participantSortie) {
            if ($participant->getId() == $participantSortie->getId()) {
                //supprime l'inscription
                $sortie->removeParticipant($participant);
                $manager->persist($sortie);
                $manager->flush();

                $this->addFlash("success", "Vous êtes désinscrit !");
                return $this->redirectToRoute('main_home', ["id" => $sortie->getId()]);
            }
        }

        //la sortie est-elle complète ?
        if ($sortie->isComplet()){
            $this->addFlash("danger", "Cette sortie est complète !");
            return $this->redirectToRoute('sortie_detail', ["id" => $sortie->getId()]);
        }

        //si on s'est rendu jusqu'ici, c'est que tout est ok. On sauvegarde l'inscription.
        //$user = $manager->getRepository(User::class)->find($this->getUser());

        $inscription = $sortie->addParticipant($participant);


        $manager->persist($inscription);
        $manager->flush();

        //on refresh la sortie pour avoir le bon nombre d'inscrits avant le tchèque ci-dessous
        $manager->refresh($sortie);

        //maintenant, on verifie si c'est complet pour changer son état
        if ($sortie->isComplet()){
           $etat = $manager->getRepository(Etat::class)->findBy(['libelle' => 'Ouverte'])[0];
            $sortie->setEtat($etat);
        }

        $this->addFlash("success", "Vous êtes inscrit !");
        return $this->redirectToRoute('sortie_detail', ["id" => $sortie->getId()]);
    }

    #[Route('/sortie/desister/{id}', name: 'inscription_desister', methods: ["GET","POST"])]
    public function desister($id, EntityManagerInterface $manager){
        //Recupération de la sortie
        $sortie = $this->getDoctrine()->getRepository(Sortie::class)->find($id);
        //Recupération du participant
         $participant = $this->getUser();

        $sortie->removeParticipant($participant);
        $manager->persist($sortie);
        $manager->flush();
        $this->addFlash("success", "Vous êtes désinscrit !");
        return $this->redirectToRoute('main_home');
    }


}
