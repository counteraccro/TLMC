<?php

namespace App\Repository;

use App\Entity\FamilleAdresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method FamilleAdresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamilleAdresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamilleAdresse[]    findAll()
 * @method FamilleAdresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilleAdresseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FamilleAdresse::class);
    }
    
    /**
     * Retourne une liste d'adresse paginé en fonction de l'ordre et de la recherche courante
     * 
     * @param int $page
     * @param int $max
     * @param array $params
     *            [order] => ordre de tri
     *            [page] => page (pagination)
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function findAllFamilleAdresses(int $page = 1, int $max = 10, $params = array())
    {
        if (! is_numeric($page)) {
            throw new \InvalidArgumentException('$page doit être un integer (' . gettype($page) . ' : ' . $page . ')');
        }
        
        if (! is_numeric($max)) {
            throw new \InvalidArgumentException('$max doit être un integer (' . gettype($max) . ' : ' . $max . ')');
        }
        
        if (! isset($params['field']) && ! isset($params['order'])) {
            throw new \InvalidArgumentException('order et field ne sont pas présents comme clés dans $params');
        }
        
        $firstResult = ($page - 1) * $max;
        
        // pagination
        $query = $this->createQueryBuilder($params['repository'])->setFirstResult($firstResult);
        
        if(isset($params['search']))
        {
            foreach($params['search'] as $searchKey => $valueKey)
            {
                //if(!empty($valueKey)){
                    $query->andWhere(str_replace('-', '.', $searchKey) . " LIKE '%" . $valueKey . "%'");
                //}
            }
        }
        
        $query->orderBy($params['repository'] . '.' . $params['field'], $params['order'])->setMaxResults($max);
        $paginator = new Paginator($query);
        
        // Nombre total d'adresse
        $result = $this->createQueryBuilder($params['repository'])
        ->select('COUNT(' . $params['repository'] . '.id)')
        ->getQuery()
        ->getSingleScalarResult();
        
        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }
        
        return array(
            'paginator' => $paginator,
            'nb' => $result
        );
    }

//    /**
//     * @return FamilleAdresse[] Returns an array of FamilleAdresse objects
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
    public function findOneBySomeField($value): ?FamilleAdresse
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
