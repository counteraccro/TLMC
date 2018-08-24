<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
     * 
     * @param unknown $id_membre
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
    public function findByUserByParameter($id_membre, $brouillon = 0, $role = 'destinataire')
    {
        $return = $this->createQueryBuilder('m')
        ->join('m.messageLus', 'ml')
        ->andWhere('m.' . $role . ' = ' . $id_membre)
        ->andWhere('m.brouillon = ' . $brouillon)
        ->andWhere('ml.corbeille = 0')
        ->getQuery()
        ->getResult();
        
        return $return;
    }
    
    /**
     *
     */
    public function findCorbeilleByUser($id_membre)
    {
        $return = $this->createQueryBuilder('m')
        ->join('m.messageLus', 'ml')
        ->andWhere('(m.expediteur = ' . $id_membre . ' OR m.destinataire = ' . $id_membre . ')')
        ->andWhere('ml.corbeille = 1')
        ->getQuery()
        ->getResult();
        
        return $return;
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
