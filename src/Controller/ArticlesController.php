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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;

use Knp\Component\Pager\PaginatorInterface;
//use Symfony\Component\Validator\Constraints\Image;

class ArticlesController extends AbstractController
{
    const ROWS_ON_PAGE = 10;

    /**
     * @Route("/articles/search", name="articles_search")
     */
    public function search(
        Request $request,
        PaginatorInterface $paginator,
        ArticleRepository $articleRepository
    )
    {
        $error = '';
        $articles = [];

        $query = $request->query->get('q');

        if (strlen(trim($query)) === 0) {
            $error = 'Field is empty.';
        } else {
            $articles = $articleRepository->searchByQuery($query);
        }

        $articlesWithPages = $paginator->paginate(
            $articles, // Doctrine Query, nicht Ergebnisse
            $request->query->getInt('page', 1), self::ROWS_ON_PAGE);

        return $this->render('articles/query.html.twig', [
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

        //die('hhhhhhhhhhhh');

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->slugify($article->getTitle()));
            $article->setCreatedAt(new \DateTime());

            $file = $article->getImage();

//            var_dump($file);
//            die();

            if ($file instanceof File && !empty($file)) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_dir'), $fileName);
                $article->setImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles_list');
        }

        return $this->render('articles/form.html.twig', [
            'form' => $form->createView(),
            'referrer' => $_SERVER['HTTP_REFERER']
        ]);
    }

    /**
     * @Route("/articles", name="articles_list")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        ArticleRepository $articleRepository
    )
    {
        $articles = $articleRepository->findAll();

        $articlesWithPages = $paginator->paginate(
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
        if (!empty($file = $article->getImage())) {
            $article->setImage(new File($this->getParameter('upload_dir').'/'. $file, 1));
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->slugify($article->getTitle()));
            $article->setUpdatedAt(new \DateTime());

            $newFile = $article->getImage();

            if ($newFile instanceof File && !empty($newFile)) {
                $fileName = md5(uniqid()) . '.' . $newFile->guessExtension();
                $newFile->move($this->getParameter('upload_dir'), $fileName);
                $article->setImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('articles_list', [
                'slug' => $article->getSlug()
            ]);
        }

        $ref = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

        return $this->render('articles/form.html.twig', [
            'form' => $form->createView(),
            'imageFile' => (!empty($file) ? $file : ''),
            'referrer' => $ref,
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
