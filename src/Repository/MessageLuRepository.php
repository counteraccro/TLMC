<?php

namespace App\Repository;

use App\Entity\MessageLu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageLu|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageLu|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageLu[]    findAll()
 * @method MessageLu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageLuRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageLu::class);
    }

//    /**
//     * @return MessageLu[] Returns an array of MessageLu objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessageLu
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
