<?php

namespace App\Repository;

use App\Entity\SpecialiteEvenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpecialiteEvenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialiteEvenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialiteEvenement[]    findAll()
 * @method SpecialiteEvenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialiteEvenementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpecialiteEvenement::class);
    }

//    /**
//     * @return SpecialiteEvenement[] Returns an array of SpecialiteEvenement objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SpecialiteEvenement
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
