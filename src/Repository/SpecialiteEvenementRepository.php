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
    
    /**
     * Récupération des spécialités liées à un événement ordonnées par le nom des établissements et le nom du service
     * 
     * @param int $id_evenement
     * @return array
     */
    public function getSpecialites(int $id_evenement){
        
        $specialites = $this->createQueryBuilder('se')
        ->select("E.id as EtablissementId, se.id, E.nom as etablissement, S.service as specialite, se.statut, se.date")
        ->innerJoin('App:Specialite', 'S', 'WITH', 'se.specialite = S.id')
        ->innerJoin('App:Etablissement', 'E', 'WITH', 'S.etablissement = E.id')
        ->andWhere('se.evenement = :idEvenement')
        ->setParameter('idEvenement', $id_evenement)
        ->orderBy('E.nom ASC, S.service')
        ->getQuery()
        ->getResult();
        
        if (count($specialites) == 1 && is_null($specialites[0]['EtablissementId'])) {
            $specialites = array();
        }
        
        $connexions = array();
        
        foreach ($specialites as $specialite) {
            if (isset($connexions[$specialite['EtablissementId']])) {
                $connexions[$specialite['EtablissementId']][] = $specialite;
            } else {
                $connexions[$specialite['EtablissementId']][0] = $specialite;
            }
        }
        
        return $connexions;
    }
    
    /**
     * Récupération des événements liées à une spécialité ordonnés par le nom de l'événement
     * 
     * @param int $id_specialite
     * @return array
     */
    public function getEvenements(int $id_specialite){
        
        $evenements = $this->createQueryBuilder('se')
        ->select("se.id, E.nom as evenement, se.statut, se.date")
        ->innerJoin('App:Evenement', 'E', 'WITH', 'se.evenement = E.id')
        ->andWhere('se.specialite = :idSpecialite')
        ->setParameter('idSpecialite', $id_specialite)
        ->orderBy('E.nom')
        ->getQuery()
        ->getResult();
        
        $connexions = array();
        if (count($evenements) == 1 && is_null($evenements[0]['id'])) {
            $evenements = array();
        }
        
        foreach ($evenements as $evenement) {
            $connexions[$evenement['id']] = array($evenement);
        }
        
        return $connexions;
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
