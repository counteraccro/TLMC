<?php

namespace App\Repository;

use App\Entity\ProduitSpecialite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProduitSpecialite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitSpecialite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitSpecialite[]    findAll()
 * @method ProduitSpecialite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitSpecialiteRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProduitSpecialite::class);
    }

//    /**
//     * @return ProduitSpecialite[] Returns an array of ProduitSpecialite objects
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
    public function findOneBySomeField($value): ?ProduitSpecialite
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
