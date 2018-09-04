<?php

namespace App\Repository;

use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * @method Groupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Groupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Groupe[]    findAll()
 * @method Groupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Groupe::class);
    }
    
    /**
     * Fonction permettant de récupérer les groupes du membre courant
     */
    public function findByUser($id_membre)
    {   
        $query = $this->createQueryBuilder('g')
        ->select('COUNT(DISTINCT g.id)');
        /*SELECT DISTINCT groupe.id, groupe.nom, groupe_membre.membre_id, membre.prenom, membre.nom
         * FROM groupe join groupe_membre ON groupe_membre.groupe_id = groupe.id
         * JOIN membre ON membre.id = groupe_membre.membre_id
         * WHERE membre.id = 7*/

        $result = $query->getQuery()->getSingleScalarResult();
        
        return array(
            'result' => $result
        );
    }

//    /**
//     * @return Groupe[] Returns an array of Groupe objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Groupe
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
