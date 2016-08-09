<?php

namespace Domain\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DomainSiteBundle:Home:search.html.twig');
    }

    /**
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(string $slug)
    {
        $params = [
            'article' => $this->get('domain_article.manager.article')->getRepository()->findOneBy(['slug' => $slug]),
        ];

        return $this->render('DomainArticleBundle:Default:view.html.twig', $params);
    }
}
