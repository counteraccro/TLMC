<?php
namespace App\Repository;

use App\Entity\Questionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Membre;

/**
 * Méthodes présentes dans ce repository
 * @method Questionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Questionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Questionnaire[] findAll()
 * @method Questionnaire[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionnaireRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Questionnaire::class);
    }

    /**
     * Retourne une liste de questionnaire paginé en fonction de l'ordre et de la recherche courante
     *
     * @param int $page
     * @param int $max
     * @param array $params
     *            [order] => ordre de tri
     *            [page] => page (pagination)
     *            [search] => tableau contenant les éléments de la recherche
     *            [repository] => repository (objet courant)
     *            [field] => champ de tri,
     *            [condition] => tableau de conditions pour le listing des éléments
     * @throws \InvalidArgumentException
     * @throws NotFoundHttpException
     * @return \Doctrine\ORM\Tools\Pagination\Paginator[]|mixed[]|\Doctrine\DBAL\Driver\Statement[]|array[]|NULL[]
     */
    public function findAllQuestionnaires(int $page = 1, int $max = 10, $params = array())
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

        // Nombre total de questionnaire
        $query = $this->createQueryBuilder($params['repository'])->select('COUNT(' . $params['repository'] . '.id)');

        // Génération des paramètres SQL
        $query = $this->generateParamsSql($query, $params);
        $result = $query->getQuery()->getSingleScalarResult();

        if (($paginator->count() <= $firstResult) && $page != 1) {
            throw new NotFoundHttpException('Page not found');
        }

        /**
         * Pour connaitre le nombre de participants pour chaque questionnaire
         * On utilise le champs description de questionnaire pour stocker le calcul et
         * l'afficher dans la vue
         */
        $nbParticipants = $this->getNbParticipantsRepondu($page, $max, $params);
        foreach ($paginator as &$questionnaire) {
            foreach ($nbParticipants as $val) {
                if ($val['id'] == $questionnaire->getId()) {
                    $questionnaire->setDescription($val['nb_participants']);
                } else if (! is_numeric($questionnaire->getDescription())) {
                    $questionnaire->setDescription(0);
                }
            }
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
     *            [sans_inactif] => boolean,
     *            [condition] => tableau de conditions pour le listing des éléments
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
     * Requête permettant d'indiquer le nombre de participants ayant répondu
     * @param int $page
     * @param int $max
     * @param array $params
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    public function getNbParticipantsRepondu(int $page = 1, int $max = 10, $params = array())
    {
        $firstResult = ($page - 1) * $max;
        $query = $this->createQueryBuilder($params['repository'])->setFirstResult($firstResult);

        $query->select($params['repository'] . '.id as id, count(distinct m.id) as nb_participants');

        if (isset($params['search'])) {
            foreach ($params['search'] as $searchKey => $valueKey) {
                $query->andWhere(str_replace('-', '.', $searchKey) . " LIKE '%" . $valueKey . "%'");
            }
        }

        $query->join($params['repository'] . '.questions', 'qu');
        $query->leftJoin('qu.reponses', 'r');
        $query->leftJoin('r.membre', 'm');
        $query->addGroupBy($params['repository'] . '.id');
        $query->setMaxResults($max);
        return $query->getQuery()->getArrayResult();
    }

    /**
     * Requête permettant d'indiquer le nombre de participants ayant répondu à un questionnaire donné
     * @param int $id_questionnaire
     * @return array|mixed|\Doctrine\DBAL\Driver\Statement|NULL
     */
    public function getNbParticipantsReponduByQuestionnaire($id_questionnaire)
    {
        $query = $this->createQueryBuilder('q');
        $query->select('count(distinct m.id) as nb_participants');
        $query->join('q.questions', 'qu');
        $query->leftJoin('qu.reponses', 'r');
        $query->leftJoin('r.membre', 'm');
        $query->andWhere('q.id = ' . $id_questionnaire);
        return $query->getQuery()->getArrayResult();
    }

    /**
     * Regarde si le membre à déjà répondu ou non au questionnaire en checkant si
     * une réponse pour ce membre est présente
     *
     * @param int $questionnaire_id
     * @param int $id_membre
     * @return true si oui, false si non
     */
    public function HasAnswered($questionnaire_id, $id_membre)
    {
        $return = $this->createQueryBuilder('q')
            ->select('rep.id')
            ->join('q.questions', 'qu')
            ->join('qu.reponses', 'rep')
            ->andWhere('rep.membre = ' . $id_membre)
            ->andWhere('q.id = ' . $questionnaire_id)
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        // Si pas de réponses tout est ok
        if (empty($return)) {
            return false;
        }
        return true;
    }
    
    /**
     * Requête qui vérifie si un couple 'slug/titre' existe pour un questionnaire
     * Permet de s'assurer de l'unicité du titre et du slug d'un questionnaire
     * @param string $questionnaire_titre
     * @param string $slug
     * @return boolean
     */
    public function isExistingTitleAndSlug($questionnaire_titre, $questionnaire_slug)
    {
        $return = $this->createQueryBuilder('q')
        ->select('q.titre', 'q.slug')
        ->andWhere('q.titre = :titre')
        ->andWhere('q.slug = :slug')
        ->setParameter('titre', $questionnaire_titre)
        ->setParameter('slug', $questionnaire_slug)
        ->getQuery()
        ->getArrayResult();
        
        // Si pas de réponses tout est ok
        if (empty($return)) {
            return false;
        }
        return true;
    }
    
    /**
     * Fonction qui retourne pour un membre donné les questionnaires répondus ainsi que les réponses associées
     * @param int $membre_id
     */
    public function findByMembreReponses($membre_id) {
        $query = $this->createQueryBuilder('q')
        //->select('q.titre, quest.libelle, quest.liste_valeur, quest.type, rep.valeur')
        ->join('q.questions', 'quest')
        ->join('quest.reponses', 'rep')
        ->andWhere('rep.membre = :membre_id')
        ->setParameter('membre_id', $membre_id)
        ->addOrderBy('q.id', 'ASC')
        ->getQuery()
        ->getResult();
        
        return $query;
    }
    
    /**
     * Fonction qui retourne les questionnaires publiés et dont la date de fin n'est pas dépassée
     */
    public function findAllActive() {
        $query = $this->createQueryBuilder('q')
        ->andWhere('q.date_publication IS NOT NULL')
        ->andWhere('q.date_fin > CURRENT_TIMESTAMP()')
        ->addOrderBy('q.titre', 'ASC')
        ->getQuery()
        ->getResult();
        
        return $query;
    }
    
    // /**
    // * @return Questionnaire[] Returns an array of Questionnaire objects
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
     * public function findOneBySomeField($value): ?Questionnaire
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
