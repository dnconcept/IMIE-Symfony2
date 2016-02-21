<?php
/**
 * Created by PhpStorm.
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 */

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class TestController
 * pour tests et démonstration
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 * @package ArticleBundle\Controller
 */
class TestController extends Controller
{

    public function helloAction()
    {
//        $authorizationChecker = $this->container->get("security.authorization_checker");
        $authorizationChecker = $this->get("security.authorization_checker");
        if ($authorizationChecker->isGranted("ROLE_SUPER_ADMIN")) {
            return new Response("hello Admin !");
        }
        throw new AccessDeniedException();
//        return new Response("hello !");
    }


    public function injectionRequestAction(Request $request)
    {
        // $_GET
        $query = $request->query;
        //$_POST
        $post = $request->request;

        if ($request->getMethod() == "POST") {

        } elseif ($request->isXmlHttpRequest()) {

        }
    }

    public function forwardAction()
    {
        return $this->forward("AppBundle:Default:test");
    }

    public function redirectAction()
    {
        return $this->redirect($this->generateUrl("homepage"));
    }

    public function redirectToRouteAction()
    {
        return $this->redirectToRoute("homepage");
    }


    public function arrayResultAction()
    {
//        $repo = $this->getDoctrine()
//            ->getRepository('ArticleBundle:Article');
//        $query = $repo->createQueryBuilder('a')
//            ->orderBy('a.title', 'ASC')
//            ->getQuery();
//        $produits = $query->getArrayResult();
//        dump($produits);
    }

}