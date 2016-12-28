<?php

namespace Domain\SiteBundle\Command;

use Domain\BusinessBundle\Util\SlugUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryMappingConvertCommand extends ContainerAwareCommand
{
    const CSV_DELIMITER = ',';

    const CATEGORY_CODE = 0;

    const CATEGORY_1_NAME_ES = 1;

    protected function configure()
    {
        $this->setName('data:category-mapping:convert');
        $this->setDescription('Convert csv categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getContainer()->get('kernel')->getRootDir() . '/../web/category-map.csv';
        $outputPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/category-map.txt';

        $handle = fopen($path, 'r');

        $categories = [];

        if ($handle !== false) {
            //skip first line
            $isFirst = true;

            while (($data = fgetcsv($handle, 1000, self::CSV_DELIMITER)) !== false) {

                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }

                $slug = SlugUtil::convertSlug($data[self::CATEGORY_1_NAME_ES]);
                $code = $data[self::CATEGORY_CODE];

                $categories[$code] = $slug;
            }

            fclose($handle);
        }

        file_put_contents($outputPath, var_export($categories, true));

        $output->writeln('Finish conversion ' . $outputPath);
    }
}
