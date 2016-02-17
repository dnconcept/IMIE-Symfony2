<?php

namespace ArticleBundle\Controller;

use ArticleBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        return $this->render('ArticleBundle:Default:index.html.twig', [
            "dateDuJour" => new \DateTime(),
            "categories" => [
                ["name" => "catégorie 1", "description" => "Description de la catégorie 1"],
                ["name" => "catégorie 2", "description" => "Description de la catégorie 2"],
            ],
        ]);
    }

    public function detailAction($id, $slug, Request $request)
    {
        if (null === $article = $this->findArticleById((int)$id)) {
            return $this->redirectToRoute("article_list");
            throw $this->createNotFoundException();
        }
        return $this->render('ArticleBundle:Default:detail.html.twig', [
            "article" => $article
        ]);
    }

    public function addAction(Request $request)
    {
        $article = new Article();
        $article
            ->setTitle("Titre 3")
            ->setDescription("Description 2")
            ->setContent("Contenu 3")
            ->setCreatedBy("creator 3");

        $em = $this->getDoctrine()->getManager();
        try {
            $em->persist($article);
            $em->flush();
            $flashMessage = "L'article a bien été ajouté !";
        } catch (\Exception $e) {
            $flashMessage = "Problème de base de données !";
        }

        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('info', $flashMessage);

        return $this->redirectToRoute("article_detail", [
            "id"   => $article->getId(),
            "slug" => $article->getSlug()
        ]);

        return $this->render("ArticleBundle:Default:add.html.twig");
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (null === $article = $this->findArticleById($id)) {
            throw $this->createNotFoundException();
        }
        $em->remove($article);
        $em->flush();

        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('info', "L'article id = $id a bien été supprimé ...");

        return $this->redirectToRoute("article_list");
    }

    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $this->findArticleById($id);

        $article->setTitle("Nouveau titre qu'on veut !");

        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute("article_list");
    }

    /**
     * @param $id
     * @return Article|null|object
     */
    private function findArticleById($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoArticle = $em->getRepository("ArticleBundle:Article");
        return $repoArticle->find($id);
    }

    public function listAction()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository("ArticleBundle:Article")
            ->findAll();

        return $this->render("ArticleBundle:Default:list.html.twig", [
            "articles" => $articles
        ]);
    }

}
