<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UrlUpdateBusinessCkEditorCommand extends ContainerAwareCommand
{
    const REG_EXP_LINK = '/<a\s[^>]*>(.*)<\/a>/siU';
    const REG_EXP_REL = '/rel=\"([^\']*?)\"/siU';
    const REL_VALUE = ' rel="nofollow noreferrer noopener" ';
    const REG_EXP_LINK_START = '/<a\s[^>]*>/siU';

    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    protected $items   = 0;

    protected $total = 0;

    protected function configure()
    {
        // this command is needed only to migrate urls see https://jira.oxagile.com/browse/INFT-3132
        // and should be removed after it
        $this->setName('data:url-business-ckeditor:update');
        $this->setDescription('Update business ckeditor urls');
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
        $output->writeln(sprintf('Total businesses processed: %s', $this->total));
        $output->writeln('Done');
    }

    protected function updateUrls()
    {
        $businesses = $this->em->getRepository(BusinessProfile::class)->getAllBusinessProfilesIterator();
        $batchSize = 20;
        $i = 0;

        $fields = [
            BusinessProfile::BUSINESS_PROFILE_FIELD_DESCRIPTION,
        ];

        foreach ($businesses as $row) {
            /* @var BusinessProfile $business */
            $business = $row[0];
            $data = [];

            foreach ($fields as $field) {
                foreach (array_keys(LocaleHelper::getLocaleList()) as $locale) {
                    $key = $field . LocaleHelper::getLangPostfix($locale);
                    $value = $business->getTranslation($field, $locale);

                    $data[$key] = $this->processText($value);
                }
            }

            foreach ($fields as $field) {
                LocaleHelper::handleTranslations($business, $field, $data);
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
}
