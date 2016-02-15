<?php

namespace ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction()
    {

//        return $this->forward("AppBundle:Default:test");
//        return $this->redirect($url_for_article1);
//        return $this->redirectToRoute("homepage");

        return $this->render('ArticleBundle:Default:index.html.twig', [
            "dateDuJour"  => new \DateTime(),
            "contenuHtml" => "<a href='test'>Test</a>",
            "variable1"   => "Valeur de la variable 1",
            "items"       => ["item 1", "item 2", "item 3"],
            "categories"  => [
                ["name" => "catégorie 1", "description" => "Description de la catégorie 1"],
                ["name" => "catégorie 2", "description" => "Description de la catégorie 2"],
            ],
        ]);
    }

    public function detailAction($id, $slug, Request $request)
    {
        $id = (int)$id;
        $article = null;
        if ($article == null) {
            throw $this->createNotFoundException();
        }
        return $this->render('ArticleBundle:Default:detail.html.twig', [
            "slug" => $slug,
            "id"   => $id
        ]);
    }

    public function addAction()
    {
        $content = $this->renderView("ArticleBundle:Default:add.html.twig");
        return new Response($content);
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

    public function listAction()
    {
        $lorem = "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Ndi tempora incidum exercitationem ulla";
        $articles = [];
        for ($i = 0; $i < 10; $i++) {
            $articles[] = [
                "id"          => 1,
                "title"       => "Titre $i",
                "description" => "Description article $i",
                "content"     => $lorem,
                "created_at"  => new \DateTime(),
                "created_by"  => "Créateur $i"
            ];
        }
        return $this->render("ArticleBundle:Default:list.html.twig", [
            "articles" => $articles
        ]);
    }

}
