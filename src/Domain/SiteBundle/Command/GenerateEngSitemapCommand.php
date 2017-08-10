<?php

namespace Domain\SiteBundle\Command;

use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateEngSitemapCommand extends ContainerAwareCommand
{
    const BASE_DIRECTORY = '/../web/';
    const ENG_SITEMAP_DIRECTORY = 'sitemapEn/';

    protected $locale = LocaleHelper::LOCALE_EN;

    protected function configure()
    {
        $this->setName('data:generate:sitemap-en');
        $this->setDescription('Generate eng sitemap');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->generateSiteMap();
    }

    protected function generateSiteMap()
    {
        $path = $this->getBasePath();
        $engSiteMapPath = $path . self::ENG_SITEMAP_DIRECTORY;

        // remove old dir and create new one
        shell_exec('rm -rf ' . $engSiteMapPath);
        shell_exec('mkdir ' . $engSiteMapPath);

        /*
         * copy default sitemap files (spanish)
         *
         * Copy all files in folder that matches with pattern "sitemap.*" to folder for english sitemap.
         * Command doesn't change filenames
         */
        shell_exec(
            'cd ' . $path . ' && for file in sitemap.*; do cp "$file" ' . self::ENG_SITEMAP_DIRECTORY . '"${file}";done'
        );

        /*
         * Extract all files in folder that matches with pattern "sitemap.*.gz"
         */
        shell_exec('cd ' . $engSiteMapPath . ' && gunzip sitemap.*.gz');

        // replace host
        $host = $this->getBaseHost();
        $engHost = $this->locale . '.' . $host;

        /*
         * Search value "host" and replace all occurrences with "$engHost/sitemapEn" in "sitemap.xml" file.
         * "sitemapEn" is added because urls should point to correct files (not to default one)
         * Save result to same file.
         */
        shell_exec(
            'cd ' . $engSiteMapPath .
            ' && sed -i "s/' . $host . '/' . $engHost . '\/' . self::ENG_SITEMAP_DIRECTORY . 'g" sitemap.xml'
        );

        /*
         * For all files in folder that matches pattern "sitemap.*.xml",
         * search value "host" and replace all occurrences with "$engHost".
         * Save result to same files.
         */
        shell_exec(
            'cd ' . $engSiteMapPath .
            ' && for file in sitemap.*.xml; do sed -i "s/' . $host . '/' . $engHost . '/g" "${file}";done'
        );

        /*
         * Archive (gzip) all files in folder that matches with pattern "sitemap.*.xml".
         * Main file (sitemap.xml) shouldn't be archived.
         */
        shell_exec('cd ' . $engSiteMapPath . ' && gzip sitemap.*.xml');
    }

    /**
     * @return string
     */
    protected function getBaseHost()
    {
        return $this->getContainer()->getParameter('router.request_context.host');
    }

    /**
     * @return string
     */
    protected function getBasePath()
    {
        return $this->getContainer()->get('kernel')->getRootDir() . self::BASE_DIRECTORY;
    }
}
