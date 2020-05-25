<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     */
    public function single(Article $article)
    {
        return $this->render('article/single.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/create", name="create_article")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);
        // Загружает данные (аналог load()).
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            // Устанавливает значение свойств сущности (через сеттер т.к. все св-ва private)
            $article->setCreatedAt(new \DateTime('now'));
            $article->setUpdatedAt(new \DateTime('now'));

            // Entity Manager отвечает за все взаимодействия с БД.
            $em = $this->getDoctrine()->getManager();

            // Используется только для добавления новой записи в БД.
            $em->persist($article);
            // Сохраняет данные (save()).
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/update/{article}", name="update_article")
     * @param Request $request
     * @param Article $article
     * @return RedirectResponse|Response
     */
    public function update(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article, [
            'action' => $this->generateUrl('update_article', [
                'article' => $article->getId()
            ]),
            'method' => 'POST',
        ]);
        // Загружает данные (аналог load()).
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Entity Manager отвечает за все взаимодействия с БД.
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('article');
        }

        return $this->render('article/form.html.twig', [
            'form' => $form->createView(),
            'isUpdate' => true
        ]);
    }

    /**
     * @Route("/article/delete/{article}", name="delete_article")
     * @param Article $article
     * @return Response
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article');
    }
}
