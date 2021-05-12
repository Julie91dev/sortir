<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function searchSorties($campusSelect, $search, $dateDebut, $dateFin, $isInscrit, $organiser,
                                                    $isNotInscrit, $passee, $userId, $dateDuJour, $dateDuJourMoinsUnMois)
    {

        $db =  $this->createQueryBuilder('s');

            //si le campus est choisi
            if ($campusSelect == 1 or $campusSelect == 2 or $campusSelect == 3){

                $db->join('s.campus', 'campus')

                    ->where('campus.id = :campus')
                    ->setParameter('campus', $campusSelect);

            }
             //si la recherche de nom
                if ($search){
                    $db->andWhere('s.nom LIKE :search')
                        ->setParameter('search', '%'.$search.'%');
                }
            //Si les dates sont renseignées
                if ($dateDebut && $dateFin){
                    $db->andWhere('s.dateHeureDebut BETWEEN :dateDebut and :dateFin')
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin);
                }
            //Si l'organisateur est coché
                if ($organiser == true){

                        $db->andWhere('s.organisateur = :organisateur')
                        ->setParameter('organisateur', $userId);

                }
            //Si liste inscrit
                if($isInscrit == true && $isNotInscrit == false){
                    $db->andWhere(':isInscrit MEMBER OF s.participants')
                        ->setParameter('isInscrit', $userId);
                }
            //Si liste non inscrit
                if ($isNotInscrit == true && $isInscrit == false){
                   $db ->andWhere(':isNotInscrit NOT MEMBER OF s.participants')
                        ->setParameter('isNotInscrit', $userId);
                }
            //Si passée
                if($passee == true){
                   $db ->andwhere('s.dateHeureDebut BETWEEN :dateDuJourMoinsUnMois and :dateDuJour')
                       ->setParameter('dateDuJourMoinsUnMois', $dateDuJourMoinsUnMois)
                    ->setParameter('dateDuJour', $dateDuJour );
                }
        //Si pas passée
            if(!($passee == true)){
                $db ->andwhere('s.dateHeureDebut >= :dateDuJour')
                    ->setParameter('dateDuJour', $dateDuJour );
            }
        $db->addOrderBy('s.dateHeureDebut', 'DESC');
        return $db->getQuery()
                    ->getResult();

    }

   /* public function findSortiesPassees($dateDuJour)
    {
        return $this->createQueryBuilder('s')

                    ->where('s.dateHeureDebut < :dateDuJour')
                    ->setParameter('dateDuJour', $dateDuJour )
                    ->getQuery()
                    ->getResult();
    }

    public function findSearchCharSortie(string $search)
    {
        return $this->createQueryBuilder('s')
                    ->Where('s.nom LIKE :search')
                    ->setParameter('search', '%'.$search.'%')
                    ->getQuery()
                    ->getResult();
        }

    public function findDatesSorties($dateDebut, $dateFin)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut BETWEEN :dateDebut and :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }*/

}
