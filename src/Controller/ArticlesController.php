<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArticlesController extends AbstractController
{
    /** @var ArticleRepository $articleRepository */
    private $articleRepository;

    private $doctrine;

    public function __construct(ArticleRepository $articleRepository, ManagerRegistry $doctrine)
    {
        $this->articleRepository = $articleRepository;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/articles/new", name="article_new")
     */
    public function addArticle(Request $request, Slugify $slugify)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->slugify($article->getTitle()));
            $article->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            //$em = $this->doctrine->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('articles/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/articles", name="articles_list")
     */
    public function articles()
    {
        $articles = $this->articleRepository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/articles/{slug}", name="article_show")
     */
    public function article(Article $article)
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }
}
