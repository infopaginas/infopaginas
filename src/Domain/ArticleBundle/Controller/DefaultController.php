<?php

namespace Domain\ArticleBundle\Controller;

use Domain\ArticleBundle\Model\Manager\ArticleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $params = [
            'articles' => $this->getArticlesManager()->getPublishedArticles(),
        ];

        return $this->render('DomainArticleBundle:Default:index.html.twig', $params);
    }

    /**
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(string $slug)
    {
        $params = [
            'article' => $this->getArticlesManager()->getArticleBySlug($slug),
        ];

        return $this->render('DomainArticleBundle:Default:view.html.twig', $params);
    }

    /**
     * @return ArticleManager
     */
    private function getArticlesManager() : ArticleManager
    {
        return $this->get('domain_article.manager.article');
    }
}
