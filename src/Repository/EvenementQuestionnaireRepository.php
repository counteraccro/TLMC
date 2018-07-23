<?php

namespace App\Repository;

use App\Entity\EvenementQuestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EvenementQuestionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method EvenementQuestionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method EvenementQuestionnaire[]    findAll()
 * @method EvenementQuestionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementQuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EvenementQuestionnaire::class);
    }

//    /**
//     * @return EvenementQuestionnaire[] Returns an array of EvenementQuestionnaire objects
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
    public function findOneBySomeField($value): ?EvenementQuestionnaire
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
