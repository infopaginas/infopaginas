<?php

namespace Domain\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $params = [
            'articles' => $this->get('domain_article.manager.article')->getArticles(),
        ];

        return $this->render('DomainArticleBundle:Default:index.html.twig', $params);
    }

    public function viewAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }
}
