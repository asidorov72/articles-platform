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

//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
//use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Knp\Component\Pager\PaginatorInterface;

class ArticlesController extends AbstractController
{
    /** @var ArticleRepository $articleRepository */
    private $articleRepository;
    protected $paginator;

    const ROWS_ON_PAGE = 10;

    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/articles/search", name="articles_search")
     */
    public function search(Request $request)
    {
        $error = '';
        $articles = [];

        $query = $request->query->get('q');

        if (strlen(trim($query)) === 0) {
            $error = 'Field is empty.';
        } else {
            $articles = $this->articleRepository->searchByQuery($query);
        }

        $articlesWithPages = $this->paginator->paginate(
            $articles, // Doctrine Query, nicht Ergebnisse
            $request->query->getInt('page', 1), self::ROWS_ON_PAGE);

        return $this->render('articles/query_article.html.twig', [
            'queryString' => $query,
            'articles' => $articlesWithPages,
            'error' => $error
        ]);
    }

    /**
     * @Route("/article/new", name="article_new")
     */
    public function add(Request $request, Slugify $slugify)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->slugify($article->getTitle()));
            $article->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('articles/article_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/articles", name="articles_list")
     */
    public function list(Request $request)
    {
        $articles = $this->articleRepository->findAll();

        $articlesWithPages = $this->paginator->paginate(
            $articles, // Doctrine Query, nicht Ergebnisse
            $request->query->getInt('page', 1), self::ROWS_ON_PAGE);

        return $this->render('articles/list.html.twig', [
            'articles' => $articlesWithPages
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article_show")
     */
    public function show(Article $article)
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
            'referrer' => $_SERVER['HTTP_REFERER'],
        ]);
    }

    /**
     * @Route("/article/{slug}/edit", name="article_edit")
     */
    public function edit(Article $article, Request $request, Slugify $slugify)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->slugify($article->getTitle()));
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('articles_list', [
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('articles/article_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{slug}/delete", name="article_delete")
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('articles_list');
    }
}
