<?php

namespace Domain\SiteBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CategoryConvertCommand extends ContainerAwareCommand
{
    const CSV_DELIMITER = ',';

    const CATEGORY_CODE = 0;

    const CATEGORY_1_NAME_ES = 1;
    const CATEGORY_2_NAME_ES = 2;
    const CATEGORY_3_NAME_ES = 3;

    const CATEGORY_1_NAME_EN = 4;
    const CATEGORY_2_NAME_EN = 5;
    const CATEGORY_3_NAME_EN = 6;

    protected function configure()
    {
        $this->setName('data:category:convert');
        $this->setDescription('Convert csv categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getContainer()->get('kernel')->getRootDir() . '/../web/category-import.csv';
        $outputPath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/category-import.txt';

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

                if (!empty($categories[$data[self::CATEGORY_1_NAME_ES]])) {
                    //update lvl 1
                    $children1 = $categories[$data[self::CATEGORY_1_NAME_ES]]['children'];

                    if (!empty($children1[$data[self::CATEGORY_2_NAME_ES]])) {
                        //update lvl 2
                        $children2 = $children1[$data[self::CATEGORY_2_NAME_ES]]['children'];

                        $children2[$data[self::CATEGORY_3_NAME_ES]] = [
                            'es' => $data[self::CATEGORY_3_NAME_ES],
                            'en' => $data[self::CATEGORY_3_NAME_EN],
                            'code' => $data[self::CATEGORY_CODE],
                        ];

                        $children1[$data[self::CATEGORY_2_NAME_ES]]['children'] = $children2;

                    } else {
                        //add lvl 2
                        $children1[$data[self::CATEGORY_2_NAME_ES]] = [
                            'es' => $data[self::CATEGORY_2_NAME_ES],
                            'en' => $data[self::CATEGORY_2_NAME_EN],
                            'children' => [
                                $data[self::CATEGORY_3_NAME_ES] => [
                                    'es' => $data[self::CATEGORY_3_NAME_ES],
                                    'en' => $data[self::CATEGORY_3_NAME_EN],
                                    'code' => $data[self::CATEGORY_CODE],
                                ],
                            ],
                        ];
                    }

                    $categories[$data[self::CATEGORY_1_NAME_ES]]['children'] = $children1;


                } else {
                    //add new lvl 1
                    $categories[$data[self::CATEGORY_1_NAME_ES]] = [
                        'es' => $data[self::CATEGORY_1_NAME_ES],
                        'en' => $data[self::CATEGORY_1_NAME_EN],
                        'children' => [
                            $data[self::CATEGORY_2_NAME_ES] => [
                                'es' => $data[self::CATEGORY_2_NAME_ES],
                                'en' => $data[self::CATEGORY_2_NAME_EN],
                                'children' => [
                                    $data[self::CATEGORY_3_NAME_ES] => [
                                        'es' => $data[self::CATEGORY_3_NAME_ES],
                                        'en' => $data[self::CATEGORY_3_NAME_EN],
                                        'code' => $data[self::CATEGORY_CODE],
                                    ],
                                ],
                            ],
                        ],
                    ];

                }
            }

            fclose($handle);
        }

        file_put_contents($outputPath, var_export($categories, true));

        $output->writeln('Finish conversion ' . $outputPath);
    }
}
