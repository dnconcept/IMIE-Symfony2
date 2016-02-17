<?php
/**
 * Created by PhpStorm.
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 */

namespace ArticleBundle\Controller;


/**
 * Class TestController
 * pour tests et démonstration
 * @author Nicolas Desprez <nicolas.desprez@dnconcept.fr>
 * @package ArticleBundle\Controller
 */
class TestController
{

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

}