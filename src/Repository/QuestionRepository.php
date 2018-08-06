<?php
namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[] findAll()
 * @method Question[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Récupère l'ordre le plus grand pour la question
     * @return mixed|\Doctrine\DBAL\Driver\Statement|array|NULL
     */
    public function getMaxOrdre(int $questionnaire_id)
    {
        return $this->createQueryBuilder('q')
            ->select('MAX(q.ordre) AS max_ordre')
            ->join('q.questionnaire', 'quest')
            ->where('quest.id = :quest_id')
            ->setParameter('quest_id', $questionnaire_id)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    // /**
    // * @return Question[] Returns an array of Question objects
    // */
    /*
     * public function findByExampleField($value)
     * {
     * return $this->createQueryBuilder('q')
     * ->andWhere('q.exampleField = :val')
     * ->setParameter('val', $value)
     * ->orderBy('q.id', 'ASC')
     * ->setMaxResults(10)
     * ->getQuery()
     * ->getResult()
     * ;
     * }
     */

    /*
     * public function findOneBySomeField($value): ?Question
     * {
     * return $this->createQueryBuilder('q')
     * ->andWhere('q.exampleField = :val')
     * ->setParameter('val', $value)
     * ->getQuery()
     * ->getOneOrNullResult()
     * ;
     * }
     */
}
