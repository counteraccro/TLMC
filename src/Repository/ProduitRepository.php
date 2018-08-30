<?php
namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

/**
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[] findAll()
 * @method Produit[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Retourne une liste de produit paginé en fonction de l'ordre et de la recherche courante
     *
     * @param int $page
     * @param int $max
     * @param array $params
     *            [order] => ordre de tri
     *            [page] => page (pagination)
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau contenant des conditions supplémentaires en dehors des filtres de l'utilisateur
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function findAllProduits(int $page = 1, int $max = 10, $params = array())
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

        // Nombre total de produit
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
                    // traitement des liaisons avec une autre table
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

        if (isset($params['condition'])) {
            foreach ($params['condition'] as $condition) {
                $query->andWhere($condition);
            }
        }

        return $query;
    }

    /**
     * Requête permettant de récupérer les différentes liaisons d'un produit avec une spécialité et un établissement
     *
     * @param int $id_produit
     * @return array $connexions
     */
    public function findEtablissementAndSpecialite(int $id_produit)
    {
        $lien_etablissement = $this->createQueryBuilder('p')
            ->select("E.id as EtablissementId, PE.id, E.nom as etablissement, '' as specialite, PE.quantite, PE.date")
            ->leftjoin('App:ProduitEtablissement', 'PE', 'WITH', 'PE.produit = p.id')
            ->leftjoin('App:Etablissement', 'E', 'WITH', 'PE.etablissement = E.id')
            ->andWhere('p.id = :idProduit')
            ->setParameter('idProduit', $id_produit)
            ->orderBy('E.nom')
            ->getQuery()
            ->getResult();

        $lien_specialite = $this->createQueryBuilder('p')
            ->select("E.id as EtablissementId, PS.id, E.nom as etablissement, S.service as specialite, PS.quantite, PS.date")
            ->leftjoin('App:ProduitSpecialite', 'PS', 'WITH', 'PS.produit = p.id')
            ->leftjoin('App:Specialite', 'S', 'WITH', 'PS.specialite = S.id')
            ->leftjoin('App:Etablissement', 'E', 'WITH', 'S.etablissement = E.id')
            ->andWhere('p.id = :idProduit')
            ->setParameter('idProduit', $id_produit)
            ->orderBy('E.nom ASC, S.service')
            ->getQuery()
            ->getResult();

        if (count($lien_etablissement) == 1 && is_null($lien_etablissement[0]['EtablissementId'])) {
            $lien_etablissement = array();
        }
        if (count($lien_specialite) == 1 && is_null($lien_specialite[0]['EtablissementId'])) {
            $lien_specialite = array();
        }

        $liens = array_merge($lien_etablissement, $lien_specialite);
        $connexions = array();

        foreach ($liens as $lien) {
            if (isset($connexions[$lien['EtablissementId']])) {
                $connexions[$lien['EtablissementId']][] = $lien;
            } else {
                $connexions[$lien['EtablissementId']][0] = $lien;
            }
        }

        return $connexions;
    }

    /**
     * Récupération des produits liés à un établissement ou une spécialité
     * 
     * @param bool $admin
     * @param int $id_etablissement
     * @param int $id_specialite
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProduitAvailable(bool $admin, int $id_etablissement, int $id_specialite = null)
    {
        $return = $this->createQueryBuilder('p');
        
        if (! $admin) {
            if (is_null($id_specialite)) {
                $return->innerJoin('App:ProduitEtablissement', 'pe', 'WITH', 'pe.produit = p.id')->andWhere('pe.etablissement = ' . $id_etablissement);
            } else {
                $return->innerJoin('App:ProduitSpecialite', 'ps', 'WITH', 'ps.produit = p.id')->andWhere('ps.specialite = ' . $id_specialite);
            }
            $return->andWhere('p.disabled = 0');
        }
        
        $return->orderBy('p.titre', 'ASC')->groupBy('p.id');
        
        return $return;
    }

    // /**
    // * @return Produit[] Returns an array of Produit objects
    // */
    /*
     * public function findByExampleField($value)
     * {
     * return $this->createQueryBuilder('p')
     * ->andWhere('p.exampleField = :val')
     * ->setParameter('val', $value)
     * ->orderBy('p.id', 'ASC')
     * ->setMaxResults(10)
     * ->getQuery()
     * ->getResult()
     * ;
     * }
     */

    /*
     * public function findOneBySomeField($value): ?Produit
     * {
     * return $this->createQueryBuilder('p')
     * ->andWhere('p.exampleField = :val')
     * ->setParameter('val', $value)
     * ->getQuery()
     * ->getOneOrNullResult()
     * ;
     * }
     */
}
