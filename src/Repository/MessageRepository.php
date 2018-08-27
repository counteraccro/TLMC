<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }


    /**
     * Fonction permettant d'obtenir le nombre de messages non-lus pour un membre
     * @param int $id_membre
     */
    public function nbMessageNoRead($id_membre)
    {
        $return = $this->createQueryBuilder('m')
        ->select('count(m.id) as nb_message')
        ->join('m.messageLus', 'ml')
        ->andWhere('m.destinataire = ' . $id_membre)
        ->andWhere('ml.lu = 0')
        ->getQuery()
        ->getArrayResult();
        
        return $return[0]['nb_message'];
    }
    
    /**
     * 
     */
    public function findByUserByParameter($id_membre, $brouillon = 0, $role = 'destinataire',  int $page = 1, int $max = 10, $search = '')
    {
        
        if (! is_numeric($page)) {
            throw new \InvalidArgumentException('$page doit être un integer (' . gettype($page) . ' : ' . $page . ')');
        }
        
        if (! is_numeric($max)) {
            throw new \InvalidArgumentException('$max doit être un integer (' . gettype($max) . ' : ' . $max . ')');
        }
        
        $firstResult = ($page - 1) * $max;
        
        $query = $this->createQueryBuilder('m')
        ->setFirstResult($firstResult)
        ->leftJoin('m.messageLus', 'ml')
        ->andWhere('m.' . $role . ' = ' . $id_membre )
        ->andWhere('(m.brouillon = ' . $brouillon . ' OR m.brouillon IS NULL)')
        ->andWhere('(ml.corbeille = 0  OR ml.corbeille IS NULL)')
        ->setMaxResults($max);
        
        if (! empty($search)) {
            //@todo : Recherche à faire
        }
        
        $paginator = new Paginator($query);
        
        $query = $this->createQueryBuilder('m')
        ->select('COUNT(DISTINCT m.id)')
        ->leftJoin('m.messageLus', 'ml')
        ->andWhere('m.' . $role . ' = ' . $id_membre)
        ->andWhere('(m.brouillon = ' . $brouillon . ' OR m.brouillon IS NULL)')
        ->andWhere('(ml.corbeille = 0  OR ml.corbeille IS NULL)');
        
        if (! empty($search)) {
            //$query->andWhere('(m.nom LIKE :search OR m.prenom LIKE :search OR m.username LIKE :search)');
            //$query->setParameter('search', '%' . $search . '%');
        }
        
        // Génération des paramètres SQL
        $result = $query->getQuery()->getSingleScalarResult();
        
        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }
        
        return array(
            'paginator' => $paginator,
            'nb' => $result
        );
    }
    
    /**
     *
     */
    public function findCorbeilleByUser($id_membre, int $page = 1, int $max = 10, $search = '')
    {
        if (! is_numeric($page)) {
            throw new \InvalidArgumentException('$page doit être un integer (' . gettype($page) . ' : ' . $page . ')');
        }
        
        if (! is_numeric($max)) {
            throw new \InvalidArgumentException('$max doit être un integer (' . gettype($max) . ' : ' . $max . ')');
        }
        
        $firstResult = ($page - 1) * $max;
        
        $query = $this->createQueryBuilder('m')
        ->setFirstResult($firstResult)
        ->join('m.messageLus', 'ml')
        ->andWhere('(m.expediteur = ' . $id_membre . ' OR m.destinataire = ' . $id_membre . ')')
        ->andWhere('ml.corbeille = 1')
        ->setMaxResults($max);
        
        if (! empty($search)) {
            //$query->andWhere('(m.nom LIKE :search OR m.prenom LIKE :search OR m.username LIKE :search)');
            //$query->setParameter('search', '%' . $search . '%');
        }
        
        $paginator = new Paginator($query);
        
        $query = $this->createQueryBuilder('m')
        ->select('COUNT(DISTINCT m.id)')
        ->join('m.messageLus', 'ml')
        ->andWhere('(m.expediteur = ' . $id_membre . ' OR m.destinataire = ' . $id_membre . ')')
        ->andWhere('ml.corbeille = 1');
        
        if (! empty($search)) {
            //$query->andWhere('(m.nom LIKE :search OR m.prenom LIKE :search OR m.username LIKE :search)');
            //$query->setParameter('search', '%' . $search . '%');
        }
        
        // Génération des paramètres SQL
        $result = $query->getQuery()->getSingleScalarResult();
        
        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }
        
        return array(
            'paginator' => $paginator,
            'nb' => $result
        );
    }
    
//    /**
//     * @return Message[] Returns an array of Message objects
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
    public function findOneBySomeField($value): ?Message
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
