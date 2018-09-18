<?php

namespace App\Repository;

use App\Entity\FamillePatient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FamillePatient|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamillePatient|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamillePatient[]    findAll()
 * @method FamillePatient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamillePatientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FamillePatient::class);
    }

//    /**
//     * @return FamillePatient[] Returns an array of FamillePatient objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FamillePatient
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
