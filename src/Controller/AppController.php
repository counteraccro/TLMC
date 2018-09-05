<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Membre;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Produit;
use App\Entity\Evenement;
use Doctrine\ORM\Mapping\Entity;
use App\Entity\Etablissement;
use App\Entity\Specialite;
use App\Entity\ProduitEtablissement;
use App\Entity\ProduitSpecialite;

class AppController extends Controller
{

    /**
     * Nombre maximum d'éléments pour la pagination
     *
     * @var integer
     */
    const MAX_NB_RESULT = 20;

    /**
     * Nombre maximum d'éléments pour la pagination en ajax
     *
     * @var integer
     */
    const MAX_NB_RESULT_AJAX = 5;

    /**
     * Indentifiant pour la recherche courante
     *
     * @var string
     */
    const CURRENT_SEARCH = 'current_search';

    /**
     * Tableau de parenté
     *
     * @var array
     */
    const FAMILLE_PARENTE = array(
        1 => 'Père',
        2 => 'Mère',
        3 => 'Frère',
        4 => 'Soeur',
        5 => 'Cousin',
        6 => 'Cousine',
        7 => 'Demi-frère',
        8 => 'Demi-soeur',
        9 => 'Conjoint',
        10 => 'Enfant',
        11 => 'Parrain',
        12 => 'Marraine',
        13 => 'Petit-enfant'
    );

    const CHOICETYPE = 'ChoiceType';

    const TEXTYPE = 'TextType';

    const TEXTAREATYPE = 'TextareaType';

    const CHECKBOXTYPE = 'CheckboxType';

    const RADIOTYPE = 'RadioType';

    const QUESTION_TYPE = array(
        self::CHOICETYPE => 'Liste déroulante',
        self::TEXTYPE => 'Champ texte',
        self::TEXTAREATYPE => 'Zone de texte',
        self::CHECKBOXTYPE => 'Case à cocher',
        self::RADIOTYPE => 'Choix unique'
    );

    const QUESTION_REGLES_REGEX = array(
        '.' => 'Tout accepter',
        '^[\d]*$' => 'Chiffres uniquement',
        "^[a-zA-Z\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$" => 'Lettres uniquement',
        "^[a-z\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$" => 'Lettres minuscules uniquement',
        "^[A-Z\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$" => 'Lettres majuscules uniquement',
        "^[a-zA-Z0-9\sáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ.;,!?\-']*$" => 'Lettres (min. & maj.) et chiffres',
        '(^(((0[1-9]|1[0-9]|2[0-8])[\/](0[1-9]|1[012]))|((29|30|31)[\/](0[13578]|1[02]))|((29|30)[\/](0[4,6,9]|11)))[\/](19|[2-9][0-9])\d\d$)|(^29[\/]02[\/](19|[2-9][0-9])(00|04|08|12|16|20|24|28|32|36|40|44|48|52|56|60|64|68|72|76|80|84|88|92|96)$)' => 'Format date (dd/mm/yyyy)'
    );
    
    /**
     * Tous les membres qui sont créés sont placés dans le groupe GLOBAL
     * @var string
     */
    const GROUPE_GLOBAL = 'GLOBAL';

    /**
     * Statut manager questionnaire
     *
     * @var string
     */
    const PROD = "prod";

    const DEMO = "demo";

    const EDIT = "edit";

    const DROITS = array(
        'ROLE_ADMIN' => 'Administratreur',
        'ROLE_BENEFICIAIRE_DIRECT' => 'Bénéficiaire Direct',
        'ROLE_BENEFICIAIRE' => 'Bénéficiaire',
        'ROLE_BENEVOLE' => 'Bénévole'
    );

    /**
     * Tableau des différentes tranche d'âge
     * @var array
     */
    const TRANCHE_AGE = array(
        0 => 'Tout âge',
        1 => '0-3 ans',
        2 => '4-7 ans',
        3 => '8-11 ans',
        4 => '12-17 ans',
        5 => '18-30 ans',
        6 => '31-64 ans',
        7 => '65 ans et +'
    );

    /**
     * Supprime une recherche de la session
     *
     * @Route("/utils/delete-search/{page}/{key}", name="utils_delete-search", defaults={"key"= null})
     */
    public function deleteSearch(Request $request, SessionInterface $session, int $page, $key = null)
    {
        $paramsSearch = $session->get(self::CURRENT_SEARCH);
        if (isset($paramsSearch[$key])) {
            unset($paramsSearch[$key]);
        }

        $this->pre($paramsSearch);

        $session->set(self::CURRENT_SEARCH, $paramsSearch);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Debug array
     *
     * @param mixed $data
     */
    public function pre($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

    /**
     * Url de la home du projet
     *
     * @return string
     */
    public function indexUrlProject()
    {
        return $this->generateUrl('index');
    }

    /**
     * Ajoute dans la session le champ field et order pour les tris
     *
     * @param SessionInterface $session
     * @param string $field
     * @param string $order
     */
    public function setDatasFilter(SessionInterface $session, $field, $order)
    {
        $session->set('current_field', $field);
        $session->set('current_order', $order);
    }

    /**
     * Renvoi un tableau contenant le champ field et order des tris
     *
     * @param SessionInterface $session
     * @return array [order] => l'ordre de tri
     *         [field] => champs à trier
     */
    public function getDatasFilter(SessionInterface $session)
    {
        $return = array();
        $return['order'] = $session->get('current_order');
        $return['field'] = $session->get('current_field');

        return $return;
    }

    /**
     * Fonction permettant la recherche à multiples filtres
     *
     * @param Request $request
     * @param SessionInterface $session
     * @param array $params
     *            [order] => l'ordre de tri
     *            [field] => champs à trier
     *            [repositoryClass] => repository (classe) concerné
     *            [repositoryMethode] => méthode à utiliser dans le repository
     *            [page] => indication pour la pagination
     *            [jointure] => tableau des jointures supplémentaires à faire
     *            [condition] => tableau contenant les filtres supplémentaires
     *            [ajax] => booléen, pour savoir si la requête est une requête ajax
     * @return array @return liste des résultats de la recherche
     */
    public function genericSearch(Request $request, SessionInterface $session, array $params)
    {
        $repository = $this->getDoctrine()->getRepository($params['repositoryClass']);

        $paramsSearch = $session->get(self::CURRENT_SEARCH);

        if (empty($paramsSearch)) {
            $paramsSearch = $request->request->all();
        } else {
            $paramsSearch = array_merge($paramsSearch, $request->request->all());
        }

        foreach ($paramsSearch as $key => $val) {
            $tab = explode('-', $key);
            if ($tab[0] != $params['repository']) {
                unset($paramsSearch[$key]);
            }
        }

        // Vérification si le champ de trie appartient bien à l'object
        $obj = new $params['repositoryClass']();
        $array_methode = get_class_methods($obj);
        $field = str_replace('_', '', $params['field']);
        $is_true = false;
        foreach ($array_methode as $methode) {
            $methode = str_replace('set', '', $methode);
            $methode = strtolower($methode);
            if ($methode == $field) {
                $is_true = true;
                break;
            }
        }

        if (! $is_true) {
            $params['field'] = 'id';
        }

        $paramsRepo = array(
            'field' => $params['field'],
            'order' => $params['order'],
            'repository' => $params['repository'],
            'search' => $paramsSearch
        );

        if (isset($params['condition'])) {
            $paramsRepo['condition'] = $params['condition'];
        }

        if (isset($params['jointure'])) {
            $paramsRepo['jointure'] = $params['jointure'];
        }

        $session->set(self::CURRENT_SEARCH, $paramsSearch);

        $max_nbr_result = (isset($params['ajax']) && $params['ajax'] ? self::MAX_NB_RESULT_AJAX : self::MAX_NB_RESULT);

        return $repository->{$params['repositoryMethode']}($params['page'], $max_nbr_result, $paramsRepo);
    }

    /**
     * Renvoie si l'utilisateur connecté a un role admin
     *
     * @return boolean
     */
    public function isAdmin()
    {
        foreach ($this->getUser()->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                return true;
            }
        }
        return false;
    }

    /**
     * Récupération des informations du membre connecté
     *
     * @return Membre
     */
    public function getMembre()
    {
        $repository = $this->getDoctrine()->getRepository(Membre::class);
        $membres = $repository->findById($this->getUser()
            ->getId());
        $membre = (isset($membres[0]) ? $membres[0] : new Membre());

        return $membre;
    }

    /**
     * Télécharge une image et retourne le nom de l'image téléchargée
     *
     * @param UploadedFile $file
     * @param string $type
     * @param string $nom
     * @param string $ancienne_image
     * @return string
     */
    public function telechargerImage(UploadedFile $file, string $type, string $nom, string $ancienne_image = null)
    {
        $type = strtolower($type);

        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension != 'jpeg' && $extension != 'jpg' && $extension != 'png') {
            return false;
        }

        $fileName = $this->cleanText($nom) . '_' . date('dmYHis') . '.' . $extension;

        $directory = $this->getParameter('pictures_directory') . $type;
        if (! file_exists($directory)) {
            mkdir($directory);
        }
        $file->move($directory, $fileName);

        $directory = str_replace("\\", "/", $directory);

        if (! is_null($ancienne_image) && ! preg_match("/^(http|https)/", $ancienne_image) && file_exists($directory . '/' . $ancienne_image)) {
            unlink($directory . '/' . $ancienne_image);
        }

        return $fileName;
    }

    /**
     * Retrait des accents et des caractères spéciaux
     *
     * @param string $str
     * @param string $encoding
     * @return string
     */
    public function cleanText(string $str, string $encoding = 'utf-8')
    {
        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $encoding);

        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

        // Remplacer les ligatures tel que : , Æ ...
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        // Supprimer tout le reste
        $str = preg_replace('#&[^;]+;#', '', $str);

        // retrait des caractères spéciaux
        $str = preg_replace("/(\\|\^|\.|\$|\||\(|\)|\[|\]|\*|\+|\?|\{|\}|\,|\=)/", '', $str);
        $str = preg_replace("/\d/", '_', $str);

        return $str;
    }

    /**
     * Supprime une image
     *
     * @Route("/utils/delete_image/{type}/{id}", name="image_delete")
     *
     * @param Request $request
     * @param string $type
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function suppressionImage(Request $request, string $type, int $id)
    {
        switch ($type) {
            case 'produit':
                $repository = $this->getDoctrine()->getRepository(Produit::class);
                $methodeGet = 'getImage';
                $methodeSet = 'setImage';
                break;
            case 'evenement':
                $repository = $this->getDoctrine()->getRepository(Evenement::class);
                $methodeGet = 'getImage';
                $methodeSet = 'setImage';
                break;
            case 'membre':
                $repository = $this->getDoctrine()->getRepository(Membre::class);
                $methodeGet = 'getAvatar';
                $methodeSet = 'setAvatar';
                break;
        }

        if (isset($repository)) {
            $objet = $repository->findOneBy(array('id' => $id));

            $image = $objet->{$methodeGet}();
            $directory = $this->getParameter('pictures_directory') . $type;

            $directory = str_replace("\\", "/", $directory);

            if (! preg_match("/^(http|https)/", $image) && file_exists($directory . '/' . $image)) {
                unlink($directory . '/' . $image);
            }
            $objet->{$methodeSet}(NULL);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($objet);

            $entityManager->flush();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true
            ));
        }
    }

    /**
     * Retourne le nombre total de produits envoyés dans les différents établissements et les spécialités
     *
     * @param Produit $produit
     * @return int
     */
    public function totalProduitsEnvoyes(Produit $produit)
    {
        $somme = 0;

        foreach ($produit->getProduitEtablissements() as $produitEtablissement) {
            $somme += $produitEtablissement->getQuantite();
        }

        foreach ($produit->getProduitSpecialites() as $produitSpecialite) {
            $somme += $produitSpecialite->getQuantite();
        }

        return $somme;
    }
}