<?php

namespace App\Repository;

use App\Entity\TypeProduit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypeProduit|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeProduit|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeProduit[]    findAll()
 * @method TypeProduit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeProduitRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeProduit::class);
    }

//    /**
//     * @return TypeProduit[] Returns an array of TypeProduit objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeProduit
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
