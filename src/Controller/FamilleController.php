<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Famille;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\FamilleType;
use App\Entity\Patient;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FamilleController extends AppController
{

    /**
     * Listing des familles
     *
     * @Route("/famille/listing/{page}/{field}/{order}", name="famille_listing", defaults={"page" = 1, "field"= null, "order"= null})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     * 
     * @param Request $request
     * @param SessionInterface $session
     * @param int $page
     * @param string $field
     * @param string $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, SessionInterface $session, int $page = 1, $field = null, $order = null)
    {
        if (is_null($field)) {
            $field = 'id';
        }

        if (is_null($order)) {
            $order = 'DESC';
        }

        $params = array(
            'field' => $field,
            'order' => $order,
            'page' => $page,
            'repositoryClass' => Famille::class,
            'repository' => 'Famille',
            'repositoryMethode' => 'findAllFamilles'
        );

        foreach ($this->getUser()->getRoles() as $role) {
            if ($role == "ROLE_ADMIN") {
                $params['sans_inactif'] = false;
                break;
            }
        }

        $result = $this->genericSearch($request, $session, $params);

        $pagination = array(
            'page' => $page,
            'route' => 'famille_listing',
            'pages_count' => ceil($result['nb'] / self::MAX_NB_RESULT),
            'nb_elements' => $result['nb'],
            'route_params' => array()
        );

        $this->setDatasFilter($session, $field, $order);

        return $this->render('famille/index.html.twig', [
            'controller_name' => 'FamilleController',
            'familles' => $result['paginator'],
            'pagination' => $pagination,
            'current_order' => $order,
            'current_field' => $field,
            'current_search' => $session->get(self::CURRENT_SEARCH),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'active' => "Liste des familles"
            )
        ]);
    }

    /**
     * Affichage de la fiche d'une famille
     *
     * @Route("/famille/see/{id}/{page}", name="famille_see")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function seeAction(SessionInterface $session, Famille $famille, int $page)
    {
        $arrayFilters = $this->getDatasFilter($session);

        return $this->render('famille/see.html.twig', [
            'page' => $page,
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Fiche d'une famille"
            )
        ]);
    }

    /**
     * Ajout d'une famille
     *
     * @Route("/famille/add/{page}", name="famille_add")
     * @Route("/famille/ajax/add/{id}", name="famille_ajax_add")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(SessionInterface $session, Request $request, int $page = 1, int $id = null)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $famille = new Famille();

        if ($request->isXmlHttpRequest()) {
            $repository = $this->getDoctrine()->getRepository(Patient::class);
            $result = $repository->findById($id);
            $patient = $result[0];
            $famille->setPatient($patient);

            $form = $this->createForm(FamilleType::class, $famille, array(
                'label_submit' => 'Ajouter',
                'disabled_patient' => true,
                'label_adresse' => ' ',
                'allow_extra_fields' => true
            ));
        } else {

            $form = $this->createForm(FamilleType::class, $famille, array(
                'label_submit' => 'Ajouter'
            ));
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $famille->setDisabled(0);
            $famille->getFamilleAdresse()->setDisabled(0);

            $em = $this->getDoctrine()->getManager();

            $em->persist($famille);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            } else {
                return $this->redirect($this->generateUrl('famille_listing'));
            }
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            $encoders = array(
                new XmlEncoder(),
                new JsonEncoder()
            );
            $normalizers = array(
                new ObjectNormalizer()
            );

            $serializer = new Serializer($normalizers, $encoders);

            $jsonPatient = $serializer->normalize($patient, null, array(
                'attributes' => array(
                    'nom',
                    'familles' => array(
                        'id',
                        'nom',
                        'prenom',
                        'familleAdresse' => array(
                            'voie',
                            'numeroVoie',
                            'ville',
                            'codePostal'
                        )
                    )
                )
            ));

            // on récupère la valeur du select adresse qui n'est pas conservé en cas d'erreur
            $resultForm = $request->request->all();
            if(!empty($resultForm))
            {
                $select_famille = $resultForm['famille']['adresse_select'];
            }
            else
            {
                $select_famille = -1;
            }
            
            return $this->render('famille/ajax_add.html.twig', array(
                'form' => $form->createView(),
                'patient' => $patient,
                'jsonPatient' => $jsonPatient,
                'select_famille' => $select_famille
            ));
        }
        
        return $this->render('famille/add.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => "Gestion de familles"
                ),
                'active' => "Ajout d'une famille"
            )
        ]);
    }

    /**
     * Edition d'une famille
     *
     * @Route("/famille/edit/{id}/{page}", name="famille_edit")
     * @Route("/famille/ajax/edit/{id}", name="famille_ajax_edit")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_BENEFICIAIRE') or is_granted('ROLE_BENEFICIAIRE_DIRECT')")
     *
     * @param SessionInterface $session
     * @param Request $request
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(SessionInterface $session, Request $request, Famille $famille, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        $form = $this->createForm(FamilleType::class, $famille, array(
            'label_submit' => 'Modifier',
            'disabled_patient' => true
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($famille);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(array(
                    'statut' => true
                ));
            }
            return $this->redirect($this->generateUrl('famille_listing', array(
                'page' => $page,
                'field' => $arrayFilters['field'],
                'order' => $arrayFilters['order']
            )));
        }

        // Si appel Ajax, on renvoi sur la page ajax
        if ($request->isXmlHttpRequest()) {

            return $this->render('famille/ajax_edit.html.twig', [
                'form' => $form->createView(),
                'famille' => $famille
            ]);
        }

        return $this->render('famille/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'famille' => $famille,
            'paths' => array(
                'home' => $this->indexUrlProject(),
                'urls' => array(
                    $this->generateUrl('famille_listing', array(
                        'page' => $page,
                        'field' => $arrayFilters['field'],
                        'order' => $arrayFilters['order']
                    )) => 'Gestion de familles'
                ),
                'active' => 'Edition de #' . $famille->getId() . ' - ' . $famille->getPrenom() . ' ' . $famille->getNom()
            )
        ]);
    }

    /**
     * Désactivation d'une famille
     *
     * @Route("/famille/delete/{id}/{page}", name="famille_delete")
     * @ParamConverter("famille", options={"mapping": {"id": "id"}})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param SessionInterface $session
     * @param Famille $famille
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, SessionInterface $session, Famille $famille, int $page = 1)
    {
        $arrayFilters = $this->getDatasFilter($session);

        if ($famille->getDisabled() == 1) {
            $famille->setDisabled(0);
        } else {
            $famille->setDisabled(1);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($famille);

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json(array(
                'statut' => true
            ));
        }
        
        return $this->redirectToRoute('famille_listing', array(
            'page' => $page,
            'field' => $arrayFilters['field'],
            'order' => $arrayFilters['order']
        ));
    }
}
