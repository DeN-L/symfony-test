<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/single/{article}", name="single_article")
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function single(Article $article)
    {
        return $this->render('articles/single.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/create", name="create_article")
     * @param Request $request
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        // Обрабатывает запрос (сабмитит форму).
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            // Устанавливает значение свойств сущности (через сеттер т.к. все св-ва private)
            $article->setCreatedAt(new \DateTime('now'));
            $article->setUpdatedAt(new \DateTime('now'));

            $em = $this->getDoctrine()->getManager();

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
