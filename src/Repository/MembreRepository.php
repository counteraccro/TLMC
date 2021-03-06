<?php
namespace App\Repository;

use App\Entity\Membre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

/**
 *
 * @method Membre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Membre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Membre[] findAll()
 * @method Membre[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembreRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Membre::class);
    }

    /**
     * Retourne une liste de membres paginé en fonction de l'ordre et de la recherche courante
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
    public function findAllMembres(int $page = 1, int $max = 10, $params = array())
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

        // Nombre total de membre
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
     * Fonction qui permet de lister les membres et leurs réponses pour un questionnaire donné
     *
     * @param int $questionnaire_id
     * @param int $page
     * @param int $max
     * @param array $params
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function GetAllMembresReponsesByQuestionnaire($questionnaire_id, int $page = 1, int $max = 10, $search = '')
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
            ->join('m.reponses', 'rep')
            ->join('rep.question', 'quest')
            ->join('quest.questionnaire', 'q')
            ->andWhere('q.id = ' . $questionnaire_id)
            ->orderBy('rep.date', 'DESC')
            ->setMaxResults($max);

        if (! empty($search)) {
            $query->andWhere('(m.nom LIKE :search OR m.prenom LIKE :search OR m.username LIKE :search)');
            $query->setParameter('search', '%' . $search . '%');
        }

        $paginator = new Paginator($query);

        $query = $this->createQueryBuilder('m')
            ->select('COUNT(DISTINCT m.id)')
            ->join('m.reponses', 'rep')
            ->join('rep.question', 'quest')
            ->join('quest.questionnaire', 'q')
            ->andWhere('q.id = ' . $questionnaire_id);

        if (! empty($search)) {
            $query->andWhere('(m.nom LIKE :search OR m.prenom LIKE :search OR m.username LIKE :search)');
            $query->setParameter('search', '%' . $search . '%');
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
     * Fonction qui renvoie les membres présents dans l'annuaire dont le prénom ou nom contient le paramètre de la fonction
     * Permet de renvoyer des suggestions à l'autocomplete
     *
     * @param string $term
     */
    public function findByTerm($term)
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.nom LIKE :term')
            ->orWhere('m.prenom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderby('m.nom', 'ASC');
        $result = $query->getQuery()->getResult();

        return $result;
    }

    // /**
    // * @return Membre[] Returns an array of Membre objects
    // */
    /*
     * public function findByExampleField($value)
     * {
     * return $this->createQueryBuilder('m')
     * ->andWhere('m.exampleField = :val')
     * ->setParameter('val', $value)
     * ->orderBy('m.id', 'ASC')
     * ->setMaxResults(10)
     * ->getQuery()
     * ->getResult()
     * ;
     * }
     */

    /*
     * public function findOneBySomeField($value): ?Membre
     * {
     * return $this->createQueryBuilder('m')
     * ->andWhere('m.exampleField = :val')
     * ->setParameter('val', $value)
     * ->getQuery()
     * ->getOneOrNullResult()
     * ;
     * }
     */
}
