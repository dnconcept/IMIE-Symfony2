<?php

namespace ArticleBundle\Controller;

use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\Comment;
use ArticleBundle\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
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

        $em = $this->getDoctrine()->getManager();
        $repoArticle = $em->getRepository("ArticleBundle:Article");
//        dump($article);

//        $article = $repoArticle->findWithComments($id);
//        dump($article);

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

    public function listAction(Request $request)
    {
        $articleRepository = $this->getRepository();

        //Exemple avec récupération paramètres dans l'URL
//        $description = $request->query->get("description");
//        $articles = $articleRepository->findArticleByDescription($description);


//  Une seule requête exécutée si on accède au commentaire dans la vue ...
//        $articles = $repo->findAllWithComments();

//  Beaucoup de requêtes exécutées si on accède au commentaire dans la vue ...
//        $articles = $repo->findAll();

        return $this->render("ArticleBundle:Default:list.html.twig", [
            "articles" => $articleRepository->findAllWithComments()
        ]);
    }

    public function addImageAction($id)
    {
        return new Response("Image ajouté avec succès sur l'article $id");
    }

    // ******************* CONTROLLER HELPERS *****************************//

    /**
     * @return ArticleRepository
     */
    protected function getRepository()
    {
        return $this
            ->getDoctrine()
            ->getRepository("ArticleBundle:Article");
    }

    /**
     * @param $id
     * @return Article|null|object
     */
    private function findArticleById($id)
    {
        return $this->getRepository()->find($id);
    }

    // ******************* FONCTION DE TESTS *****************************//

    private function requeteDQLPerso(EntityManager $em, $description)
    {
        if (null !== $description) {
            $query = $em->createQuery("SELECT art
        FROM ArticleBundle:Article art
        WHERE art.description LIKE ?10
        ORDER BY art.createdAt DESC
        ")
                ->setParameter(10, "%$description%");

//            $query = $em->createQuery("SELECT art
//        FROM ArticleBundle:Article art
//        WHERE art.description LIKE :description_param
//        ORDER BY art.createdAt DESC
//        ")
//                ->setParameter("description_param", "%$description%");

        } else {
            $query = $em->createQuery("SELECT art
        FROM ArticleBundle:Article art
        ORDER BY art.createdAt DESC
        ");
        }
    }

    private function addComments(EntityManager $em, Article $article)
    {
        $comments = $article->getComments();

        $comment1 = new Comment();
        $comment1
            ->setContent("Commentaire 1 sur article " . $article->getId())
            ->setArticle($article);

        $comment2 = new Comment();
        $comment2
            ->setContent("Commentaire 2 sur article " . $article->getId())
            ->setArticle($article);

        $comments[] = $comment1;
        $comments[] = $comment2;

        $em->persist($article);
        $em->flush();
    }

}
