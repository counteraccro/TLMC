<?php

namespace App\Repository;

use App\Entity\Temoignage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Temoignage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temoignage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temoignage[]    findAll()
 * @method Temoignage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemoignageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Temoignage::class);
    }

    /**
     * Retourne une liste de témoignages paginé en fonction de l'ordre et de la recherche courante
     *
     * @param int $page
     * @param int $max
     * @param array $params
     *            [order] => ordre de tri
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau contenant des conditions supplémentaires en dehors des filtres de l'utilisateur
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function findAllTemoignages(int $page = 1, int $max = 10, $params = array())
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
        
        // Génération des paramètres SQL
        $query = $this->generateParamsSql($query, $params);
        
        $query->orderBy($params['repository'] . '.' . $params['field'], $params['order'])->setMaxResults($max);
        $paginator = new Paginator($query);
        
        // Nombre total de témoignage
        $query = $this->createQueryBuilder($params['repository'])->select('COUNT(' . $params['repository'] . '.id)');
        
        // Génération des paramètres SQL
        $query = $this->generateParamsSql($query, $params);
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
     * Génération de la requête
     *
     * @param QueryBuilder $query
     * @param array $params
     *            [order] => ordre de tri
     *            [page] => page (pagination)
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau contenant des conditions supplémentaires en dehors des filtres de l'utilisateur
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function generateParamsSql(QueryBuilder $query, array $params)
    {
        $index = 1;
        if (isset($params['search'])) {
            foreach ($params['search'] as $searchKey => $valueKey) {
                
                $explode_key = explode('-', $searchKey);
                if (count($explode_key) == 3) {
                    $query = $query->join($explode_key[0] . '.' . $explode_key[1], $explode_key[1]);
                    $query->andWhere($explode_key[1] . "." . $explode_key[2] . " LIKE :searchTerm$index");
                    $query->setParameter('searchTerm' . $index, '%' . $valueKey . '%');
                } else {
                    $query->andWhere(str_replace('-', '.', $searchKey) . " LIKE :searchTerm$index");
                    $query->setParameter('searchTerm' . $index, '%' . $valueKey . '%');
                }
                $index ++;
            }
        }
        
        if (isset($params['jointure'])) {
            foreach ($params['jointure'] as $jointure) {
                $query->join($jointure['oldrepository'] . '.' . $jointure['newrepository'], $jointure['newrepository']);
            }
        }
        
        if(isset($params['condition'])){
            foreach ($params['condition'] as $condition){
                $query->andWhere($condition);
            }
        }
        
        return $query;
    }
    
//    /**
//     * @return Temoignage[] Returns an array of Temoignage objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Temoignage
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
