<?php

namespace Domain\SiteBundle\Command;

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

    protected $locale = 'en';

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

        // copy esp sitemap
        shell_exec(
            'cd ' . $path . ' && for file in sitemap.*; do cp "$file" ' . self::ENG_SITEMAP_DIRECTORY . '"${file}";done'
        );

        // unzip files
        shell_exec('cd ' . $engSiteMapPath . ' && gunzip sitemap.*.gz');

        // replace host
        $host = $this->getBaseHost();
        $engHost = $this->locale . '.' . $host;

        // replace host in main file
        shell_exec(
            'cd ' . $engSiteMapPath .
            ' && sed -i "s/' . $host . '/' . $engHost . '\/' . self::ENG_SITEMAP_DIRECTORY . 'g" sitemap.xml'
        );

        // replace host in child files
        shell_exec(
            'cd ' . $engSiteMapPath .
            ' && for file in sitemap.*.xml; do sed -i "s/' . $host . '/' . $engHost . '/g" "${file}";done'
        );

        // gzip child files
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
