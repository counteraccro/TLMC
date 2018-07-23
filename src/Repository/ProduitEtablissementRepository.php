<?php

namespace App\Repository;

use App\Entity\ProduitEtablissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProduitEtablissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitEtablissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitEtablissement[]    findAll()
 * @method ProduitEtablissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitEtablissementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProduitEtablissement::class);
    }

//    /**
//     * @return ProduitEtablissement[] Returns an array of ProduitEtablissement objects
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
    public function findOneBySomeField($value): ?ProduitEtablissement
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
