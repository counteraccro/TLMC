<?php

namespace App\Repository;

use App\Entity\ExtensionFormulaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExtensionFormulaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExtensionFormulaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExtensionFormulaire[]    findAll()
 * @method ExtensionFormulaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtensionFormulaireRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExtensionFormulaire::class);
    }

//    /**
//     * @return ExtensionFormulaire[] Returns an array of ExtensionFormulaire objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExtensionFormulaire
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
