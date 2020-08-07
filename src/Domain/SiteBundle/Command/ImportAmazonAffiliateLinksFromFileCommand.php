<?php

namespace Domain\SiteBundle\Command;

use Doctrine\ORM\EntityNotFoundException;
use Domain\BusinessBundle\Entity\AmazonAffiliateItem;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Util\CategoryUtil;
use Domain\BusinessBundle\VO\Url;
use League\Flysystem\FileExistsException;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportAmazonAffiliateLinksFromFileCommand extends ContainerAwareCommand
{
    private const CATEGORY_NAME_DELIMITER = '/';

    private $output;

    protected function configure()
    {
        $this->setName('data:import-affiliate-links');
        $this->setDescription('Import Amazonf affiliate links from the file');
        $this->addArgument('path', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $path = $input->getArgument('path');

        if (!file_exists($path)) {
            throw new FileExistsException('Cannot read the file');
        }

        $data = $this->getFileData($path);

        $this->importData($data);
    }

    private function getFileData(string $path): array
    {
        $data = [];

        $lines = file($path, FILE_SKIP_EMPTY_LINES);
        $headers = str_getcsv(array_shift($lines));

        foreach ($lines as $lineNumber => $line) {
            if (mb_detect_encoding($line, CategoryUtil::ENCODING_ISO_8859_1)) {
                $line = mb_convert_encoding($line, CategoryUtil::ENCODING_UTF8, CategoryUtil::ENCODING_ISO_8859_1);
            }

            $entry = str_getcsv($line);

            foreach ($headers as $i => $header) {
                $data[$lineNumber][$header] = $entry[$i];
            }
        }

        return $data;
    }

    private function importData($data)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        foreach ($data as $row) {
            $categoryName = trim(explode(self::CATEGORY_NAME_DELIMITER, $row['Category'])[0]);
            /** @var Category $category */
            $category = $em->getRepository(Category::class)
                ->getCategoryByCaseInsensitiveName([AdminHelper::convertAccentedString($categoryName)]);

            if ($category) {
                $url = new Url();
                $url->setRelNoFollow(false);
                $url->setRelNoOpener(false);
                $url->setRelNoReferrer(false);
                $url->setUrl($row['Amazon Affiliate Category Link']);
                $category->setAmazonAffiliateUrl($url);

                for ($i = 1; $i <= Category::MAX_AMAZON_AFFILIATE_ITEMS_COUNT; $i++) {
                    $af = new AmazonAffiliateItem();
                    $af->setCategory($category);
                    $af->setEmbeddedHTML($row['Amazon Affiliate Link ' . $i]);
                    $em->persist($af);
                }

                $em->flush();
                $em->clear();
            } else {
                $this->output->writeln('Category "' . $categoryName . '" not found');
            }
        }
    }
}
