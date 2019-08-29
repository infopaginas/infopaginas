<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Domain\ArticleBundle\Entity\Article;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UrlUpdateArticleCkEditorCommand extends ContainerAwareCommand
{
    const REG_EXP_LINK = '/<a\s[^>]*>(.*)<\/a>/siU';
    const REG_EXP_REL = '/rel=\"([^\']*?)\"/siU';
    const REL_VALUE = ' rel="nofollow noreferrer noopener" ';
    const REG_EXP_LINK_START = '/<a\s[^>]*>/siU';

    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    protected $items = 0;

    protected $total = 0;

    protected function configure()
    {
        // this command is needed only to migrate urls see https://jira.oxagile.com/browse/INFT-3132
        // and should be removed after it
        $this->setName('data:url-article-ckeditor:update');
        $this->setDescription('Update article ckeditor urls');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $output->writeln('Started...');

        $this->updateUrls();

        $output->writeln(sprintf('Updated link: %s', $this->items));
        $output->writeln(sprintf('Total articles processed: %s', $this->total));
        $output->writeln('Done');
    }

    protected function updateUrls()
    {
        $articles = $this->getInternalArticles();
        $batchSize = 20;
        $i = 0;

        $fields = [
            'body',
        ];

        foreach ($articles as $row) {
            /* @var Article $article */
            $article = $row[0];
            $data = [];

            foreach ($fields as $field) {
                foreach (array_keys(LocaleHelper::getLocaleList()) as $locale) {
                    $key = $field . LocaleHelper::getLangPostfix($locale);
                    $value = $article->getTranslation($field, $locale);

                    $data[$key] = $this->processText($value);
                }
            }

            foreach ($fields as $field) {
                $this->handleTranslations($article, $field, $data);
            }

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }

            $i ++;

            $this->total++;
        }

        $this->em->flush();
    }

    private function processText($text)
    {
        $result = $text;

        if (preg_match_all($this::REG_EXP_LINK, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $item) {
                if (!empty($item[0])) {
                    $link = $this->processLink($item[0]);

                    $result = str_replace($item[0], $link, $result);
                }
            }
        }

        return $result;
    }

    private function processLink($link)
    {
        if (preg_match_all($this::REG_EXP_REL, $link, $matches, PREG_SET_ORDER)) {
            $item = end($matches);

            if (!empty($item[0])) {
                $this->items++;

                return str_replace($item[0], $this::REL_VALUE, $link);
            }
        }

        return $this->insertRelAttr($link);
    }

    private function insertRelAttr($link)
    {
        if (preg_match_all($this::REG_EXP_LINK_START, $link, $matches, PREG_SET_ORDER)) {
            $item = current($matches);

            if (!empty($item[0])) {
                $this->items++;
                $linkStart = substr_replace($item[0], $this::REL_VALUE, -1, 0) ;

                return str_replace($item[0], $linkStart, $link);
            }
        }

        return $link;
    }

    /**
     * @return IterableResult
     */
    private function getInternalArticles()
    {
        $qb = $this->em->getRepository(Article::class)->createQueryBuilder('a');
        $qb->andWhere($qb->expr()->isNull('a.externalId'));

        $query = $this->em->createQuery($qb->getDQL());

        $iterateResult = $query->iterate();

        return $iterateResult;
    }

    /**
     * @param Article $item
     * @param string  $property
     * @param array   $data
     *
     * @return Article
     */
    private function handleTranslations(Article $item, $property, $data)
    {
        if (property_exists($item, $property)) {
            $accessor = PropertyAccess::createPropertyAccessor();

            $defaultValue = null;

            foreach (LocaleHelper::getLocaleList() as $locale => $name) {
                $propertyLocale = $property . LocaleHelper::getLangPostfix($locale);

                if (!empty($data[$propertyLocale])) {
                    $value = trim($data[$propertyLocale]);

                    if (!$defaultValue) {
                        $defaultValue = $value;
                    }

                    $this->addTranslation($item, $property, $value, $locale);
                } else {
                    $value = null;
                }

                if (property_exists($item, $propertyLocale)) {
                    $accessor->setValue($item, $propertyLocale, $value);
                }
            }

            $accessor->setValue($item, $property, $defaultValue);
        }

        return $item;
    }

    /**
     * @param Article $item
     * @param string  $property
     * @param string  $data
     * @param string  $locale
     *
     * @return Article
     */
    private function addTranslation(Article $item, $property, $data, $locale)
    {
        $translation = $item->getTranslationItem($property, $locale);

        if ($translation) {
            $translation->setContent($data);
        }

        return $item;
    }
}
