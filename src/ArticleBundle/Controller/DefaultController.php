<?php

namespace ArticleBundle\Controller;

use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\Comment;
use ArticleBundle\Form\ArticleType;
use ArticleBundle\Repository\ArticleRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{

    /**
     * Page d'accueil de notre bundle
     * @return Response
     */
    public function indexAction()
    {
        $serviceLocalFile = $this->container->get('article.local_file');
//        $serviceLocalFile = $this->get('article.local_file');
        $serviceLocalFile->store(["data" => "data_tostore"]);

        return $this->render('ArticleBundle:Default:index.html.twig', [
            "dateDuJour" => new \DateTime(),
            "categories" => [
                ["name" => "catégorie 1", "description" => "Description de la catégorie 1"],
                ["name" => "catégorie 2", "description" => "Description de la catégorie 2"],
            ],
        ]);
    }

    /**
     * Vue de détail de l'article
     * @param $id
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function detailAction($id, $slug)
    {
        if (null === $article = $this->findArticleById((int)$id)) {
            return $this->redirectToRoute("article_list");
//            throw $this->createNotFoundException();
        }
        return $this->render('ArticleBundle:Default:detail.html.twig', [
            "article" => $article
        ]);
    }

    /**
     * Ajout d'un article  [GET ou POST]
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->add("Ajouter", "submit", ["attr" => ["class" => "btn btn-primary"]]);

        //Démonstration du service validator permettant de valider une instance de la classe article
        $validator = $this->get("validator");
        $list = $validator->validate($article);
        dump($list);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            $flashMessage = "L'article a bien été ajouté !";
            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('info', $flashMessage);
            return $this->redirectToRoute("article_detail", [
                "id"   => $article->getId(),
                "slug" => $article->getSlug()
            ]);
        }
        return $this->render("ArticleBundle:Default:add.html.twig", [
            "formulaire" => $form->createView()
        ]);
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id, Request $request)
    {
//        $user = $this->getUser();
//        var_dump($user);
//        die;
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

    // Accessible seulement en GET
    public function updateAction($id, Request $request)
    {
        $article = $this->findArticleById($id);
        $form = $this->createFormForArticle($article);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            /** @var Session $session */
            $session = $request->getSession();
            $session->getFlashBag()->add('info', "L'article a bien été modifié !");

            return $this->redirectToRoute("article_list");
        }
        return $this->render("ArticleBundle:Default:update.html.twig", [
            "formulaire" => $form->createView(),
            "article_id" => $article->getId()
        ]);
    }

    /**
     * Création du formulaire de l'article à partir du form builder
     * @param Article $article
     * @return Form
     */
    private function createFormForArticle(Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->add("update", "submit", ["label" => "Mettre à jour", "attr" => ["class" => "btn btn-primary"]]);
        return $form;
    }

    // Accessible seulement en POST
    public function updatePostAction($id, Request $request)
    {
        $article = $this->findArticleById($id);
        $form = $this->createFormForArticle($article);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $articleUpdated = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($articleUpdated);
            $em->flush();
            return $this->redirectToRoute("article_detail", [
                "id"   => $article->getId(),
                "slug" => $article->getSlug(),
            ]);
        }
        return $this->forward("ArticleBundle:Default:update", [
            "id"   => $article->getId(),
            "slug" => $article->getSlug(),
        ]);
    }

    public function listAction(Request $request)
    {
        $articleRepository = $this->getRepository();

        //Exemple avec récupération des articles paramètres dans l'URL
        //On récupére tous les articles contenant dans leur description l'expression $description
        //$description est récupéré à partir des paramètres passé dans l'url (?description=test_description)
//        $description = $request->query->get("description");
//        $articles = $articleRepository->findArticleByDescription($description);


//  Une seule requête exécutée si on accède au commentaire dans la vue ...
//        $articles = $articleRepository->findAllWithComments();

//  Beaucoup de requêtes exécutées si on accède au commentaire dans la vue ...
//        $articles = $articleRepository->findAll();

        return $this->render("ArticleBundle:Default:list.html.twig", [
            "articles" => $articleRepository->findAllWithComments()
        ]);
    }

    /**
     * Permet d'ajouter une image à l'article avec id = $id
     * @param $id int   Id de l'article
     * @return Response
     */
    public function addImageAction($id)
    {
        //Equivalent à $this->getRepository()->find($id)
//        $article = $this->getRepository()->findOneBy([
//            "id" => $id
//        ]);

        //Récuration de l'article
        /** @var Article $article */
        $article = $this->getRepository()->find($id);


        //Création d'une instance d'Image
        $image = new \ArticleBundle\Entity\Image();
        $image
            ->setUrl("http://www.beneteau.fr/var/beneteau/storage/images/media/images/first-20/first-20/3020700-1-fre-FR/First-20.jpg")
            ->setTitle("First 210");

        //On affecte l'image à l'article, le setter de l'article s'occupe de définir l'article de l'image !
        $article->setImage($image);

        //Enregistrement en base de données
        $em = $this->getDoctrine()->getManager();
//        $em->persist($image);  // Pas besoin de persister l'image si on a défini l'optoin cascade={"persist"} dans le mapping (annotations)
        $em->persist($article);
        $em->flush();

        return new Response("Image ajoutée avec succès sur l'article $id");
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
     * @return Article|null
     */
    private function findArticleById($id)
    {
        return $this->getRepository()->find($id);
    }

    // ******************* FONCTION DE TESTS & DEMONSTRATION *****************************//

    /**
     * @param EntityManager $em
     * @param $description
     * @return \Doctrine\ORM\AbstractQuery|\Doctrine\ORM\Query
     */
    private function requeteDQLPerso(EntityManager $em, $description)
    {
        if (null !== $description) {
            $query = $em->createQuery("SELECT art
        FROM ArticleBundle:Article art
        WHERE art.description LIKE ?10
        ORDER BY art.createdAt DESC
        ")
                ->setParameter(10, "%$description%");

            //Autre manière de "binder" les paramètres de notre requète DQL
            $query = $em->createQuery("SELECT art
        FROM ArticleBundle:Article art
        WHERE art.description LIKE :description_param
        ORDER BY art.createdAt DESC
        ")
                ->setParameter("description_param", "%$description%");

        } else {
            $query = $em->createQuery("SELECT art
        FROM ArticleBundle:Article art
        ORDER BY art.createdAt DESC
        ");
        }
        return $query;
    }

    /**
     * @param EntityManager $em
     * @param Article $article
     */
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

    /**
     * @return Form
     */
    private function createFormForArticleWithFormBuilder(Article $article)
    {
        $formBuilder = $this->createFormBuilder($article);
        $formBuilder
            ->add("title", "text")
            ->add("description", "textarea")
            ->add("content", "textarea")
            ->add("createdAt", "datetime")
            ->add("Ajouter", "submit")
            ->add("Reset", "reset")
            ->setAction($this->generateUrl("article_update_post", ["id" => $article->getId()]));
        /** @var Form $form */
        return $formBuilder->getForm();
    }

}
