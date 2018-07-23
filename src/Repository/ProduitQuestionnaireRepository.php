<?php

namespace App\Repository;

use App\Entity\ProduitQuestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProduitQuestionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitQuestionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitQuestionnaire[]    findAll()
 * @method ProduitQuestionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitQuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProduitQuestionnaire::class);
    }

//    /**
//     * @return ProduitQuestionnaire[] Returns an array of ProduitQuestionnaire objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProduitQuestionnaire
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
